<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Csvmodel extends Model
{
    protected $fillable = [
        'Year','Make','Model','Platform','Class','Notes','admin_id'
    ];
    public function admin()
    {
        return $this->belongsTo(Admin::class);
    }
    public function merchant()
    {
        return $this->belongsTo(Merchant::class);
    }
}
