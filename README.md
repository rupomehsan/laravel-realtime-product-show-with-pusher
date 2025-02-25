# Laravel Real-Time Broadcasting Setup Guide

This guide will walk you through setting up real-time broadcasting in Laravel using Pusher.

## 1️⃣ Install Required Dependencies

Run the following command to install Pusher:

```bash
composer require pusher/pusher-php-server
```

## 2️⃣ Signup pusher https://dashboard.pusher.com

## 2️⃣ Configure `.env` File

Open the `.env` file and set up Pusher details:

```ini
BROADCAST_DRIVER=pusher
PUSHER_APP_ID=your_app_id
PUSHER_APP_KEY=your_app_key
PUSHER_APP_SECRET=your_app_secret
PUSHER_APP_CLUSTER=ap2  # Update as per your Pusher setup
```

Clear cached configuration:

```bash
php artisan config:clear
php artisan cache:clear
```

## 3️⃣ Update `config/broadcasting.php`

Ensure `config/broadcasting.php` has the correct setup:

```php
return [
    'default' => env('BROADCAST_DRIVER', 'pusher'),
    'connections' => [
        'pusher' => [
            'driver' => 'pusher',
            'key' => env('PUSHER_APP_KEY'),
            'secret' => env('PUSHER_APP_SECRET'),
            'app_id' => env('PUSHER_APP_ID'),
            'options' => [
                'cluster' => env('PUSHER_APP_CLUSTER'),
                'useTLS' => true
            ],
        ],
        'log' => ['driver' => 'log'],
        'null' => ['driver' => 'null'],
    ],
];
```

## 5️⃣ Create a Broadcast Event

Generate an event:

```bash
php artisan make:event ProductUpdated
```

Modify `app/Events/ProductUpdated.php`:

```php
namespace App\Events;

use App\Models\Product;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class ProductUpdated implements ShouldBroadcast
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
```

## 4️⃣ Set Up Broadcasting Routes

Ensure `routes/channels.php` exists and contains:

```php
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('products', function ($user) {
    return true;
});
```

## 6️⃣ Modify `BroadcastServiceProvider.php`

Ensure `app/Providers/BroadcastServiceProvider.php` includes:

```php
use Illuminate\Support\Facades\Broadcast;

public function boot()
{
    Broadcast::routes();
    require base_path('routes/channels.php');
}
```

## 7️⃣ Update Laravel Controller

Modify `app/Http/Controllers/ProductController.php`:

```php
use App\Events\ProductUpdated;

public function addProduct()
{
    $product = Product::create([
        'name' => "Product" . rand(1, 100),
        'price' => "100",
        'description' => "Description"
    ]);
    broadcast(new ProductUpdated($product))->toOthers();
    return response()->json(['message' => 'Product added successfully!', 'product' => $product]);
}
```

## 8️⃣ Set Up Laravel Echo in Blade View

Modify `resources/views/welcome.blade.php`:

```html
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <title>Product List</title>
        <link
            href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css"
            rel="stylesheet"
        />
        <script src="https://js.pusher.com/7.0/pusher.min.js"></script>
    </head>
    <body>
        <div class="container">
            <h5 class="text-center alert alert-success mt-3">Product List</h5>
            <div
                class="row align-items-start justify-content-start border"
                id="product-list"
            >
                @foreach($products as $product)
                <div class="col-md-3 ">
                    <div class="card m-2" style="width: 15rem; ">
                        <img
                            src="https://placehold.co/600x400"
                            class="card-img-top"
                            alt="..."
                        />
                        <div class="card-body">
                            <p class="card-text">{{ $product->name }}</p>
                            <p class="card-text">{{ $product->description }}</p>
                            <h6>${{ $product->price }}</h6>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        <script>
            document.addEventListener("DOMContentLoaded", function () {
                Pusher.logToConsole = true;

                var pusher = new Pusher("{{ env('PUSHER_APP_KEY') }}", {
                    cluster: "{{ env('PUSHER_APP_CLUSTER') }}",
                    forceTLS: true,
                });

                var channel = pusher.subscribe("products");
                channel.bind("product-updated", function (data) {
                    let productList = document.getElementById("product-list");
                    let newProduct = `
                        <div class="col-md-3">
                            <div class="card m-2" style="width: 15rem;">
                                <img src="https://placehold.co/600x400" class="card-img-top" alt="Product Image">
                                <div class="card-body">
                                    <p class="card-text">${data.product.name}</p>
                                    <p class="card-text">${data.product.description}</p>
                                    <h6>$${data.product.price}</h6>
                                </div>
                            </div>
                        </div>
                    `;
                    productList.innerHTML = newProduct + productList.innerHTML;
                });
            });
        </script>
    </body>
</html>
```
