<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class LivreurLocationUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * The command ID.
     *
     * @var int
     */
    public $commandId;

    /**
     * The livreur's latitude.
     *
     * @var float
     */
    public $latitude;

    /**
     * The livreur's longitude.
     *
     * @var float
     */
    public $longitude;

    /**
     * The estimated delivery time in minutes.
     *
     * @var int|null
     */
    public $estimatedTime;

    /**
     * Create a new event instance.
     */
    public function __construct(int $commandId, float $latitude, float $longitude, ?int $estimatedTime = null)
    {
        $this->commandId = $commandId;
        $this->latitude = $latitude;
        $this->longitude = $longitude;
        $this->estimatedTime = $estimatedTime;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('command-' . $this->commandId),
        ];
    }

    /**
     * The event's broadcast name.
     *
     * @return string
     */
    public function broadcastAs(): string
    {
        return 'livreur-location-updated';
    }
}
