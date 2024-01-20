# Laravel Product Management

This repository contains the Laravel backend code for a Product Management web application.

## Project Setup

1. Clone the repository:

   ```bash
   git clone <https://github.com/omaranajar/laravel-vue-product-management.git>
   cd laravel-product-management

2. Install Laravel dependencies:

   ```bash
   composer install

3. Create a copy of the .env file:

   ```bash
   cp .env.example .env

4. Configure your .env file with your database settings.

5. Generate the application key:

   ```bash
   php artisan key:generate

6. Run migrations:

    ```bash
   php artisan migrate

7. Create a symbolic link in Laravel:

   ```bash
   php artisan storage:link

7. Start the development server:

   ```bash
   php artisan serve

The application should now be accessible at http://localhost:8000.

For more details on Laravel, please refer to the Laravel Documentation.
