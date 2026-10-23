<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Product;
use App\Models\ProductPriceHistory;

class BulkPriceUpdaterTest extends TestCase
{
    public function test_admin_can_view_bulk_price_updater(): void
    {
        $admin = User::where('username', 'dr.njama')->first();

        $response = $this->actingAs($admin)->get('/products-bulk-prices');
        $response->assertStatus(200);
        $response->assertSee('NJAMA PHARMACY');
        $response->assertSee('Sasisha Bei za Kuuzia');
    }

    public function test_admin_can_update_product_selling_price_in_bulk(): void
    {
        $admin = User::where('username', 'dr.njama')->first();
        $product = Product::first();

        $this->assertNotNull($product);
        $oldPrice = (float) $product->selling_price;
        $newPrice = $oldPrice + 500;

        $response = $this->actingAs($admin)->post('/products-bulk-prices', [
            'prices' => [
                $product->id => $newPrice,
            ],
        ]);

        $response->assertStatus(302);
        $response->assertRedirect('/products-bulk-prices');

        $product->refresh();
        $this->assertEquals($newPrice, (float) $product->selling_price);

        // Check price history record was logged
        $history = ProductPriceHistory::where('product_id', $product->id)->latest('id')->first();
        $this->assertNotNull($history);
        $this->assertEquals($newPrice, (float) $history->new_selling_price);
        $this->assertEquals($oldPrice, (float) $history->old_selling_price);
    }
}
