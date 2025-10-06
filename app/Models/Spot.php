<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Spot extends Model
{
    use SoftDeletes, HasFactory;
    
    protected $fillable = [
        'user_id',
        'name',
        'address',
        'picture',
    ];

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function categories() {
        return $this->hasMany(Category::class);
    }

    public function reviews() {
        return $this->hasMany(Review::class);
    }
}
