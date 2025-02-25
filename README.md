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
