<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalesInvoiceDetail extends Model
{
    use HasFactory;

    protected $table = 'sales_invoice_details';
    protected $primaryKey = 'uid';
    public $timestamps = false;

    protected $fillable = [
        'sales_invoice_uid',
        'product_uid',
        'quantity',
        'unit_price',
        'discount_percent',
        'discount_amount',
        'tax_percent',
        'tax_amount',
        'total_amount',
    ];

    protected $casts = [
        'quantity' => 'decimal:3',
        'unit_price' => 'decimal:2',
        'discount_percent' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'tax_percent' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
    ];

    public function salesInvoice()
    {
        return $this->belongsTo(SalesInvoice::class, 'sales_invoice_uid', 'uid');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_uid', 'uid');
    }
}
