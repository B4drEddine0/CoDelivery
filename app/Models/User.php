<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'phone',
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
    
    
    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }
    
    
    public function isClient(): bool
    {
        return $this->role === 'client';
    }
    
    
    public function isLivreur(): bool
    {
        return $this->role === 'livreur';
    }
    
    
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }
    
    
    public function clientCommands(): HasMany
    {
        return $this->hasMany(Command::class, 'client_id');
    }
    
    
    public function livreurCommands(): HasMany
    {
        return $this->hasMany(Command::class, 'livreur_id');
    }
    
    
    public function driverCommands(): HasMany
    {
        return $this->livreurCommands();
    }
}
