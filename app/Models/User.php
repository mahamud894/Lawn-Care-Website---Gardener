<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\Booking;
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
        'name',
        'email',
        'phone',
        'password',
        'isAdmin',
        'isGardener',
        'isAdminApproved',
        'isBlocked',
        'google_id',
    ];

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
            'isAdmin' => 'boolean',
            'isGardener' => 'boolean',
            'isAdminApproved' => 'boolean',
            'isBlocked' => 'boolean',
        ];
    }
    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    /** Team profile for gardener accounts. */
    public function teamMember()
    {
        return $this->hasOne(Team::class);
    }

    /** Where this user should land after logging in. */
    public function homeRoute(): string
    {
        if ($this->isAdmin) {
            return route('viewAdminPanel');
        }

        return $this->isGardener ? route('gardener.dashboard') : route('home');
    }
}
