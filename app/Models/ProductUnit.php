<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductUnit extends Model
{
    use HasFactory;

    protected $table = 'product_units';
    protected $primaryKey = 'uid';
    public $timestamps = false;

    protected $fillable = [
        'unit_code',
        'unit_name',
        'is_active',
    ];
}
