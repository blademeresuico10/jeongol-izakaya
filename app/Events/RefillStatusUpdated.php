<?php

namespace App\Events;

use App\Models\OrderRefill;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class RefillStatusUpdated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public OrderRefill $refill) {}

    public function broadcastOn(): array
    {
        return [
            new Channel('kitchen'),
            new Channel('waitstaff'),
        ];
    }

    public function broadcastAs(): string
    {
        return 'refill.status.updated';
    }

    public function broadcastWith(): array
    {
        $tableId = null;
        if ($this->refill->order->reservation) {
            $tableId = $this->refill->order->reservation->table_id;
        } elseif ($this->refill->order->walkin) {
            $tableId = $this->refill->order->walkin->table_id;
        }

        return [
            'refill_id' => $this->refill->id,
            'order_id' => $this->refill->order_id,
            'table_id' => $tableId,
            'ingredient_name' => $this->refill->ingredient->name ?? 'Unknown',
            'status' => $this->refill->status,
        ];
    }
}