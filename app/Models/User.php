<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'users';
    protected $primaryKey = 'uid';
    public $timestamps = false;

    const CREATED_AT = 'created_date';
    const UPDATED_AT = 'updated_date';

    protected $fillable = [
        'user_name',
        'full_name',
        'password_hash',
        'mobile_no',
        'email',
        'role_name',
        'user_type',
        'terminal_name',
        'branch',
        'is_active',
        'last_login_date',
    ];

    protected $hidden = [
        'password_hash',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'last_login_date' => 'datetime',
        'created_date' => 'datetime',
        'updated_date' => 'datetime',
    ];

    public function getAuthPassword()
    {
        return $this->password_hash;
    }

    public function isAdmin()
    {
        return $this->role_name === 'Administrator';
    }

    public function isManager()
    {
        return $this->role_name === 'Manager';
    }

    public function isCashier()
    {
        return $this->role_name === 'Cashier';
    }

    public function hasRole($roles)
    {
        $roles = is_array($roles) ? $roles : [$roles];
        return in_array($this->role_name, $roles);
    }
}
