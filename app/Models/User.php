<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * Get the primary key column name.
     *
     * @return string
     */
    public function getKeyName()
    {
        return 'user_id';
    }

    public function notecards(){
        return $this->hasMany(Notecard::class, 'user_id');
    }
    public function stacks(){
        return $this->hasMany(Stack::class, 'user_id');
    }
    public function daily_stack() {
        return $this->hasOne(DailyStack::class, 'user_id');
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'password',
        'subscription_token'
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
        'email_verified_at' => 'datetime',
        'is_unsubscribed' => 'boolean',
        'password' => 'hashed',
    ];
}
