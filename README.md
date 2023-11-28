# UserAPI - Laravel REST API with Sanctum

This is an example of a REST API using auth tokens with Laravel Sanctum

## Setup

1. Clone the project

```
git clone https://github.com/Edgaras318/UserAPI.git
```

2. Enter the folder and install project dependencies

```
composer install
```

3. Copy the example environment file, setup a local database and poplulate the DB fields

```
cp .env.example .env
```

5. Run the database migrations (valid DB connection required)

```
php artisan migrate
```

6. Start the local development server

```
php artisan serve
```

## Routes

```
# Public

POST   /api/register
@body: first_name, last_name, email, password, password_confirmation, ?address


# Protected

GET   /api/users

PUT   /api/users/:id
@body: first_name, last_name, email, password, ?password_confirmation, ?address

DELETE  /api/users/:id
```

## Testing

The project includes some unit and feature tests, you can run them with:

```

php artisan test

```
