<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeDeduction extends Model
{
    use HasFactory;

    protected $table = 'employee_deductions';
    protected $primaryKey = 'uid';
    public $timestamps = false;

    protected $fillable = [
        'employee_uid',
        'deduction_type',
        'amount',
        'is_active',
        'created_date',
        'updated_date',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'is_active' => 'boolean',
        'created_date' => 'datetime',
        'updated_date' => 'datetime',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_uid', 'uid');
    }
}
