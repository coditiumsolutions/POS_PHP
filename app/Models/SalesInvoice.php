<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalesInvoice extends Model
{
    use HasFactory;

    protected $table = 'sales_invoices';
    protected $primaryKey = 'uid';
    public $timestamps = false;

    const CREATED_AT = 'created_date';
    const UPDATED_AT = 'updated_date';

    protected $fillable = [
        'client_transaction_id',
        'invoice_no',
        'invoice_date',
        'branch_uid',
        'branch',
        'customer_uid',
        'user_uid',
        'user_name',
        'terminal_uid',
        'terminal',
        'payment_method_uid',
        'sub_total',
        'discount_amount',
        'tax_amount',
        'other_charges',
        'net_amount',
        'paid_amount',
        'customer_given_amount',
        'change_given_amount',
        'balance_amount',
        'payment_status',
        'invoice_status',
        'pos_type',
        'remarks',
    ];

    protected $casts = [
        'invoice_date' => 'datetime',
        'sub_total' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'other_charges' => 'decimal:2',
        'net_amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'customer_given_amount' => 'decimal:2',
        'change_given_amount' => 'decimal:2',
        'balance_amount' => 'decimal:2',
        'created_date' => 'datetime',
        'updated_date' => 'datetime',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_uid', 'uid');
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_uid', 'uid');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_uid', 'uid');
    }

    public function details()
    {
        return $this->hasMany(SalesInvoiceDetail::class, 'sales_invoice_uid', 'uid');
    }

    public function paymentMethod()
    {
        return $this->belongsTo(PaymentMethod::class, 'payment_method_uid', 'uid');
    }
}
