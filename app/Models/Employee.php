<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    protected $primaryKey = 'uid';

    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'employee_code',
        'full_name',
        'father_name',
        'cnic',
        'designation',
        'department',
        'mobile_no',
        'email',
        'address',
        'date_of_joining',
        'basic_salary',
        'is_active',
        'created_date',
        'updated_date',
    ];
}
