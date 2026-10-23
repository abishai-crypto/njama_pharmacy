<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Product;
use App\Models\InventoryBatch;
use App\Models\InventoryMovement;
use App\Models\Sale;
use App\Models\CashierShift;

class PosFefoSaleTest extends TestCase
{
    public function test_pos_search_returns_products(): void
    {
        $cashier = User::where('username', 'cashier')->first();

        $response = $this->actingAs($cashier)->getJson('/pos/search?q=Paracetamol');
        $response->assertStatus(200);
        $data = $response->json();

        $this->assertIsArray($data);
        $this->assertNotEmpty($data);
        $this->assertArrayHasKey('id', $data[0]);
        $this->assertArrayHasKey('selling_price', $data[0]);
    }

    public function test_pos_checkout_deducts_batch_via_fefo_and_records_movement(): void
    {
        $cashier = User::where('username', 'cashier')->first();

        // 1. Ensure Cashier has open shift
        $shift = CashierShift::firstOrCreate(
            ['cashier_id' => $cashier->id, 'status' => 'open'],
            ['opened_at' => now(), 'opening_cash' => 50000]
        );

        // 2. Find a product with active stock
        $product = Product::whereHas('batches', function ($q) {
            $q->where('status', 'active')->where('quantity_remaining', '>=', 2);
        })->first();

        $this->assertNotNull($product, 'Must have at least one product with active stock');

        $initialBatch = $product->batches()
            ->where('status', 'active')
            ->where('quantity_remaining', '>', 0)
            ->orderByRaw('expiry_date IS NULL, expiry_date ASC')
            ->first();

        $initialQty = (float) $initialBatch->quantity_remaining;
        $sellQty = 1.0;

        // 3. Checkout via POS
        $payload = [
            'items' => [
                [
                    'product_id' => $product->id,
                    'quantity' => $sellQty,
                    'unit_price' => (float) $product->selling_price,
                    'discount' => 0,
                ],
            ],
            'customer_name' => 'Test Patient',
            'payment_method' => 'cash',
            'amount_paid' => (float) $product->selling_price,
            'notes' => 'Automated test POS sale',
        ];

        $response = $this->actingAs($cashier)->postJson('/pos/checkout', $payload);

        $response->assertStatus(200);
        $result = $response->json();
        $this->assertTrue($result['success']);
        $this->assertArrayHasKey('sale_id', $result);
        $this->assertArrayHasKey('invoice_number', $result);

        $saleId = $result['sale_id'];

        // 4. Verify Sale in DB
        $sale = Sale::with('items.batchAllocations')->find($saleId);
        $this->assertNotNull($sale);
        $this->assertEquals('paid', $sale->payment_status);
        $this->assertEquals((float) $product->selling_price, (float) $sale->total_amount);

        // 5. Verify FEFO deduction on the batch
        $initialBatch->refresh();
        $this->assertEquals($initialQty - $sellQty, (float) $initialBatch->quantity_remaining);

        // 6. Verify immutable Inventory Movement ledger
        $movement = InventoryMovement::where('reference_id', $sale->invoice_number)
            ->where('product_id', $product->id)
            ->where('movement_type', 'sale')
            ->first();

        $this->assertNotNull($movement, 'Must create an inventory movement ledger record');
        $this->assertEquals(-$sellQty, (float) $movement->quantity);

        // 7. Test Receipt Endpoints
        $receiptResponse = $this->actingAs($cashier)->get("/pos/receipt/{$saleId}");
        $receiptResponse->assertStatus(200);
        $receiptResponse->assertSee($sale->invoice_number);
        $receiptResponse->assertSee('NJAMA PHARMACY');

        $digitalReceiptResponse = $this->actingAs($cashier)->get("/pos/digital-receipt/{$saleId}");
        $digitalReceiptResponse->assertStatus(200);
        $digitalReceiptResponse->assertSee($sale->invoice_number);
    }
}
