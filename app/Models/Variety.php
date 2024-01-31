<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Variety extends Model
{
    protected $table = "varieties";
    protected $primaryKey = "id";

    protected $fillable = ['name', 'price', 'product_id', 'created_at', 'updated_at'];
    protected static $columns = [
        'created_at' => "Created Date",
        'updated_at' => "Modify Date",
    ];
    public function product(){
        return $this->belongsTo('App\Models\Product', 'product_id', 'id');
    }
}
