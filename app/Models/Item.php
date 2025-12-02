<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Item extends Model
{
    use SoftDeletes;

    protected $fillable = ['code', 'name', 'price', 'image'];

    public function saleItems()
    {
        return $this->hasMany(SaleItem::class);
    }
}
