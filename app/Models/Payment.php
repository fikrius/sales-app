<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Payment extends Model
{
    use SoftDeletes;

    protected $fillable = ['code', 'sale_id', 'date', 'amount'];

    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }
}
