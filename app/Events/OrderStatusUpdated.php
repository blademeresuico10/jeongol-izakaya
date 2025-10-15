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

    public function broadcastOn(): array
    {
        return [
            new Channel('kitchen'),
            new Channel('waitstaff'), 
        ];
    }

    public function broadcastAs(): string
    {
        return 'order.status.updated';
    }

    public function broadcastWith(): array
    {
        $tableId = null;
        if ($this->order->reservation) {
            $tableId = $this->order->reservation->table_id;
        } elseif ($this->order->walkin) {
            $tableId = $this->order->walkin->table_id;
        }

        return [
            'order_id' => $this->order->id,
            'table_id' => $tableId,
            'table_number' => $this->order->linked_table->table_number ?? null,
            'status' => $this->order->status,
        ];
    }
}