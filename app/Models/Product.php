<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $table = 'products';
    protected $primaryKey = 'uid';
    public $timestamps = false;

    const CREATED_AT = 'created_date';
    const UPDATED_AT = 'updated_date';

    protected $fillable = [
        'product_code',
        'barcode',
        'product_name',
        'category_uid',
        'unit_uid',
        'brand_name',
        'purchase_price',
        'sale_price',
        'wholesale_price',
        'minimum_stock',
        'maximum_stock',
        'tax_percent',
        'discount_percent',
        'is_taxable',
        'is_active',
        'description',
        'image_path',
    ];

    protected $casts = [
        'purchase_price' => 'decimal:2',
        'sale_price' => 'decimal:2',
        'wholesale_price' => 'decimal:2',
        'minimum_stock' => 'decimal:3',
        'maximum_stock' => 'decimal:3',
        'tax_percent' => 'decimal:2',
        'discount_percent' => 'decimal:2',
        'is_taxable' => 'boolean',
        'is_active' => 'boolean',
        'created_date' => 'datetime',
        'updated_date' => 'datetime',
    ];

    public function category()
    {
        return $this->belongsTo(ProductCategory::class, 'category_uid', 'uid');
    }

    public function unit()
    {
        return $this->belongsTo(ProductUnit::class, 'unit_uid', 'uid');
    }

    public function inventories()
    {
        return $this->hasMany(Inventory::class, 'product_uid', 'uid');
    }
}
