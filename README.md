# How to Run a Laravel Project from a Directory

## Prerequisites

Before running the Laravel project, ensure you have the following installed:

-   PHP (>=8.0 recommended)
-   Composer
-   MySQL or any preferred database
-   A web server (Apache, Nginx, or Laravel's built-in server)

## Steps to Run the Laravel Project

### 1.Open the terminal Navigate to the Project Directory

```sh
git clone https://github.com/rupomehsan/laravel-realtime-product-show-with-pusher.git
```

```sh
cd laravel-realtime-product-show-with-pusher
```

### 2. Install Dependencies

```sh
composer install
```

### 3. Copy `.env` File & Generate Application Key

```sh
cp .env.example .env
php artisan key:generate
```

### 4. Configure the `.env` File

Edit the `.env` file to set up database credentials and other configurations.

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database_name
DB_USERNAME=your_database_user
DB_PASSWORD=your_database_password


BROADCAST_DRIVER=pusher
PUSHER_APP_ID=1947953
PUSHER_APP_KEY=36ba5d689f573cef7020
PUSHER_APP_SECRET=8288ae53644d254baf94
PUSHER_APP_CLUSTER=ap2
```

### 5. Run Database Migrations & Seeders (Optional)

```sh
php artisan migrate --seed
```

### 6. Start the Development Server

```sh
php artisan serve
```

This will start the application at `http://127.0.0.1:8000/`.

# A brief explanation of how Pusher is integrated and how real-time updates are handled.

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

## Troubleshooting

-   If you get permission issues, run:
    ```sh
    chmod -R 775 storage bootstrap/cache
    ```
-   If `.env` changes don't apply, run:
    ```sh
    php artisan config:clear
    ```

Now your Laravel project should be running successfully!
