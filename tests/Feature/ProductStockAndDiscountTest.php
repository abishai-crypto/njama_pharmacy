<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Product;
use App\Models\Category;
use App\Models\UnitOfMeasure;
use App\Models\InventoryBatch;
use App\Models\InventoryMovement;
use App\Models\Sale;
use App\Models\CashierShift;

class ProductStockAndDiscountTest extends TestCase
{
    public function test_pos_checkout_with_item_and_overall_discount(): void
    {
        $cashier = User::where('username', 'cashier')->first();

        // 1. Open shift
        CashierShift::firstOrCreate(
            ['cashier_id' => $cashier->id, 'status' => 'open'],
            ['opened_at' => now(), 'opening_cash' => 50000]
        );

        // 2. Product with stock
        $product = Product::whereHas('batches', function ($q) {
            $q->where('status', 'active')->where('quantity_remaining', '>=', 2);
        })->first();

        $unitPrice = (float) $product->selling_price;
        $qty = 2.0;
        $subtotal = $qty * $unitPrice;
        $itemDiscount = 100.0;
        $overallDiscount = 200.0;
        $totalDiscount = $itemDiscount + $overallDiscount;
        $payable = max(0, $subtotal - $totalDiscount);

        $payload = [
            'items' => [
                [
                    'product_id' => $product->id,
                    'quantity' => $qty,
                    'unit_price' => $unitPrice,
                    'discount' => $itemDiscount,
                ],
            ],
            'overall_discount' => $overallDiscount,
            'customer_name' => 'Discount Test Patient',
            'payment_method' => 'cash',
            'amount_paid' => $payable + 500, // pays extra to test change
            'notes' => 'Testing sales discount',
        ];

        $response = $this->actingAs($cashier)->postJson('/pos/checkout', $payload);
        $response->assertStatus(200);

        $data = $response->json();
        $this->assertTrue($data['success']);
        $this->assertEquals($totalDiscount, $data['discount_amount']);
        $this->assertEquals($payable, $data['total_amount']);
        $this->assertEquals(500, $data['change_due']);

        // Check Sale in database
        $sale = Sale::find($data['sale_id']);
        $this->assertEquals($subtotal, (float) $sale->subtotal);
        $this->assertEquals($totalDiscount, (float) $sale->discount_amount);
        $this->assertEquals($payable, (float) $sale->total_amount);

        // Verify thermal receipt has discount
        $receipt = $this->actingAs($cashier)->get("/pos/receipt/{$sale->id}");
        $receipt->assertStatus(200);
        $receipt->assertSee('Punguzo (Discount)');

        // Verify digital receipt has discount
        $digital = $this->actingAs($cashier)->get("/pos/digital-receipt/{$sale->id}");
        $digital->assertStatus(200);
        $digital->assertSee('Discount:');
    }

    public function test_register_product_with_initial_quantity(): void
    {
        $admin = User::where('role', 'admin')->first();
        $category = Category::first();
        $unit = UnitOfMeasure::first();

        $code = 'TEST-' . rand(1000, 9999);
        $initialQty = 75;

        $payload = [
            'item_code' => $code,
            'generic_name' => 'Amoxicillin ' . rand(100, 999),
            'brand_name' => 'Amox Brand',
            'category_id' => $category->id,
            'unit_id' => $unit->id,
            'purchase_cost' => 1500,
            'selling_price' => 2500,
            'minimum_stock_alert' => 10,
            'initial_quantity' => $initialQty,
            'batch_number' => 'INIT-AMOX-01',
            'expiry_date' => now()->addYear()->format('Y-m-d'),
        ];

        $response = $this->actingAs($admin)->post('/products', $payload);
        $response->assertRedirect(route('products.index'));

        // Verify product was created
        $product = Product::where('item_code', $code)->first();
        $this->assertNotNull($product);
        $this->assertEquals($initialQty, (float) $product->total_stock);

        // Verify batch was created
        $batch = InventoryBatch::where('product_id', $product->id)->first();
        $this->assertNotNull($batch);
        $this->assertEquals('INIT-AMOX-01', $batch->batch_number);
        $this->assertEquals($initialQty, (float) $batch->quantity_remaining);

        // Verify opening_stock movement was recorded
        $movement = InventoryMovement::where('product_id', $product->id)
            ->where('movement_type', 'opening_stock')
            ->first();
        $this->assertNotNull($movement);
        $this->assertEquals($initialQty, (float) $movement->quantity);
    }

    public function test_update_product_stock_quantity(): void
    {
        $admin = User::where('role', 'admin')->first();
        $product = Product::whereHas('batches')->first();
        $currentStock = (float) $product->total_stock;
        $newStock = $currentStock + 20;

        $payload = [
            'item_code' => $product->item_code,
            'generic_name' => $product->generic_name,
            'category_id' => $product->category_id,
            'unit_id' => $product->unit_id,
            'purchase_cost' => $product->purchase_cost,
            'selling_price' => $product->selling_price,
            'minimum_stock_alert' => $product->minimum_stock_alert,
            'stock_quantity' => $newStock,
            'stock_adjustment_reason' => 'Kuhesabu upya bidhaa',
        ];

        $response = $this->actingAs($admin)->put("/products/{$product->id}", $payload);
        $response->assertRedirect(route('products.index'));

        $product->refresh();
        $this->assertEquals($newStock, (float) $product->total_stock);

        // Verify movement was recorded
        $movement = InventoryMovement::where('product_id', $product->id)
            ->where('movement_type', 'adjustment_in')
            ->latest('id')
            ->first();
        $this->assertNotNull($movement);
        $this->assertEquals(20, (float) $movement->quantity);
    }

    public function test_update_batch_directly(): void
    {
        $admin = User::where('role', 'admin')->first();
        $batch = InventoryBatch::where('status', 'active')->first();
        $oldRemaining = (float) $batch->quantity_remaining;
        $newRemaining = $oldRemaining + 15;

        $response = $this->actingAs($admin)->put("/batches/{$batch->id}", [
            'batch_number' => $batch->batch_number,
            'quantity_remaining' => $newRemaining,
            'expiry_date' => now()->addMonths(6)->format('Y-m-d'),
            'notes' => 'Batch stock direct adjustment',
        ]);

        $response->assertSessionHas('success');
        $batch->refresh();
        $this->assertEquals($newRemaining, (float) $batch->quantity_remaining);

        // Verify movement
        $movement = InventoryMovement::where('batch_id', $batch->id)
            ->where('movement_type', 'adjustment_in')
            ->latest('id')
            ->first();
        $this->assertNotNull($movement);
        $this->assertEquals(15, (float) $movement->quantity);
    }
}
