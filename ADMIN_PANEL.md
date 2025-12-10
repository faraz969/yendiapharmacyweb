# Custom Admin Panel - Pharmacy Management System

## Overview

A beautiful, modern custom admin panel built with Laravel Blade views, Bootstrap 5, and Font Awesome icons. This replaces Filament and provides a more traditional, customizable admin interface.

## Features

- ✅ Modern, responsive design with sidebar navigation
- ✅ Role-based access control (admin, manager, staff)
- ✅ Dashboard with statistics and recent orders
- ✅ Category management (CRUD)
- ✅ Product management (ready for implementation)
- ✅ Order management (ready for implementation)
- ✅ User management
- ✅ Beautiful login page

## Access

**Admin Login URL:** `http://localhost:8000/admin/login`

**Default Credentials:**
- Email: `admin@pharmacy.com`
- Password: `password`

## Structure

```
app/Http/Controllers/Admin/
├── DashboardController.php
├── CategoryController.php
├── ProductController.php
├── VendorController.php
├── PurchaseOrderController.php
├── OrderController.php
├── PrescriptionController.php
├── DeliveryZoneController.php
└── Auth/
    └── LoginController.php

resources/views/admin/
├── layouts/
│   └── app.blade.php          # Main admin layout with sidebar
├── auth/
│   └── login.blade.php         # Login page
├── dashboard.blade.php         # Dashboard view
├── categories/
│   ├── index.blade.php
│   ├── create.blade.php
│   └── edit.blade.php
└── [other modules...]

routes/
└── admin.php                   # Admin routes
```

## Routes

All admin routes are prefixed with `/admin` and protected by `auth` and `admin` middleware.

### Main Routes
- `GET /admin/login` - Login page
- `POST /admin/login` - Login handler
- `POST /admin/logout` - Logout
- `GET /admin/dashboard` - Dashboard

### Resource Routes
- `GET /admin/categories` - List categories
- `GET /admin/categories/create` - Create category form
- `POST /admin/categories` - Store category
- `GET /admin/categories/{id}/edit` - Edit category form
- `PUT /admin/categories/{id}` - Update category
- `DELETE /admin/categories/{id}` - Delete category

Similar routes exist for:
- Products (`/admin/products`)
- Vendors (`/admin/vendors`)
- Purchase Orders (`/admin/purchase-orders`)
- Orders (`/admin/orders`)
- Prescriptions (`/admin/prescriptions`)
- Delivery Zones (`/admin/delivery-zones`)
- Users (`/admin/users`)

## Middleware

**AdminMiddleware** - Checks if user is authenticated and has admin/manager/staff role.

Located at: `app/Http/Middleware/AdminMiddleware.php`

## Design Features

### Sidebar Navigation
- Fixed sidebar with collapsible menu
- Active route highlighting
- Icon-based navigation
- Responsive (hides on mobile)

### Dashboard
- Statistics cards with gradients
- Recent orders table
- Quick access to main features

### Forms
- Bootstrap 5 styling
- Validation error display
- Image upload support
- Switch toggles for boolean fields

## Next Steps

1. **Complete Product CRUD** - Implement ProductController methods and views
2. **Complete Order Management** - Add order approval, packing, delivery workflows
3. **Add Reports** - Create reporting views and controllers
4. **Add Inventory Management** - Batch tracking, stock levels
5. **Add User Management** - Full user CRUD with role assignment

## Customization

### Changing Colors
Edit CSS variables in `resources/views/admin/layouts/app.blade.php`:
```css
:root {
    --sidebar-width: 250px;
    --sidebar-bg: #2c3e50;
    --sidebar-active: #3498db;
    --header-bg: #ffffff;
}
```

### Adding Menu Items
Edit the sidebar menu in `resources/views/admin/layouts/app.blade.php`:
```blade
<li>
    <a href="{{ route('admin.your-route') }}" class="{{ request()->routeIs('admin.your-route.*') ? 'active' : '' }}">
        <i class="fas fa-icon"></i> Menu Item
    </a>
</li>
```

## Notes

- Filament is still installed but not used. You can remove it if desired.
- All file uploads go to `storage/app/public/` (make sure storage link exists)
- Images are stored in subdirectories like `categories/`, `products/`
- The admin panel uses Laravel's built-in authentication

