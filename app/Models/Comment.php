<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
        protected $table = "comments";
        protected $primaryKey = "comment_id";
        protected $fillable = ['product_id', 'user_id', 'comment', 'created_at', 'updated_at'];

        protected static $columns = [
            'created_at' => "Created Date",
            'updated_at' => "Modify Date",
        ];

        public $timestamps = false;

        function product(){
            return $this->belongsTo('App\Models\Product', 'product_id', 'id');
        }

        function user(){
            return $this->belongsTo('App\Models\User', 'user_id', 'id');
        }

}
