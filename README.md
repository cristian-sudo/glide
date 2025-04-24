# Cristian Plop MAC Address Vendor Lookup API Test

A Laravel-based API service that allows you to look up vendor information for MAC addresses. 

## Features

- Look up vendor information for a single MAC address
- Bulk lookup for multiple MAC addresses
- Support for multiple MAC address formats
- Case-insensitive MAC address handling
- Automatic deduplication of MAC addresses in bulk lookups
- Comprehensive error handling and validation

## Requirements

- PHP 8.1 or higher
- Composer
- MySQL 5.7+ or SQLite
- Laravel 10.x

## Installation

1. Clone the repository:
```bash
git clone https://github.com/cristian-sudo/glide.git
cd glide
```

2. Install PHP dependencies:
```bash
composer install
```

3. Create a copy of the environment file:
```bash
cp .env.example .env
```

4. Generate an application key:
```bash
php artisan key:generate
```

5. Configure your database in the `.env` file:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=glide
DB_USERNAME=
DB_PASSWORD=
```

6. Run the database migrations:
```bash
php artisan migrate
```

## Run the tests
Use `make test` to run all the tests.