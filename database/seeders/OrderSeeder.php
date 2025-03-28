<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Order;

class OrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Order::create([
            'customer_id' => 1, // ID của khách hàng (giả sử khách hàng với ID 1 đã tồn tại)
            'total' => 100.50, // Tổng tiền
            'status' => 'pending', // Trạng thái đơn hàng
        ]);

        Order::create([
            'customer_id' => 2, // ID của khách hàng (giả sử khách hàng với ID 2 đã tồn tại)
            'total' => 250.75, // Tổng tiền
            'status' => 'completed', // Trạng thái đơn hàng
        ]);
    }
}
