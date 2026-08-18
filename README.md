# FIINWAY — India's Marketplace

> **Buy & Sell New and Pre-Owned Products** — Electronics, Fashion, Furniture, Vehicles, and more.

A full-featured Laravel 11 multi-vendor marketplace platform inspired by Flipkart, built with Blade, Alpine.js, Tailwind CSS v4, and Razorpay payments.

---

## ✨ Features

| Module | Highlights |
|---|---|
| 🛒 **Buyer** | Browse, search, filter, wishlist, cart, checkout, order tracking, reviews, returns |
| 🏪 **Seller** | Product listing (new/used), image upload, earnings dashboard, order management |
| 🛡️ **Admin** | Product approval, user management, payouts, refunds, returns, settings |
| 💳 **Payments** | Razorpay integration with webhook verification |
| 📱 **OTP Auth** | Phone-first authentication with mocked OTP (no external gateway needed locally) |
| ♾️ **Infinite Scroll** | Auto-loading product pages via IntersectionObserver API |
| 👤 **Profile** | Edit name, email, phone, city, state, pincode; referral & earn system |
| 💬 **Chat** | Buyer ↔ Seller messaging per order |

---

## 🚀 Quick Start (Local)

### Prerequisites
- PHP 8.3+
- Composer 2.x
- Node.js 18+ & npm 9+
- SQLite (default) **or** MySQL/PostgreSQL

### 1. Clone & Install
```bash
git clone https://github.com/YOUR_USERNAME/fiinway.git
cd fiinway

composer install
npm install
```

### 2. Environment Setup
```bash
cp .env.example .env
php artisan key:generate
```

Edit `.env` and set at minimum:
```env
APP_URL=http://localhost:8000
RAZORPAY_KEY_ID=your_key
RAZORPAY_KEY_SECRET=your_secret
```

### 3. Database Setup
```bash
# SQLite (default — zero config)
touch database/database.sqlite
php artisan migrate --seed

# OR MySQL
# Set DB_CONNECTION=mysql and credentials in .env, then:
php artisan migrate --seed
```

### 4. Build Assets & Run
```bash
npm run build
php artisan serve
```

Visit **http://localhost:8000**

---

## 🔑 Default Credentials (after seeding)

| Role | Phone | Password | Notes |
|---|---|---|---|
| **Admin** | `9999999999` | `admin123` | Access `/admin/dashboard` |
| **Seller** | `9876543210` | `password` | Access `/seller/dashboard` |
| **Buyer** | `9123456789` | `password` | OTP login also works |

> **OTP Login**: Check `storage/logs/laravel.log` for the mocked OTP code during local development.

---

## 📁 Project Structure

```
fiinway/
├── app/
│   ├── Http/Controllers/
│   │   ├── Admin/          # AdminController (products, users, orders, settings…)
│   │   ├── Buyer/          # Cart, Checkout, Orders, Returns, Wishlist, Reviews
│   │   ├── Seller/         # Products, Orders, Earnings
│   │   └── Auth/           # Phone OTP + Profile
│   ├── Models/             # Product, Order, User, Cart, Review, ReturnRequest…
│   └── Services/           # OrderStatusService, etc.
├── database/
│   ├── migrations/         # Full marketplace schema
│   └── seeders/            # DatabaseSeeder (100+ users, 120+ products)
├── public/
│   └── logo.png            # FIINWAY brand logo
├── resources/
│   ├── css/app.css         # Global styles (Flipkart-inspired design system)
│   ├── js/app.js
│   └── views/
│       ├── layouts/        # app.blade.php, admin.blade.php
│       ├── buyer/          # products, cart, checkout, orders, profile
│       ├── seller/         # dashboard, products, earnings
│       ├── admin/          # dashboard, products, orders, users, settings
│       ├── components/     # product-card, header, product-image
│       └── profile/        # index (account page)
└── routes/web.php          # All routes (buyer, seller, admin, auth)
```

---

## ☁️ Deployment (Production)

### Shared Hosting / VPS

```bash
# 1. Upload files (exclude: vendor, node_modules, .env, storage/logs, public/build)

# 2. On server:
composer install --no-dev --optimize-autoloader
npm ci && npm run build

cp .env.example .env
php artisan key:generate

# 3. Set APP_ENV=production, APP_DEBUG=false in .env
# 4. Set DB_CONNECTION and credentials

php artisan migrate --force
php artisan db:seed --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan storage:link
```

### Environment Variables for Production

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_DATABASE=fiinway_prod
DB_USERNAME=dbuser
DB_PASSWORD=strongpassword

MAIL_MAILER=smtp
MAIL_HOST=smtp.mailgun.org
MAIL_PORT=587
MAIL_USERNAME=your@mailgun.com
MAIL_PASSWORD=yourpassword
MAIL_FROM_ADDRESS=support@fiinway.com

RAZORPAY_KEY_ID=rzp_live_xxxxx
RAZORPAY_KEY_SECRET=your_live_secret
```

---

## 🛠️ Tech Stack

| Layer | Technology |
|---|---|
| **Backend** | Laravel 11, PHP 8.3 |
| **Frontend** | Blade Templates, Alpine.js 3, Tailwind CSS v4 |
| **Database** | SQLite (dev) / MySQL 8+ (prod) |
| **Payments** | Razorpay (webhooks supported) |
| **Assets** | Vite 8 |
| **Auth** | Session-based with phone OTP |

---

## 🤝 Contributing

1. Fork the repo
2. Create a feature branch: `git checkout -b feature/my-feature`
3. Commit: `git commit -m "feat: add my feature"`
4. Push: `git push origin feature/my-feature`
5. Open a Pull Request

---

## 📄 License

MIT License — see [LICENSE](LICENSE) file for details.

---

*Built with ❤️ for FIINWAY Marketplace*
