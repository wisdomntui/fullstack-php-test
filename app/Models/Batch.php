<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Batch extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'hmo_id',
    ];

    public function hmo()
    {
        return $this->belongsTo(Hmo::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}
