<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Supplier;
use App\Models\Product;
use App\Models\Category;
use App\Models\Purchase;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class PurchaseTest extends TestCase
{
    use DatabaseTransactions;

    protected User $user;
    protected Supplier $supplier;
    protected Product $product;

    protected function setUp(): void
    {
        parent::setUp();

        // Create permission if it doesn't exist
        Permission::firstOrCreate(
            ['name' => 'orders.menu'],
            ['group_name' => 'orders']
        );

        $role = Role::firstOrCreate(['name' => 'test-procurement-role']);
        if (!$role->hasPermissionTo('orders.menu')) {
            $role->givePermissionTo('orders.menu');
        }

        $this->user = User::factory()->create([
            'username' => 'testuser_procurement',
        ]);
        $this->user->assignRole($role);

        $this->supplier = Supplier::create([
            'name' => 'Supplier ABC',
            'email' => 'abc@supplier.com',
            'phone' => '0812345678',
            'address' => 'Bontang',
            'shopname' => 'ABC Shop',
            'type' => 'Distributor',
            'bank_name' => 'BCA',
            'bank_branch' => 'Bontang',
            'account_name' => 'Supplier ABC',
            'account_number' => '123456',
            'city' => 'Bontang',
        ]);

        $category = Category::create([
            'name' => 'Materials',
            'slug' => 'materials',
        ]);

        $this->product = Product::create([
            'name' => 'Cement Box',
            'slug' => 'cement-box',
            'code' => 'PC1001',
            'category_id' => $category->id,
            'stock' => 10,
            'primary_unit' => 'pcs',
            'secondary_unit' => 'box',
            'conversion_rate' => 10,
            'buying_price' => 50000,
            'selling_price' => 60000,
            'wholesale_price' => 55000,
            'wholesale_minimum_qty' => 5,
            'buying_date' => '2026-07-08',
            'expire_date' => '2030-07-08',
        ]);
    }

    public function test_can_create_purchase_order_and_receive_stock(): void
    {
        $response = $this->actingAs($this->user)
            ->post('/purchases', [
                'supplier_id' => $this->supplier->id,
                'purchase_date' => '2026-07-08',
                'purchase_status' => 'received',
                'pay_amount' => 100000,
                'items' => [
                    [
                        'product_id' => $this->product->id,
                        'quantity' => 2,
                        'unit_cost' => 50000,
                        'unit_type' => 'primary',
                    ]
                ]
            ]);

        $response->assertRedirect(route('purchases.index'));
        $this->product->refresh();
        // Base stock: 10 + 2 = 12
        $this->assertEquals(12, $this->product->stock);
    }

    public function test_purchase_order_in_secondary_unit_multiplies_stock(): void
    {
        $response = $this->actingAs($this->user)
            ->post('/purchases', [
                'supplier_id' => $this->supplier->id,
                'purchase_date' => '2026-07-08',
                'purchase_status' => 'received',
                'pay_amount' => 500000,
                'items' => [
                    [
                        'product_id' => $this->product->id,
                        'quantity' => 2, // 2 boxes
                        'unit_cost' => 250000,
                        'unit_type' => 'secondary', // Uses conversion rate of 10
                    ]
                ]
            ]);

        $response->assertRedirect(route('purchases.index'));
        $this->product->refresh();
        // Base stock: 10 + (2 * 10) = 30
        $this->assertEquals(30, $this->product->stock);
    }

    public function test_receiving_pending_secondary_purchase_multiplies_stock(): void
    {
        // Create as pending: no stock change yet
        $this->actingAs($this->user)->post('/purchases', [
            'supplier_id' => $this->supplier->id,
            'purchase_date' => '2026-07-08',
            'purchase_status' => 'pending',
            'pay_amount' => 0,
            'items' => [
                [
                    'product_id' => $this->product->id,
                    'quantity' => 2, // 2 boxes
                    'unit_cost' => 250000,
                    'unit_type' => 'secondary', // conversion rate 10
                ]
            ]
        ]);

        $this->product->refresh();
        $this->assertEquals(10, $this->product->stock); // unchanged while pending

        // Receive later: stock must rise by 2 boxes * conversion 10 = 20 -> 30
        $purchase = Purchase::latest('id')->first();
        $this->actingAs($this->user)
            ->post("/purchases/{$purchase->id}/status", ['status' => 'received'])
            ->assertRedirect();

        $this->product->refresh();
        $this->assertEquals(30, $this->product->stock);
    }

    public function test_can_record_stock_opname_addition(): void
    {
        $response = $this->actingAs($this->user)
            ->post('/adjustments', [
                'product_id' => $this->product->id,
                'type' => 'addition',
                'quantity' => 5,
                'reason' => 'Found lost inventory box',
            ]);

        $response->assertRedirect(route('adjustments.index'));
        $this->product->refresh();
        // Base stock: 10 + 5 = 15
        $this->assertEquals(15, $this->product->stock);
    }

    public function test_can_record_stock_opname_subtraction(): void
    {
        $response = $this->actingAs($this->user)
            ->post('/adjustments', [
                'product_id' => $this->product->id,
                'type' => 'subtraction',
                'quantity' => 3,
                'reason' => 'Broken items during logistics',
            ]);

        $response->assertRedirect(route('adjustments.index'));
        $this->product->refresh();
        // Base stock: 10 - 3 = 7
        $this->assertEquals(7, $this->product->stock);
    }
}
