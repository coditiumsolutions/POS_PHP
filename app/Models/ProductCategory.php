<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductCategory extends Model
{
    use HasFactory;

    protected $table = 'product_categories';
    protected $primaryKey = 'uid';
    public $timestamps = false;

    protected $fillable = [
        'category_code',
        'category_name',
        'description',
        'is_active',
    ];
}
