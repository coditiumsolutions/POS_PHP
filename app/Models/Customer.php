<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    protected $table = 'customers';
    protected $primaryKey = 'uid';
    public $timestamps = false;

    const CREATED_AT = 'created_date';
    const UPDATED_AT = 'updated_date';

    protected $fillable = [
        'customer_code',
        'customer_name',
        'mobile_no',
        'phone_no',
        'email',
        'address',
        'city',
        'area',
        'ntn',
        'opening_balance',
        'advance_balance',
        'credit_limit',
        'discount_percent',
        'is_active',
    ];

    protected $casts = [
        'opening_balance' => 'decimal:2',
        'advance_balance' => 'decimal:2',
        'credit_limit' => 'decimal:2',
        'discount_percent' => 'decimal:2',
        'is_active' => 'boolean',
        'created_date' => 'datetime',
        'updated_date' => 'datetime',
    ];

    public function salesInvoices()
    {
        return $this->hasMany(SalesInvoice::class, 'customer_uid', 'uid');
    }

    public function payments()
    {
        return $this->hasMany(CustomerPayment::class, 'customer_uid', 'uid');
    }

    public function monthlySupplies()
    {
        return $this->hasMany(CustomerMonthlySupply::class, 'customer_uid', 'uid');
    }
}
