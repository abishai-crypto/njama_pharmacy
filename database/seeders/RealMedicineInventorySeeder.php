<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Supplier;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\Product;
use App\Models\Category;
use App\Models\UnitOfMeasure;
use App\Models\InventoryBatch;
use App\Models\InventoryMovement;
use App\Models\User;

class RealMedicineInventorySeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('role', 'admin')->first();
        $adminId = $admin ? $admin->id : 1;

        // 1. Supplier from invoice: @TONIC
        $supplier = Supplier::firstOrCreate(
            ['name' => 'TONIC Pharmaceuticals & Medical Supplies'],
            [
                'contact_person' => 'Sales Representative',
                'phone' => '+255 768 000 111',
                'email' => 'orders@tonicpharma.co.tz',
                'address' => 'Kariakoo, Dar es Salaam, Tanzania',
                'tin_number' => '102-458-990',
                'vrn_number' => '40-001290-A',
                'opening_balance' => 0.00,
                'outstanding_balance' => 0.00,
                'payment_terms' => 'Cash / Paid on delivery',
                'is_active' => true,
            ]
        );

        // 2. Invoice received on 20 October 2026 with total Tsh 1,308,596
        $purchase = Purchase::updateOrCreate(
            ['invoice_number' => 'INV-20261020-TONIC'],
            [
                'supplier_id' => $supplier->id,
                'purchase_date' => '2026-10-20',
                'total_amount' => 1308596.00,
                'paid_amount' => 1308596.00,
                'balance_due' => 0.00,
                'payment_status' => 'paid',
                'payment_method' => 'bank_transfer',
                'received_by' => $adminId,
                'notes' => 'Orodha ya Dawa na Vifaa Tiba – Invoice ya Oktoba 2026 (Bidhaa 180: Orodha Kuu 1-167 na Ziada 1-13). Total verified Tsh 1,308,596.',
            ]
        );

        $medicines = include database_path('data/medicines_data.php');
        $unitsCache = UnitOfMeasure::all()->keyBy('code');
        $categoriesCache = Category::all()->keyBy('slug');

        $totalComputed = 0;

        foreach ($medicines as $item) {
            $cat = $categoriesCache->get($item['category']) ?? Category::first();
            $unit = $unitsCache->get($item['unit']) ?? UnitOfMeasure::firstOrCreate(
                ['code' => $item['unit']],
                ['name' => $item['unit']]
            );

            // Create or update Product
            $product = Product::updateOrCreate(
                ['item_code' => $item['item_code']],
                [
                    'generic_name' => $item['name'],
                    'brand_name' => $item['brand'],
                    'category_id' => $cat ? $cat->id : null,
                    'unit_id' => $unit ? $unit->id : null,
                    'dosage_form' => $item['form'],
                    'strength' => $item['strength'],
                    'pack_size' => isset($item['sales_style']) ? "{$item['brand']} ({$item['sales_style']})" : $item['brand'],
                    'requires_prescription' => $item['rx'],
                    'is_restricted' => $item['restricted'],
                    'purchase_cost' => $item['cost'],
                    'selling_price' => $item['selling'],
                    'minimum_stock_alert' => 5,
                    'storage_conditions' => 'Store in a cool dry place below 30°C',
                    'is_active' => true,
                ]
            );

            $subtotal = round($item['qty'] * $item['cost'], 2);
            $totalComputed += $subtotal;

            // Record Purchase Item
            PurchaseItem::updateOrCreate(
                [
                    'purchase_id' => $purchase->id,
                    'product_id' => $product->id,
                ],
                [
                    'batch_number' => $item['batch'],
                    'expiry_date' => $item['expiry'],
                    'quantity' => $item['qty'],
                    'unit_cost' => $item['cost'],
                    'subtotal' => $subtotal,
                ]
            );

            // Record Batch
            $batch = InventoryBatch::updateOrCreate(
                [
                    'product_id' => $product->id,
                    'batch_number' => $item['batch'],
                ],
                [
                    'supplier_id' => $supplier->id,
                    'purchase_id' => $purchase->id,
                    'quantity_received' => $item['qty'],
                    'quantity_remaining' => $item['qty'],
                    'purchase_cost' => $item['cost'],
                    'selling_price' => $item['selling'],
                    'expiry_date' => $item['expiry'],
                    'date_received' => '2026-10-20',
                    'storage_location' => 'Main Pharmacy Shelf',
                    'status' => 'active',
                    'created_by' => $adminId,
                ]
            );

            // Record Movement ledger if not already recorded or update
            $existingMovement = InventoryMovement::where('product_id', $product->id)
                ->where('batch_id', $batch->id)
                ->where('reference_id', $purchase->invoice_number)
                ->first();

            if (!$existingMovement) {
                InventoryMovement::create([
                    'product_id' => $product->id,
                    'batch_id' => $batch->id,
                    'movement_type' => 'purchase_receipt',
                    'quantity' => $item['qty'],
                    'balance_after' => $item['qty'],
                    'unit_cost' => $item['cost'],
                    'reference_type' => 'Purchase',
                    'reference_id' => $purchase->invoice_number,
                    'notes' => "Invoice receipt 20 Oct 2026: {$item['brand']}",
                    'user_id' => $adminId,
                    'created_at' => now(),
                ]);
            } else {
                $existingMovement->update([
                    'quantity' => $item['qty'],
                    'balance_after' => $item['qty'],
                    'unit_cost' => $item['cost'],
                ]);
            }
        }
    }
}
