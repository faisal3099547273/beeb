<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'started_at' => 'datetime',
        'expired_at' => 'datetime',
        'specification' => 'array'
    ];

    // Scope

    public function scopePending()
    {
        return $this->where('status', 0)->where('expired_at', '>', now());
    }

    public function scopeLive()
    {
        return $this->where('status', 1)->where('started_at', '<', now())->where('expired_at', '>', now());
    }

    public function scopeUpcoming()
    {
        return $this->where('status', 1)->where('started_at', '>', now());
    }

    public function scopeExpired()
    {
        return $this->where('expired_at', '<', now());
    }

    public function merchant()
    {
        return $this->belongsTo(Merchant::class);
    }

    public function admin()
    {
        return $this->belongsTo(Admin::class);
    }
    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id', 'id');
    }

    public function productCate(){
        return $this->belongsTo('App\Models\Productcategory', 'productcate_id', 'id');
    }
    public function variation(){
    return $this->hasMany('App\Models\Variety', 'product_id', 'id');
    }
    public function bids()
    {
        return $this->hasMany(Bid::class);
    }
    public function comments(){
        return $this->hasMany('App\Models\Comment', 'Product_id', 'id');
    }
    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function winner()
    {
        return $this->hasOne(Winner::class);
    }
    public function images(){
        return $this->hasMany('App\Models\Galleryproduct', 'product_id', 'id');
    }

}
