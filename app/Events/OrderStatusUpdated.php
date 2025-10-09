<?php

namespace App\Events;

use App\Models\orders;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OrderStatusUpdated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public orders $order) {}

    public function broadcastOn(): Channel
    {
        return new Channel('kitchen');
    }

    public function broadcastAs(): string
    {
        return 'order.status.updated';
    }

    public function broadcastWith(): array
    {
        return [
            'order_id' => $this->order->id,
            'table_number' => $this->order->linked_table->table_number ?? null,
            'status' => $this->order->status,
        ];
    }
}
