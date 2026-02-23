# School ERP - Main Branch

This is the main branch. All development work happens in the **Feature** branch.

## Branches

- **main** - Production-ready code (clean)
- **Feature** - Active development branch with full codebase

## Usage

To work with the codebase, checkout the Feature branch:

```bash
git checkout Feature
```

## Setup

```bash
# Checkout Feature branch
git checkout Feature

# Install dependencies
composer install
npm install

# Setup environment
cp .env.example .env
php artisan key:generate

# Run migrations
php artisan migrate --seed

# Start server
php artisan serve
```

---

For full documentation, see the Feature branch.
