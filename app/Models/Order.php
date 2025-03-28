<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = ['customer_id', 'total', 'status'];

   
    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }
}