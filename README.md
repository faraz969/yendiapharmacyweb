# Pharmacy Backend - Laravel API

Laravel backend API for the pharmacy e-commerce platform with admin panel and role management.

## Features

- Laravel 8.x
- Filament Admin Panel (v1)
- Spatie Laravel Permission for role management
- RESTful API ready
- User management with roles

## Installation

### Prerequisites

- PHP >= 7.4
- Composer
- MySQL/PostgreSQL
- Node.js & NPM (for assets)

### Setup

1. Install dependencies:
```bash
composer install
```

2. Copy environment file:
```bash
cp .env.example .env
```

3. Generate application key:
```bash
php artisan key:generate
```

4. Configure database in `.env`:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=pharmacy_db
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

5. Run migrations:
```bash
php artisan migrate
```

6. Seed admin user:
```bash
php artisan db:seed
```

## Admin Panel

Access the Filament admin panel at: `http://localhost:8000/admin`

**Default Admin Credentials:**
- Email: `admin@pharmacy.com`
- Password: `password`

## Roles

The system comes with three default roles:
- **admin** - Full system access
- **manager** - Management access
- **staff** - Staff access

## API Endpoints

API endpoints will be available at: `http://localhost:8000/api`

## Project Structure

```
app/
├── Filament/
│   └── Resources/      # Filament admin resources
├── Models/             # Eloquent models
├── Http/
│   ├── Controllers/    # API controllers
│   └── Middleware/     # Custom middleware
database/
├── migrations/        # Database migrations
└── seeders/           # Database seeders
```

## User Management

Users can be managed through the Filament admin panel. Roles can be assigned to users, and permissions can be managed through Spatie Permission.

## Next Steps

1. Create API controllers for products, orders, etc.
2. Set up API authentication (Sanctum)
3. Create additional Filament resources
4. Implement business logic
5. Add API documentation

## Security Notes

- Change the default admin password immediately
- Use strong passwords in production
- Configure proper CORS settings
- Enable HTTPS in production
- Review and configure file permissions
