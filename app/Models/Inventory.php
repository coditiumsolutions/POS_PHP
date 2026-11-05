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

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_uid', 'uid');
    }
}
