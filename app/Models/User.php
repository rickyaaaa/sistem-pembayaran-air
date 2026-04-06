<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * @property int $id
 * @property string $username
 * @property string $name
 * @property string $password
 * @property string $role  'admin' | 'pengurus'
 * @property string|null $remember_token
 */
class User extends Authenticatable
{
    use HasFactory, Notifiable;

    const ROLE_ADMIN = 'admin';
    const ROLE_PENGURUS = 'pengurus';

    protected $fillable = [
        'username',
        'name',
        'password',
        'role',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }

    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }

    public function isPengurus(): bool
    {
        return $this->role === self::ROLE_PENGURUS;
    }

    public function isStaff(): bool
    {
        // Returns true for both admin and pengurus (any authenticated staff)
        return in_array($this->role, [self::ROLE_ADMIN, self::ROLE_PENGURUS]);
    }

    public function changeRequests()
    {
        return $this->hasMany(ChangeRequest::class, 'requested_by');
    }
}
