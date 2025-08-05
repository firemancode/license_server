# 🚀 License Server

A comprehensive license management system built with Laravel 11 and AdminLTE 3. This system allows you to manage software licenses, track activations, and provide API endpoints for license verification.

## ✨ Features

### 🔐 License Management
- Create and manage software licenses
- Track license status (active, expired, blocked, pending)
- Set activation limits per license
- License key generation and validation

### 📦 Product Management
- Manage software products
- Version control
- Pricing information
- Active/inactive status

### 🌐 Activation Tracking
- Track domain activations
- IP address logging
- User agent detection
- Activation history

### 🔌 API Integration
- RESTful API endpoints
- License verification
- Signature validation
- Rate limiting

### 📊 Admin Dashboard
- Real-time statistics
- License overview
- Product management
- User management

## 🛠️ Installation

### Prerequisites
- PHP 8.1 or higher
- Composer
- MySQL/MariaDB
- Node.js & NPM (for frontend assets)

### 1. Clone Repository
```bash
git clone <repository-url>
cd license-server
```

### 2. Install Dependencies
```bash
composer install
npm install
```

### 3. Environment Setup
```bash
cp .env.example .env
php artisan key:generate
```

### 4. Database Configuration
Edit `.env` file:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=license_server
DB_USERNAME=root
DB_PASSWORD=
```

### 5. Run Migrations & Seeders
```bash
php artisan migrate
php artisan db:seed
```

### 6. Build Assets
```bash
npm run build
```

### 7. Start Development Server
```bash
php artisan serve
```

## 🔑 Default Login Credentials

### Admin User
- **Email:** `admin@license.com`
- **Password:** `admin123`

### Test User
- **Email:** `test@example.com`
- **Password:** `password`

## 📋 API Documentation

### License Verification
```http
POST /api/verify-license
Content-Type: application/json

{
    "license_key": "YOUR-LICENSE-KEY",
    "domain": "your-domain.com",
    "product_slug": "plugin-wp-pro"
}
```

### Response
```json
{
    "success": true,
    "message": "License verified successfully",
    "data": {
        "license": {
            "key": "YOUR-LICENSE-KEY",
            "status": "active",
            "expires_at": "2024-12-31",
            "max_activations": 5,
            "current_activations": 2
        }
    }
}
```

## 🏗️ Project Structure

```
license-server/
├── app/
│   ├── Http/Controllers/
│   │   ├── Admin/          # Admin controllers
│   │   └── API/           # API controllers
│   ├── Models/            # Eloquent models
│   └── View/Components/   # Blade components
├── database/
│   ├── migrations/        # Database migrations
│   └── seeders/          # Database seeders
├── resources/
│   ├── views/
│   │   ├── admin/        # Admin views
│   │   └── layouts/      # Layout templates
│   ├── css/              # Stylesheets
│   └── js/               # JavaScript files
├── routes/
│   ├── web.php          # Web routes
│   └── api.php          # API routes
└── storage/
    └── logs/            # Application logs
```

## 🔧 Configuration

### License Key Format
Default format: `XXXXX-XXXXX-XXXXX`

### Activation Limits
- Set per license in admin panel
- Track domain and IP addresses
- Prevent abuse with rate limiting

### API Rate Limiting
- 60 requests per minute per IP
- Configurable in `app/Http/Kernel.php`

## 🚀 Deployment

### Production Setup
1. Set `APP_ENV=production` in `.env`
2. Configure database credentials
3. Run `php artisan config:cache`
4. Set up web server (Apache/Nginx)
5. Configure SSL certificate

### Environment Variables
```env
APP_NAME="License Server"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-domain.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=license_server
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

## 🧪 Testing

```bash
# Run all tests
php artisan test

# Run specific test
php artisan test --filter=LicenseTest
```

## 📝 Contributing

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'feat: add amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

## 📄 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## 🤝 Support

For support, email support@license-server.com or create an issue in the repository.

## 🔄 Changelog

### v1.0.0
- Initial release
- License management system
- Admin dashboard
- API endpoints
- Activation tracking

---

**Built with ❤️ using Laravel 11 & AdminLTE 3**
 