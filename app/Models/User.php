<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Mail\SuperAdminResetPasswordMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Contracts\Auth\MustVerifyEmail;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'first_name',
        'last_name',
        'email',
        'password',
        'phone',
        'profile_picture',
        'verification_token',
        'role',
        'fcm_token',
        'device_id',
        'device_type',
        'status',
        'company_name',
        'service_specialisation',
        'service_type',
        'availability_preferences',
        'coverage',
        'address',
        'latitude',
        'longitude'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at'         => 'datetime',
        'password'                  => 'hashed',
        'availability_preferences'  => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'house_owner_id');
    }

    public function sendPasswordResetNotification($token)
    {
        if ($this->role === 'superadmin') {
            $resetUrl = url(route('superadmin.password.reset', [
                'token' => $token,
                'email' => $this->email,
            ], false));

            Mail::to($this->email)->send(new SuperAdminResetPasswordMail($resetUrl));
        } else {
            parent::sendPasswordResetNotification($token);
        }
    }

    public function serviceProviders()
    {
        return $this->hasMany(ServiceProvider::class);
    }

    public function specialization()
    {
        return $this->belongsTo(Specialization::class, 'service_specialisation');
    }
}
