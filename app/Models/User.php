<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'full_name',
        'phone',
        'email',
        'password',
        'role',
        'avatar',
        'active',
        'key_active',
        'key_reset_password',
        'reset_password_at',
        'google_id', 
    ];

    const ROLE_ADMIN = 'admin';
    const ROLE_USER = 'user';
    const ROLE_STAFF = 'staff';

    const ACTIVE_YES = 1;
    const ACTIVE_NO = 0;


    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function isActive(): bool
    {
        return $this->active === self::ACTIVE_YES;
    }

    public function addresses()
    {
        return $this->hasMany(Address::class);
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
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
