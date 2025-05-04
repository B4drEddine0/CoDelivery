<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Review extends Model
{
    protected $fillable = [
        'user_id',
        'command_id',
        'comment',
        'rating',
    ];

    /**
     * Get the user who left the review
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the command that was reviewed
     */
    public function command(): BelongsTo
    {
        return $this->belongsTo(Command::class);
    }
}
