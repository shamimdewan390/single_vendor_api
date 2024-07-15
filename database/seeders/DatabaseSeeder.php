<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\Product;
use App\Models\Order;
use App\Models\Transaction;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run()
    {
        $this->call(RoleSeeder::class);
        // Create 10 categories
        Category::factory(10)->create();

        // Create 50 products
        Product::factory(50)->create();

        // Create 10 users and associated orders and transactions
        User::factory(10)->create()->each(function ($user) {
            Order::factory(rand(1, 5))->create(['user_id' => $user->id])->each(function ($order) {
                Transaction::factory()->create(['order_id' => $order->id]);

                // Attach products to the order
                $products = Product::inRandomOrder()->take(rand(1, 5))->pluck('id');
                foreach ($products as $productId) {
                    $order->products()->attach($productId, ['quantity' => rand(1, 5)]);
                }
            });
        });
    }
}
