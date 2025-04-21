<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
        'first_name',
        'last_name',
        'email',
        'phone',
        'password',
        'role',
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
        ];
    }
    
    /**
     * Get the user's full name.
     *
     * @return string
     */
    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }
    
    /**
     * Check if the user is a client.
     *
     * @return bool
     */
    public function isClient(): bool
    {
        return $this->role === 'client';
    }
    
    /**
     * Check if the user is a delivery person.
     *
     * @return bool
     */
    public function isLivreur(): bool
    {
        return $this->role === 'livreur';
    }
    
    /**
     * Check if the user is an admin.
     *
     * @return bool
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }
    
    /**
     * Get commands created by the user as a client.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function clientCommands(): HasMany
    {
        return $this->hasMany(Command::class, 'client_id');
    }
    
    /**
     * Get commands assigned to the user as a livreur.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function livreurCommands(): HasMany
    {
        return $this->hasMany(Command::class, 'livreur_id');
    }
}
