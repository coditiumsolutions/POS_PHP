<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
    use HasFactory;

    protected $table = 'branches';
    protected $primaryKey = 'uid';
    public $timestamps = false;

    const CREATED_AT = 'created_date';
    const UPDATED_AT = 'updated_date';

    protected $fillable = [
        'branch_code',
        'branch_name',
        'address',
        'city',
        'contact_person',
        'mobile_no',
        'phone_no',
        'email',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'created_date' => 'datetime',
        'updated_date' => 'datetime',
    ];
}
