<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    protected $table = 'companies';

    protected $guarded = 
    [
        'company_name',
        'street_address',
        'representative_name' 
    ];

    public function products()
    {
        return $this->hasMany('App\Models\Product');
    }
}
