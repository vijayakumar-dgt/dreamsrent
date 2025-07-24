# 🚗 DreamsRent - Vehicle Rental Management System

[![Laravel](https://img.shields.io/badge/Laravel-12.x-red.svg)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.4%2B-blue.svg)](https://php.net)
[![License](https://img.shields.io/badge/License-MIT-green.svg)](LICENSE)

DreamsRent is a comprehensive, modern vehicle rental management system built with Laravel 12. It provides a complete solution for managing car rentals, bookings, payments, and customer relationships with a beautiful, responsive interface.

## 🌟 Features

### 🚙 **Vehicle Management**
- Complete vehicle inventory management
- Multiple vehicle types (Cars, Bikes, Boats)
- Vehicle specifications, images, and documentation
- Seasonal pricing and tariff management
- Maintenance tracking and damage reports

### 📅 **Booking System**
- Real-time availability checking
- Advanced booking calendar
- Quotation generation
- Multiple rental types (Daily, Weekly, Monthly, Yearly)
- Driver assignment and management

### 💳 **Payment Integration**
- PayPal integration (Sandbox & Live)
- Stripe payment gateway
- Multiple currency support
- Invoice generation and management
- Payment history tracking

### 👥 **User Management**
- Multi-role system (Admin, Customer, Driver)
- Customer profiles and preferences
- Document management
- Wishlist functionality
- Review and rating system

### 🎨 **Multiple Themes**
- 4 beautiful responsive themes
- Mobile-optimized designs
- RTL language support
- Customizable layouts

### 🌍 **Multi-Language & Location**
- Complete localization system
- Multiple language support
- Location-based services
- Currency management

### 📊 **Reports & Analytics**
- Comprehensive dashboard
- Booking reports
- Revenue analytics
- Customer insights

### 📱 **Additional Features**
- SMS notifications
- Email templates
- SEO optimization
- Blog management
- FAQ system
- Newsletter subscription

## 🛠️ Installation

### Requirements

- PHP 8.4 or higher
- MySQL 8.0 or higher
- Composer
- Node.js & NPM
- Apache/Nginx web server

### Quick Installation

1. **Clone the repository**
```bash
git clone https://github.com/your-username/dreamsrent.git
cd dreamsrent
```

2. **Install PHP dependencies**
```bash
composer install
```

3. **Install Node.js dependencies**
```bash
npm install
```

4. **Environment setup**
```bash
cp .env.example .env
php artisan key:generate
```

5. **Configure your database**
Edit `.env` file with your database credentials:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=dreamsrent_db
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

6. **Run migrations and seeders**
```bash
php artisan migrate --seed
```

7. **Create storage link**
```bash
php artisan storage:link
```

8. **Build assets**
```bash
npm run build
```

9. **Start the development server**
```bash
php artisan serve
```

Visit `http://localhost:8000` to access the application.

## 🔧 Configuration

### Payment Gateways

#### PayPal Configuration
```env
PAYPAL_MODE=sandbox
PAYPAL_SANDBOX_CLIENT_ID=your_sandbox_client_id
PAYPAL_SANDBOX_CLIENT_SECRET=your_sandbox_client_secret
```

#### Stripe Configuration
```env
STRIPE_KEY=your_stripe_publishable_key
STRIPE_SECRET=your_stripe_secret_key
```

### Email Configuration
```env
MAIL_MAILER=smtp
MAIL_HOST=your_smtp_host
MAIL_PORT=587
MAIL_USERNAME=your_email
MAIL_PASSWORD=your_password
MAIL_ENCRYPTION=tls
```

## 🎯 Usage

### Admin Panel
Access the admin panel at `/admin` with default credentials:
- **Email:** admin@dreamsrent.com
- **Password:** password

### Customer Registration
Customers can register at `/register` and start booking vehicles immediately.

### API Documentation
API documentation is available at `/documentation`

## 🏗️ Architecture

DreamsRent follows a modular architecture with the following modules:

- **Booking** - Handles all booking-related functionality
- **CarInfo** - Vehicle management and information
- **Communication** - Messaging and notifications
- **GeneralSetting** - Application configuration
- **MenuManagement** - Dynamic menu management
- **Page** - CMS and page builder
- **Report** - Analytics and reporting
- **RolesPermission** - User roles and permissions

## 🧪 Testing

Run the test suite:
```bash
# Run all tests
php artisan test

# Run specific test suite
php artisan test --testsuite=Feature

# Run with coverage
php artisan test --coverage
```

## 🔒 Security

DreamsRent implements several security measures:

- CSRF protection on all forms
- SQL injection prevention
- XSS protection
- Security headers middleware
- Input validation and sanitization
- Rate limiting on API endpoints

## 🚀 Performance

- Optimized database queries with eager loading
- Redis caching support
- Image optimization
- Minified assets
- Database indexing
- Query optimization

## 📝 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## 🤝 Support

For support and questions:

- 📧 Email: support@dreamsrent.com
- 📖 Documentation: Available in `/public/documentation`
- 🐛 Issues: Create an issue on GitHub

## 🙏 Credits

Built with ❤️ using:
- [Laravel](https://laravel.com) - The PHP Framework
- [Bootstrap](https://getbootstrap.com) - CSS Framework
- [jQuery](https://jquery.com) - JavaScript Library
- [Chart.js](https://www.chartjs.org) - Charts and Analytics

---

**DreamsRent** - Making vehicle rental management simple and efficient.
