# Web App with Notifications

A Laravel web application for managing users and delivering on-screen notifications. Users can view their notifications, track unread messages, mark notifications as read, and manage their notification preferences and contact information. The application also provides notification management features for creating, filtering, and viewing notifications targeted at specific users or all users.

## Requirements

The application was developed and tested with:

- PHP 8.5.10
- Laravel 13.31.0
- Composer 2.10.3
- PostgreSQL 18.6
- Node.js 20.20.0
- npm 10.8.2
- Livewire 4.4.4
- Tailwind CSS 4.3.3
- Vite 8.2.2

PHP 8.3 or newer is required by the installed Laravel version.

## Installation

### 1. Clone the repository

Clone the repository and move into the project directory:

```bash
git clone <repository-url>
cd rin2-app
```

### 2. Install PHP dependencies

```bash
composer install
```

### 3. Install frontend dependencies

```bash
npm install
```

### 4. Create the environment file

Create the local environment file from the provided template:

```bash
cp .env.example .env
```

Generate the Laravel application key:

```bash
php artisan key:generate
```

### 5. Create and configure the PostgreSQL database

Create a PostgreSQL database named `rin2`.

The PostgreSQL connection settings are provided below. Make sure the corresponding values are set in `.env`:

```env
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=rin2
DB_USERNAME=your_database_username
DB_PASSWORD=your_database_password
```

Replace `your_database_username` and `your_database_password` with the credentials of your local PostgreSQL installation.

### 6. Run the migrations and seed the users

Run:

```bash
php artisan migrate --seed
```

This creates the database tables and seeds the initial users.

The seeded users include the following test account:

```text
Email:    test@example.com
Password: password
```

This account can be used to log into the application.

### 7. Seed example notifications

Example notifications are provided separately so that the notification functionality can be tested immediately.

Run:

```bash
php artisan db:seed --class=NotificationSeeder
```

This creates:

- one notification for a specific user
- one global notification available to all users

The seeded notifications expire seven days after they are created.

## Running the application

Open two terminals in the project directory.

### Terminal 1 – Laravel

Start the Laravel development server:

```bash
php artisan serve
```

The application will be available at:

```text
http://127.0.0.1:8000
```

### Terminal 2 – Frontend assets

Start the Vite development server:

```bash
npm run dev
```

This compiles the frontend assets and automatically updates them when frontend files are changed.

## Logging in

Open the application in a browser:

```text
http://127.0.0.1:8000
```

Use the seeded test account:

```text
Email:    test@example.com
Password: password
```

## Phone number validation

The application uses `giggsey/libphonenumber-for-php` version 9.0.38 to validate international phone numbers and determine whether a number is classified as a mobile number.

No external API key or paid phone verification service is required to run the application.

## Documentation

Additional project documentation is available in the `docs/` directory:

- [`architecture.md`](docs/architecture.md) — application structure and key design decisions.
- [`database-erd.pdf`](docs/database-erd.pdf) — entity-relationship diagram of the application database.