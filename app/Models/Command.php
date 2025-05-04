<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Command extends Model
{
    use HasFactory;

    /**
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'client_id',
        'livreur_id',
        'title',
        'description',
        'service_type',
        'establishment_name',
        'pickup_address',
        'delivery_address',
        'price',
        'status',
        'priority',
        'accepted_at',
        'delivered_at',
        'pickup_latitude',
        'pickup_longitude',
        'delivery_latitude',
        'delivery_longitude',
    ];

    
    protected $casts = [
        'price' => 'decimal:2',
        'accepted_at' => 'datetime',
        'delivered_at' => 'datetime',
    ];

    
    public function client(): BelongsTo
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    
    public function livreur(): BelongsTo
    {
        return $this->belongsTo(User::class, 'livreur_id');
    }

    
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    
    public function isAccepted(): bool
    {
        return $this->status === 'accepted';
    }

   
    public function isInProgress(): bool
    {
        return $this->status === 'in_progress';
    }

    
    public function isDelivered(): bool
    {
        return $this->status === 'delivered';
    }

    
    public function isCancelled(): bool
    {
        return $this->status === 'cancelled';
    }
    
    /**
     * Get the review associated with this command
     */
    public function review(): HasOne
    {
        return $this->hasOne(Review::class);
    }
}
