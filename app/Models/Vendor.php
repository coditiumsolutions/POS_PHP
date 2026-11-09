<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vendor extends Model
{
    use HasFactory;

    protected $table = 'vendors';
    protected $primaryKey = 'uid';
    public $timestamps = false;

    const CREATED_AT = 'created_date';
    const UPDATED_AT = 'updated_date';

    protected $fillable = [
        'vendor_code',
        'vendor_name',
        'contact_person',
        'mobile_no',
        'phone_no',
        'email',
        'address',
        'city',
        'ntn',
        'opening_balance',
        'credit_limit',
        'is_active',
        'created_date',
        'updated_date',
    ];

    protected $casts = [
        'opening_balance' => 'decimal:2',
        'credit_limit' => 'decimal:2',
        'is_active' => 'boolean',
        'created_date' => 'datetime',
        'updated_date' => 'datetime',
    ];

    public function purchaseInvoices()
    {
        return $this->hasMany(PurchaseInvoice::class, 'vendor_uid', 'uid');
    }

    public function payments()
    {
        return $this->hasMany(VendorPayment::class, 'vendor_uid', 'uid');
    }
}
