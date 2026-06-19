# Jeltro — Custom Apparel E-Commerce

A full-stack e-commerce web application for a custom apparel studio, built with Laravel 12 and PHP 8.4. Portfolio project showcasing end-to-end web development including storefront, cart, checkout, account management, and an admin panel.

---

## Tech Stack

- **Backend:** Laravel 12, PHP 8.4
- **Database:** SQLite
- **Frontend:** Blade Templates, Vanilla JS
- **Styling:** Custom CSS (no frameworks)
- **Auth:** Laravel Breeze
- **Storage:** Laravel Storage (local)

---

## Features

### Storefront
- Product catalog with Men / Women gender filtering
- Product detail page with image gallery, size & color variants, arrow key navigation
- Save / bookmark products (heart button, synced to account)
- Search page with category browsing

### Cart & Checkout
- Persistent session cart with quantity controls
- Custom design upload (art file or typed text) per product
- Stock validation at checkout
- Free shipping on orders over $100
- Order placement with saved address selection

### Account
- Register / Login / Logout
- Profile management — name, email, gender, avatar photo
- Saved addresses with default selection
- Order history with live status tracking
- Saved / wishlisted products
- Order cancellation requests
- Real-time order notifications (bell icon dropdown)

### Admin Panel
- Product management — create, edit, archive, restore, permanent delete
- Multi-image gallery uploads per product
- Order management with enforced status transitions
  - Pending → On Delivery → Completed
  - Pending / On Delivery → Cancelled
- Cancel request approvals / rejections
- Pending order count badges in sidebar

---

## Local Setup

```bash
# Clone the repo
git clone https://github.com/JJayDev-05/Jeltro.git
cd Jeltro

# Install dependencies
composer install

# Environment setup
cp .env.example .env
php artisan key:generate

# Database
touch database/database.sqlite
php artisan migrate

# Storage symlink
php artisan storage:link

# Start server
php artisan serve
```

Visit `http://localhost:8000`

---

## Admin Access

After registering an account, promote it to admin via tinker:

```bash
php artisan tinker
>>> \App\Models\User::where('email', 'your@email.com')->update(['is_admin' => true]);
```

Then visit `/admin` to access the panel.

---

## Deployment

Deployed on [Railway](https://railway.app) using Nixpacks with automatic PHP 8.3 detection.

---

## Author

**Joebert Jay Jimena**
[GitHub](https://github.com/JJayDev-05)
