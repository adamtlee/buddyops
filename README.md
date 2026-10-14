# Buddy Ops

Buddy Ops is a dog-care management application for keeping a dog's profile and daily care history in one place. The Laravel app provides an administrative interface through Filament and a versioned, authenticated JSON API.

## Product features

- Manage dogs and their profiles, including breed, birthdate, sex, weight, allergies, and notes.
- Track feedings, walks, play sessions, vet visits, health flags, and recurring schedules.
- Associate document metadata with a dog.
- Browse and manage records in the Filament admin panel at `/admin`.
- Read user-owned dog and care data through the `/api/v1` API. API data is scoped to the authenticated user's dogs.

## Requirements

- PHP 8.3 or later
- Composer
- Node.js and npm
- SQLite (the repository's default database configuration) or another database supported by Laravel

## Setup and installation

1. For the default local SQLite setup, install dependencies, create the environment file, generate the application key, run migrations, and build frontend assets:

   ```sh
   composer run setup
   ```

   The setup script copies `.env.example` to `.env` if needed. The example configuration uses SQLite. If using another database, first copy `.env.example` to `.env`, configure the database settings, then run `composer install`, `php artisan key:generate`, `php artisan migrate`, `npm install --ignore-scripts`, and `npm run build` individually.

2. Seed the local demo account and dog-care records (optional):

   ```sh
   php artisan db:seed
   ```

   Demo seeding is restricted to the `local` environment. It creates or reuses `test@example.com` and adds two demo dogs with sample activity. The factory password is `password`; change credentials before using any shared or deployed environment. Document entries are metadata placeholders and do not refer to uploaded files.

3. Start the local web server:

   ```sh
   php artisan serve
   ```

4. Open the app at `http://localhost:8000`. Visit `http://localhost:8000/admin` to sign in to Filament. With demo data seeded, use `test@example.com` / `password` for local development only.

For frontend hot reload during development, run `npm run dev` in a separate terminal. For a production asset build, run `npm run build`.

## API

The API base path is `/api/v1`. It supports Sanctum bearer tokens and authenticated same-origin browser sessions. For browser session access, sign in at `/admin` and call API paths from the same host and port, for example `http://localhost:8000/api/v1/dogs`. For non-browser clients, obtain a bearer token from `POST /api/v1/auth/token` using account credentials and a `device_name`.

Available endpoints include:

| Method | Endpoint | Purpose |
| --- | --- | --- |
| `POST` | `/api/v1/auth/token` | Issue a bearer token |
| `DELETE` | `/api/v1/auth/token` | Revoke the current bearer token |
| `GET` | `/api/v1/ping` | Confirm API authentication |
| `GET` | `/api/v1/me` | Get the authenticated account |
| `GET` | `/api/v1/dogs` | List the authenticated user's dogs |
| `GET` | `/api/v1/dogs/{dog}` | Get one owned dog |
| `GET` | `/api/v1/dogs/{dog}/{resource}` | Get feeding, walk, play, vet, health, schedule, or document records |

List endpoints are paginated; `per_page` can be set from 1 to 100. Requests must be authenticated. A dog belonging to another user is not exposed. See [docs/api.md](docs/api.md) for request examples, response behavior, and browser-session notes.

## Tests

Run the PHPUnit suite with:

```sh
php artisan test
```

## Main technologies

- PHP 8.3+, Laravel 13
- Filament 5 admin panel
- Laravel Sanctum 4 API authentication
- SQLite by default
- Vite and Tailwind CSS 4 for frontend assets
