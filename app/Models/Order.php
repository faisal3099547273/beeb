<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $table = "orders";
    protected $primaryKey = "order_id";
    protected $fillable = ['user_id', 'ip_address', 'quantity', 'product_id', 'status', 'created_at'];
    public function product(){
        return $this->belongsTo('App\Models\Product', 'product_id', 'id');
    }
    public function variations()
    {
        return $this->hasManyThrough(Variety::class, Product::class);
    }

}
