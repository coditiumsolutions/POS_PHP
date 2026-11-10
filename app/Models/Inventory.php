<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
    use HasFactory;

    protected $table = 'inventories';
    protected $primaryKey = 'uid';
    public $timestamps = false;
    protected $guarded = ['uid'];

    protected $casts = [
        'quantity_in_stock' => 'decimal:3',
        'quantity_available' => 'decimal:3',
        'quantity_reserved' => 'decimal:3',
        'quantity_in' => 'decimal:3',
        'quantity_out' => 'decimal:3',
        'average_cost' => 'decimal:2',
        'updated_date' => 'datetime',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_uid', 'uid');
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_uid', 'uid');
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class, 'warehouse_uid', 'uid');
    }

    public function getBranchNameAttribute(): string
    {
        return $this->branch?->branch_name
            ?: ($this->warehouse?->warehouse_name ?: '—');
    }

    public function getCurrentQuantityAttribute()
    {
        return $this->quantity_in_stock;
    }

    public static function findForProductBranch(int $productUid, int $branchUid): ?self
    {
        return static::query()
            ->where('product_uid', $productUid)
            ->where('branch_uid', $branchUid)
            ->first();
    }
}
