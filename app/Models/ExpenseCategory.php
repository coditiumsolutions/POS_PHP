<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExpenseCategory extends Model
{
    use HasFactory;

    protected $table = 'expense_categories';
    protected $primaryKey = 'uid';
    public $timestamps = false;

    protected $fillable = [
        'category_code',
        'category_name',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function expenses()
    {
        return $this->hasMany(Expense::class, 'expense_category_uid', 'uid');
    }
}
