<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VendorPayment extends Model
{
    use HasFactory;

    protected $table = 'vendor_payments';
    protected $primaryKey = 'uid';
    public $timestamps = false;
    protected $guarded = ['uid'];

    protected $casts = [
        'payment_date' => 'datetime',
        'amount' => 'decimal:2',
        'created_date' => 'datetime',
        'updated_date' => 'datetime',
    ];

    public function vendor()
    {
        return $this->belongsTo(Vendor::class, 'vendor_uid', 'uid');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_uid', 'uid');
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_uid', 'uid');
    }
}
