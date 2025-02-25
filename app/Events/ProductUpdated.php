<?php

namespace App\Events;

namespace App\Events;

use App\Models\Product;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;

class ProductUpdated implements ShouldBroadcastNow
{
    use InteractsWithSockets, SerializesModels;

    public $product;

    public function __construct(Product $product)
    {
        $this->product = $product;
    }

    public function broadcastOn()
    {
        return new Channel('products');
    }

    public function broadcastAs()
    {
        return 'product-updated';
    }
}
