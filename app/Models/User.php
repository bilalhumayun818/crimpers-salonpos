<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'staff_role_id',
        'two_factor_code',
        'two_factor_expires_at',
        'google2fa_secret',
        'branch_id',
    ];

    public function staffRole()
    {
        return $this->belongsTo(StaffRole::class, 'staff_role_id');
    }

    /**
     * Load the staff role bypassing any branch global scopes.
     * This ensures cross-branch roles are always accessible.
     */
    public function staffRoleWithoutScope()
    {
        if (!$this->staff_role_id) return null;
        return \App\Models\StaffRole::withoutGlobalScopes()->find($this->staff_role_id);
    }

    public function isAdmin()
    {
        return $this->role === 'admin' || $this->email === 'safullahzafar@gmail.com';
    }

    public function hasPermission($module, $action = 'view')
    {
        if ($this->isAdmin()) {
            return true;
        }

        // Use withoutGlobalScopes to find the role regardless of active branch session
        $role = $this->staffRoleWithoutScope();

        if (!$role) {
            return false;
        }

        $permissions = $role->permissions;
        if (!is_array($permissions)) {
            return false;
        }

        return isset($permissions[$module][$action]) && $permissions[$module][$action] == '1';
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'name' => \App\Casts\FlexibleEncryption::class,
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'two_factor_expires_at' => 'datetime',
        ];
    }

    public function generateTwoFactorCode()
    {
        $this->timestamps = false;
        $this->two_factor_code = rand(100000, 999999);
        $this->two_factor_expires_at = now()->addMinutes(10);
        $this->save();
    }

    public function resetTwoFactorCode()
    {
        $this->timestamps = false;
        $this->two_factor_code = null;
        $this->two_factor_expires_at = null;
        $this->save();
    }
}
