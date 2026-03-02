<<<<<<< HEAD
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
=======
# School ERP System 🎓

A comprehensive School/College Management System built with Laravel 12 and Bootstrap 5.

![Laravel](https://img.shields.io/badge/Laravel-12-FF2D20?style=flat-square&logo=laravel)
![PHP](https://img.shields.io/badge/PHP-8.2-777BB4?style=flat-square&logo=php)
![Bootstrap](https://img.shields.io/badge/Bootstrap-5-7952B3?style=flat-square&logo=bootstrap)
![MySQL](https://img.shields.io/badge/MySQL-8-4479A1?style=flat-square&logo=mysql)

## 📋 Features

### Core Modules
- **Student Management** - Admissions, records, documents
- **Teacher Management** - Profiles, departments, assignments
- **Attendance System** - Daily attendance, reports, analytics
- **Timetable Management** - Weekly schedules, room allocation
- **Examination & Results** - Marks entry, report cards
- **Fee Management** - Fee structures, payments, scholarships
- **Library Management** - Book issuance, returns, inventory

### Additional Features
- Role-based access control (Admin, Teacher, Student, Office Staff)
- Department and Program management
- Academic sessions and divisions
- Guardian information management
- Razorpay payment integration
- Export reports (PDF, Excel)

## 🚀 Installation

### Prerequisites
- PHP 8.2 or higher
- Composer
- MySQL 8.0 or higher
- Node.js & NPM (optional)

### Setup Steps

1. **Clone the repository**
```bash
git clone https://github.com/YOUR_USERNAME/School-Erp.git
cd School-Erp
```

2. **Install PHP dependencies**
```bash
composer install
```

3. **Copy environment file**
```bash
cp .env.example .env
```

4. **Generate application key**
```bash
php artisan key:generate
```

5. **Configure database** in `.env` file
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=school_erp
DB_USERNAME=root
DB_PASSWORD=
```

6. **Run migrations**
```bash
php artisan migrate --seed
```

7. **Create storage links**
```bash
php artisan storage:link
```

8. **Start development server**
```bash
php artisan serve
```

9. **Access the application**
```
http://localhost:8000
```

## 👤 Default Credentials

After seeding, use these credentials to login:

| Role | Email | Password |
|------|-------|----------|
| Admin | admin@school.com | password |
| Teacher | teacher@school.com | password |
| Student | student@school.com | password |

## 📁 Project Structure

```
School-Erp/
├── app/
│   ├── Http/
│   │   ├── Controllers/    # Request handling logic
│   │   └── Requests/       # Form validation
│   └── Models/             # Database models
├── database/
│   ├── migrations/         # Database schemas
│   ├── seeders/           # Test data
│   └── factories/         # Model factories
├── resources/
│   └── views/             # Blade templates
├── routes/
│   └── web.php            # Web routes
└── public/                # Public assets
```

## 🛠️ Technologies Used

- **Backend**: Laravel 12 (PHP 8.2)
- **Frontend**: Bootstrap 5, Vanilla JavaScript
- **Database**: MySQL 8.0
- **Payment Gateway**: Razorpay
- **Icons**: Bootstrap Icons, Font Awesome
- **Charts**: Chart.js

## 📸 Screenshots

### Dashboard
![Dashboard](screenshots/dashboard.png)

### Timetable Management
![Timetable](screenshots/timetable.png)

### Attendance Report
![Attendance](screenshots/attendance.png)

### Fee Management
![Fees](screenshots/fees.png)

## 🔧 Configuration

### Email Configuration
Update `.env` with your SMTP credentials:
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-password
MAIL_ENCRYPTION=tls
```

### Razorpay Configuration
```env
RAZORPAY_KEY=your_razorpay_key
RAZORPAY_SECRET=your_razorpay_secret
```

## 🧪 Testing

Run the test suite:
```bash
php artisan test
```

## 📝 License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

## 👨‍💻 Author

**Your Name**
- GitHub: [@YOUR_USERNAME](https://github.com/YOUR_USERNAME)
- Email: your.email@example.com

## 🤝 Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

1. Fork the Project
2. Create your Feature Branch (`git checkout -b feature/AmazingFeature`)
3. Commit your Changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the Branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

## 📞 Support

For support, email your.email@example.com or create an issue in the repository.

---

Made with ❤️ using Laravel
>>>>>>> Feature
