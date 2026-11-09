<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    use HasFactory;

    protected $table = 'expenses';
    protected $primaryKey = 'uid';
    public $timestamps = false;

    const CREATED_AT = 'created_date';
    const UPDATED_AT = 'updated_date';

    protected $fillable = [
        'expense_no',
        'expense_date',
        'expense_category_uid',
        'branch_uid',
        'user_uid',
        'amount',
        'payment_mode',
        'description',
        'created_date',
        'updated_date',
    ];

    protected $casts = [
        'expense_date' => 'datetime',
        'amount' => 'decimal:2',
        'created_date' => 'datetime',
        'updated_date' => 'datetime',
    ];

    public function category()
    {
        return $this->belongsTo(ExpenseCategory::class, 'expense_category_uid', 'uid');
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_uid', 'uid');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_uid', 'uid');
    }
}
