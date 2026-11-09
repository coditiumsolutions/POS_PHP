<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeePayment extends Model
{
    use HasFactory;

    protected $table = 'employee_payments';
    protected $primaryKey = 'uid';
    public $timestamps = false;

    protected $guarded = ['uid'];

    protected $fillable = [
        'payment_no',
        'payment_date',
        'employee_uid',
        'month_year',
        'basic_salary',
        'total_allowances',
        'total_deductions',
        'net_salary',
        'amount',
        'remarks',
        'user_uid',
        'created_date',
        'updated_date',
    ];

    protected $casts = [
        'payment_date' => 'datetime',
        'basic_salary' => 'decimal:2',
        'total_allowances' => 'decimal:2',
        'total_deductions' => 'decimal:2',
        'net_salary' => 'decimal:2',
        'amount' => 'decimal:2',
        'created_date' => 'datetime',
        'updated_date' => 'datetime',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_uid', 'uid');
    }
}
