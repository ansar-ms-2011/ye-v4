# Laravel + Vue + Vuetify Starter Kit

A modern, full-stack web application built on **Laravel 13**, **Vue 3**, **Vuetify 4**, and **Inertia.js v3**.

---

## Overview

This repository provides a robust starter kit combining the developer ergonomics of Laravel with a Vue 3 and Vuetify SPA frontend powered by Inertia.js (monolithic SPA architecture without traditional API boilerplate).

### Tech Stack

- **Backend**: PHP ^8.3 (PHP 8.4 recommended), Laravel ^13.33, Laravel Fortify ^1.40, Spatie Laravel Permission ^8.3, Laravel Wayfinder
- **Frontend**: Vue ^3.5.13, Vuetify ^4.0.7, Material Design Icons (`@mdi/font`), TypeScript ^5.2.2, Inertia.js (`@inertiajs/vue3` ^3.0.0, `@inertiajs/vite`)
- **Build & Tooling**: Vite ^8.0.0, Laravel Vite Plugin, Vite Plugin Vuetify, Composer, npm / pnpm
- **Code Quality & Formatting**: Laravel Pint (PHP), ESLint 9, Prettier, `vue-tsc` (TypeScript type checking)
- **Testing**: Pest ^4.7.8, PHPUnit, Mockery

---

## Key Entry Points

- **Backend Entry Point**: `public/index.php` -> `bootstrap/app.php`
- **Routing**: `routes/web.php`, `routes/console.php`
- **Frontend Entry Point**: `resources/js/app.ts`
- **Root Template**: `resources/views/app.blade.php`
- **Frontend Pages**: `resources/js/pages/`
- **Frontend Layouts**: `resources/js/layouts/`
- **Vite Config**: `vite.config.ts`

---

## Requirements

Ensure your development environment meets the following prerequisites:

- **PHP**: `>= 8.3` (with extensions: `ctype`, `curl`, `dom`, `fileinfo`, `filter`, `mbstring`, `openssl`, `pcre`, `pdo`, `pdo_sqlite` or `pdo_mysql`, `session`, `tokenizer`, `xml`)
- **Composer**: `>= 2.2`
- **Node.js**: `>= 20.x`
- **Package Manager**: `npm` (or `pnpm`)
- **Database**: SQLite (default), MySQL, PostgreSQL, or MariaDB

---

## Installation & Setup

### 1. Clone the Repository

```bash
git clone <repository-url>
cd ye-v4
```

### 2. Automated Setup

You can run the built-in Composer setup script:

```bash
composer run setup
```

*This command automatically installs PHP dependencies, copies `.env.example` to `.env` (if not present), generates the application key, runs migrations, installs npm packages, and builds frontend assets.*

### 3. Manual Setup (Alternative)

If you prefer step-by-step setup:

```bash
# 1. Install Composer dependencies
composer install

# 2. Setup Environment Configuration
cp .env.example .env

# 3. Generate Application Key
php artisan key:generate

# 4. Create SQLite database file (if using SQLite)
# On Linux/macOS:
touch database/database.sqlite
# On Windows PowerShell:
# New-Item -ItemType File -Path database\database.sqlite -Force

# 5. Run Database Migrations (and Seeders if needed)
php artisan migrate

# 6. Install Node dependencies
npm install

# 7. Build assets
npm run build
```

---

## Running the Application

### Development Mode

To start all services concurrently (HTTP server, queue worker, Laravel Pail log stream, and Vite dev server):

```bash
composer run dev
```

Alternatively, you can run services individually in separate terminals:

```bash
# Terminal 1: Laravel Backend Server
php artisan serve

# Terminal 2: Vite Dev Server (Hot Reload)
npm run dev

# Terminal 3: Queue Worker (optional/as needed)
php artisan queue:listen --tries=1 --timeout=0

# Terminal 4: Real-time Laravel Log Tail (optional)
php artisan pail --timeout=0
```

Access the application in your browser at: `http://localhost:8000` (or the URL specified in your `.env`).

### Production Build

```bash
npm run build
```

*(For SSR support: `npm run build:ssr`)*

---

## Available Scripts

### Composer Scripts (`composer.json`)

| Command | Description |
|---|---|
| `composer run setup` | Full initial project setup (deps, env, keys, migrations, build) |
| `composer run dev` | Runs backend, queue worker, logs (`pail`), and Vite concurrently |
| `composer run lint` | Auto-fixes PHP code style using Laravel Pint |
| `composer run lint:check` | Checks PHP code style without modifying files |
| `composer run test` | Clears config cache, checks PHP code style, and executes Pest/PHPUnit tests |
| `composer run ci:check` | Full CI verification (linting, Prettier, TypeScript, Pest tests) |

### NPM Scripts (`package.json`)

| Command | Description |
|---|---|
| `npm run dev` | Starts the Vite development server with HMR |
| `npm run build` | Builds frontend assets for production |
| `npm run build:ssr` | Builds client and SSR bundles |
| `npm run lint` | Runs ESLint and auto-fixes issues |
| `npm run lint:check` | Runs ESLint checks |
| `npm run format` | Auto-formats code in `resources/` with Prettier |
| `npm run format:check` | Verifies code formatting with Prettier |
| `npm run types:check` | Runs TypeScript type checking via `vue-tsc` |

---

## Environment Variables

Key configuration variables defined in `.env.example`:

| Variable | Description | Default / Example |
|---|---|---|
| `APP_NAME` | Name of the application | `Laravel` |
| `APP_ENV` | Application environment (`local`, `production`, `testing`) | `local` |
| `APP_KEY` | Application encryption key | Auto-generated via `php artisan key:generate` |
| `APP_DEBUG` | Enable/disable debug mode | `true` |
| `APP_URL` | Root URL of the application | `http://localhost` |
| `DB_CONNECTION` | Database driver (`sqlite`, `mysql`, `pgsql`) | `sqlite` |
| `DB_HOST` | Database host (when using MySQL/PostgreSQL) | `127.0.0.1` |
| `DB_PORT` | Database port | `3306` |
| `DB_DATABASE` | Database name or SQLite file path | `laravel` / `database/database.sqlite` |
| `DB_USERNAME` | Database username | `root` |
| `DB_PASSWORD` | Database password | `""` |
| `SESSION_DRIVER` | Session storage driver | `database` |
| `QUEUE_CONNECTION` | Queue system driver | `database` |
| `CACHE_STORE` | Cache storage driver | `database` |
| `MAIL_MAILER` | Mail sending driver (`log`, `smtp`, etc.) | `log` |
| `VITE_APP_NAME` | Application name exposed to frontend via Vite | `"${APP_NAME}"` |

> **Note**: For production deployments, ensure `APP_DEBUG=false`, configure appropriate caching and session stores (e.g. Redis), and provide valid mail/database credentials.

---

## Testing & Quality Assurance

This project uses **Pest** for backend unit and feature testing, **Laravel Pint** for PHP styling, **ESLint** and **Prettier** for frontend linting/formatting, and **vue-tsc** for TypeScript verification.

### Run All Tests

```bash
php artisan test
# or
vendor/bin/pest
# or
composer run test
```

### Run a Specific Test / Filter

```bash
php artisan test --filter=ExampleTest
# or
vendor/bin/pest tests/Feature/ExampleTest.php
```

### Run Static Analysis & Linters

```bash
# Check PHP code style
composer run lint:check

# Fix PHP code style
composer run lint

# Check TypeScript types
npm run types:check

# Lint and check JS/Vue code
npm run lint:check

# Run complete CI verification suite
composer run ci:check
```

---

## Project Structure

```text
├── app/                  # Core application PHP code (Models, Controllers, Middleware, Helpers)
│   ├── Actions/          # Fortify authentication and domain actions
│   ├── helpers/          # Application helper functions
│   ├── Http/Controllers # Web and API controllers
│   ├── Models/           # Eloquent data models
│   └── Providers/        # Service providers
├── bootstrap/            # App initialization, routing & middleware configuration
├── config/               # Application configuration files
├── database/             # Migrations, seeders, and factories
│   ├── factories/        # Model factory definitions
│   ├── migrations/       # Database schema migrations
│   └── seeders/          # Database seeders
├── public/               # Web server public document root
├── resources/            # Frontend assets and views
│   ├── css/              # Global CSS and styling
│   ├── js/               # Vue 3 application source code
│   │   ├── assets/       # Static frontend assets (images, icons)
│   │   ├── components/   # Reusable Vue components
│   │   ├── composables/  # Vue composable functions
│   │   ├── layouts/      # Application page layouts
│   │   ├── pages/        # Inertia page components
│   │   ├── plugins/      # Vuetify and other plugin configurations
│   │   └── types/        # TypeScript interfaces and type definitions
│   └── views/            # Blade view templates (e.g., app.blade.php)
├── routes/               # Route definitions (web.php, console.php)
├── storage/              # Logs, file cache, and uploaded files
├── tests/                # Pest / PHPUnit test suites
│   ├── Feature/          # Feature and integration tests
│   └── Unit/             # Unit tests
├── artisan               # Laravel command-line interface
├── composer.json         # PHP dependencies and scripts
├── package.json          # JavaScript/Node dependencies and scripts
├── pint.json             # Laravel Pint code formatter configuration
├── tsconfig.json         # TypeScript configuration
└── vite.config.ts        # Vite build tool configuration
```

---

## TODOs / Roadmap

- [ ] Configure production deployment pipelines (e.g., Laravel Cloud, Docker/Kubernetes).
- [ ] Add end-to-end (E2E) testing suite (e.g., Playwright / Cypress) if required.
- [ ] Add project-specific custom business domain documentation.

---

## License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
