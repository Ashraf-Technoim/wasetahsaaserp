<?php

namespace Database\Seeders\Demo;

use Illuminate\Database\Seeder;

class DemoAdjustmentProductsTableSeeder extends Seeder
{
    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        \DB::table('adjustment_products')->delete();

        \DB::table('adjustment_products')->insert([
            0 => [
                'id' => 1,
                'type' => 1,
                'purchase_price' => 1145.89,
                'quantity' => 1.0,
                'created_at' => '2022-05-01 06:04:24',
                'updated_at' => '2022-05-01 06:04:24',
                'adjustment_id' => 1,
                'product_id' => 3,
            ],
            1 => [
                'id' => 2,
                'type' => 0,
                'purchase_price' => 953.03,
                'quantity' => 1.0,
                'created_at' => '2022-05-01 06:04:24',
                'updated_at' => '2022-05-01 06:04:24',
                'adjustment_id' => 1,
                'product_id' => 2,
            ],
        ]);
    }
}
