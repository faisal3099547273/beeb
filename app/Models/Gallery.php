<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Gallery extends Model
{
     protected $table = "galleries";
     protected $primaryKey = "gallery_id";
     protected $fillable = ['product_id', 'image', 'created_at', 'updated_at'];
     public $timestamps = false;
     protected static $columns = [
        'created_at' => "Created Date",
        'updated_at' => "Modify Date",
    ];
    function product(){
        return $this->belongsTo('App\Models\Product', 'product_id', 'id');
    }
}
