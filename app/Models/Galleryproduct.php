<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Galleryproduct extends Model
{
    protected $table = "galleryproducts";
    protected $primaryKey = "id";
    protected $fillable = ['image', 'product_id', 'image', 'created_at', 'updated_at'];
    public $timestamps = false;
    protected static $columns = [
       'created_at' => "Created Date",
       'updated_at' => "Modify Date",
   ];
   function productImages(){
    return $this->belongsTo('App\Models\Product', 'product_id', 'id');
   }
}
