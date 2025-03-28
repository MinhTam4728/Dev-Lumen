<?php

namespace App\Transformers;

class CustomerTransformer
{
    /**
     * Format dữ liệu của một khách hàng
     *
     * @param  \App\Models\Customer  $customer
     * @return array
     */
    public static function transform($customer)
    {
        return [
            'id' => $customer->id,
            'name' => $customer->name,
            'email' => $customer->email,
            'phone' => $customer->phone,
            'role' => $customer->role === 0 ? 'Admin' : 'User', // Format vai trò
            'created_at' => $customer->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $customer->updated_at->format('Y-m-d H:i:s'),
        ];
    }
}