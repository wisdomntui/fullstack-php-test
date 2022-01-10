<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    public static $rules = [
        'provider_data.date' => 'required',
        'provider_data.hmo_code' => 'required|exists:hmos,code',
        'provider_data.provider_name' => 'required',
        'order.*.item' => 'required',
        'order.*.unit_price' => 'required|numeric',
        'order.*.qty' => 'required|numeric',
        'order.*.sub_total' => 'required|numeric',
    ];

    public function batch()
    {
        return $this->belongsTo(Batch::class);
    }
}
