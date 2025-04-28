<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Command extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
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
        'livreur_latitude',
        'livreur_longitude',
        'client_latitude',
        'client_longitude',
        'livreur_location_updated_at',
        'client_location_updated_at',
        'location_updated_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'price' => 'decimal:2',
        'accepted_at' => 'datetime',
        'delivered_at' => 'datetime',
        'livreur_location_updated_at' => 'datetime',
        'client_location_updated_at' => 'datetime',
        'location_updated_at' => 'datetime',
    ];

    /**
     * Get the client that owns the command.
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    /**
     * Get the livreur that is assigned to the command.
     */
    public function livreur(): BelongsTo
    {
        return $this->belongsTo(User::class, 'livreur_id');
    }

    /**
     * Check if the command is pending.
     */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Check if the command is accepted.
     */
    public function isAccepted(): bool
    {
        return $this->status === 'accepted';
    }

    /**
     * Check if the command is in progress.
     */
    public function isInProgress(): bool
    {
        return $this->status === 'in_progress';
    }

    /**
     * Check if the command is delivered.
     */
    public function isDelivered(): bool
    {
        return $this->status === 'delivered';
    }

    /**
     * Check if the command is cancelled.
     */
    public function isCancelled(): bool
    {
        return $this->status === 'cancelled';
    }
    

}
