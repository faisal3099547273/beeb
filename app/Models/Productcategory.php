<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use PhpOffice\PhpSpreadsheet\Calculation\Statistical;

class Productcategory extends Model
{
    protected $primaryKey = "id";
    protected $table = "productcategoies";
    protected $fillable = ["name", "icon", "status", "created_at", "updated_at"];
    protected Static $columns = [
        "created_at" => "Created Date",
        "updated" => "Modify Date",
    ];
    public $timestamps = false;

   public function catProducts(){
    return $this->hasMany('App\Models\Product', 'productcate_id', 'id');
   }

}
