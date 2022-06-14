<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $table = 'products';

    protected $fillable =
    [
    'company_id',
    'product_name',
    'price',
    'stock',
    'comment',
    'image'
    ];

    public function company()
    {
        return $this->belongsTo('App\Models\Company');
    }
}
