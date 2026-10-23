<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\ExpenseCategory;
use App\Models\Expense;

class ReportsAndFinancialsTest extends TestCase
{
    public function test_admin_can_view_inventory_valuation(): void
    {
        $admin = User::where('username', 'dr.njama')->first();

        $response = $this->actingAs($admin)->get('/reports/inventory-valuation');
        $response->assertStatus(200);
        $response->assertSee('NJAMA PHARMACY');
        $response->assertSee('Thamani kwa Bei ya Jumla');
        $response->assertSee('Thamani kwa Bei ya Rejareja');
    }

    public function test_admin_can_view_profit_and_loss_report(): void
    {
        $admin = User::where('username', 'dr.njama')->first();

        $response = $this->actingAs($admin)->get('/reports/profit-loss');
        $response->assertStatus(200);
        $response->assertSee('FAIDA GHAFI');
        $response->assertSee('Matumizi ya Uendeshaji');
    }

    public function test_admin_can_view_sales_report(): void
    {
        $admin = User::where('username', 'dr.njama')->first();

        $response = $this->actingAs($admin)->get('/reports/sales');
        $response->assertStatus(200);
        $response->assertSee('Jumla ya Mauzo');
    }

    public function test_admin_can_view_expiry_report(): void
    {
        $admin = User::where('username', 'dr.njama')->first();

        $response = $this->actingAs($admin)->get('/reports/expiry');
        $response->assertStatus(200);
        $response->assertSee('Zilizopitwa na Wakati');
    }

    public function test_user_can_record_operating_expense(): void
    {
        $admin = User::where('username', 'dr.njama')->first();
        $cat = ExpenseCategory::first();

        $response = $this->actingAs($admin)->post('/expenses', [
            'category_id' => $cat->id,
            'title' => 'Kodi ya pango la mwezi',
            'amount' => 150000,
            'expense_date' => date('Y-m-d'),
            'payment_method' => 'bank_transfer',
            'notes' => 'Malipo ya kodi ya duka NJAMA PHARMACY',
        ]);

        $response->assertStatus(302);
        $this->assertDatabaseHas('expenses', [
            'title' => 'Kodi ya pango la mwezi',
            'amount' => 150000,
        ]);
    }
}
