# 🤖 Easykids Robotics Competition Registration System

![Laravel](https://img.shields.io/badge/Laravel-12.0-FF2D20?style=flat-square&logo=laravel)
![PHP](https://img.shields.io/badge/PHP-8.2+-777BB4?style=flat-square&logo=php)
![Tailwind CSS](https://img.shields.io/badge/Tailwind%20CSS-3.1-38B2AC?style=flat-square&logo=tailwindcss)
![License](https://img.shields.io/badge/License-MIT-green?style=flat-square)
![Status](https://img.shields.io/badge/Status-Active-brightgreen?style=flat-square)

---

## 📑 Table of Contents

- [English](#-english)
- [ไทย (Thai)](#-thai)

---

# 🇺🇸 English

## 📋 About The Project

**Easykids Robotics Competition Registration System** is a comprehensive web application designed to streamline the registration process for robotics competitions. The platform enables users to create and manage teams, register for specific competition classes based on age and robot type, process group payments through a shopping cart system, request tax invoices, and upload payment documentation. 

On the administrative side, the system provides a powerful dashboard for verifying payments, approving or rejecting registrations, and automating data synchronization to Google Sheets and Google Drive for team records and financial documentation.

### Key Highlights
✨ **Seamless User Experience** - Intuitive team and member management  
✨ **Secure Payments** - Group payment checkout with payment slip verification  
✨ **Automation** - Automatic email confirmations and data synchronization  
✨ **Admin Control** - Comprehensive payment verification and approval system  
✨ **Professional Documentation** - Automated tax invoice and e-ticket generation  

---

## ✨ Key Features

### 👥 User Portal
- **Authentication System**
  - Email/password authentication
  - Google OAuth 2.0 social login integration
  - Secure session management

- **Team & Member Management**
  - Create and manage robotics teams
  - Add/remove team members with role assignments
  - Edit team information and member details

- **Competition Registration**
  - Browse available competition classes
  - Register based on age group and robot type compatibility
  - View competition details, schedules, and requirements
  - Real-time availability checking

- **Group Payment System**
  - Shopping cart for multiple registrations
  - Group payment checkout
  - E-slip (payment slip) upload functionality
  - Tax invoice request during checkout

- **E-Ticket & Confirmations**
  - Automated e-ticket generation upon approval
  - Email confirmations for successful registrations
  - Rejection notifications with detailed reasons

### 🔐 Admin Dashboard
- **Payment Management**
  - View all pending, approved, and rejected payments
  - Advanced filtering and search capabilities
  - Payment status tracking

- **Payment Verification**
  - Split-screen modal for comparing payment slips
  - Detailed transaction information
  - Payment amount and account validation

- **Transaction Approval**
  - One-click approval with confirmation dialogs
  - Rejection functionality with custom reason notes
  - Automatic email notifications to users

### ⚙️ Automation Features
- **Google Drive Integration**
  - Auto-sync team registration data to dedicated Google Drive folder
  - Auto-sync tax invoice data to finance folder
  - Automatic folder organization and file naming

- **Email Notifications**
  - Approval confirmation emails
  - Rejection notification emails with reasons
  - Welcome and registration submission emails

- **Data Synchronization**
  - Real-time sync to Google Sheets for reporting
  - Automatic data formatting and organization
  - Historical data preservation

---

## 🛠️ Built With

### Backend
- **[Laravel 12.0](https://laravel.com/)** - Modern PHP framework
- **[PHP 8.2+](https://www.php.net/)** - Server-side language
- **[PostgreSQL](https://www.postgresql.org/)** - Database (via Supabase)
- **[Google API Client 2.19](https://github.com/googleapis/google-api-php-client)** - Google Services integration
- **[Laravel Socialite 5.25](https://laravel.com/docs/socialite)** - OAuth provider
- **[Flysystem Google Drive Extension 2.4](https://packagist.org/packages/masbug/flysystem-google-drive-ext)** - Cloud storage
- **[Simple QR Code 4.2](https://packagist.org/packages/simplesoftwareio/simple-qrcode)** - QR code generation

### Frontend
- **[Blade Templates](https://laravel.com/docs/blade)** - Templating engine
- **[Tailwind CSS 3.1](https://tailwindcss.com/)** - Utility-first CSS
- **[Alpine.js 3.4](https://alpinejs.dev/)** - Lightweight JavaScript framework
- **[Vite 7.0](https://vitejs.dev/)** - Build tool
- **[Axios](https://axios-http.com/)** - HTTP client

### Development Tools
- **[Laravel Debugbar 4.1](https://github.com/barryvdh/laravel-debugbar)** - Development utilities
- **[PHPUnit 11.5](https://phpunit.de/)** - Testing framework
- **[Laravel Pint 1.29](https://laravel.com/docs/pint)** - Code style formatter
- **[Laravel Sail 1.41](https://laravel.com/docs/sail)** - Docker development environment

---

## 🚀 Getting Started

### Prerequisites

Before you begin, ensure you have the following installed:

- **PHP** 8.2 or higher
- **Composer** (latest version)
- **Node.js** 18+ and npm
- **Git**
- **PostgreSQL** (or use Supabase)
- **Google Account** (for API credentials)

### Installation Steps

#### 1. Clone the Repository

```bash
git clone https://github.com/yourusername/ezkid-competitions-center.git
cd ezkid-competitions-center/app
```

#### 2. Install PHP Dependencies

```bash
composer install
```

#### 3. Install Node.js Dependencies

```bash
npm install
npm run build
```

#### 4. Create Environment File

```bash
cp .env.example .env
```

#### 5. Generate Application Key

```bash
php artisan key:generate
```

#### 6. Configure Database Connection

Edit your `.env` file and configure Supabase (PostgreSQL) connection:

```env
DB_CONNECTION=pgsql
DB_HOST=your-supabase-host.supabase.co
DB_PORT=5432
DB_DATABASE=your_database_name
DB_USERNAME=your_postgres_user
DB_PASSWORD=your_postgres_password
```

> **Note:** Get these credentials from your [Supabase project settings](https://supabase.com/dashboard).

#### 7. Run Database Migrations

```bash
php artisan migrate
```

#### 8. Start Development Server

For a quick start:
```bash
php artisan serve
```

For full development with Vite and queue listening:
```bash
npm run dev
```

Or use the custom composer script:
```bash
composer run dev
```

---

## 🔧 Environment Variables

### Database Configuration

| Variable | Description | Example |
|----------|-------------|---------|
| `DB_CONNECTION` | Database driver | `pgsql` |
| `DB_HOST` | Supabase PostgreSQL host | `abc123.supabase.co` |
| `DB_PORT` | Database port | `5432` |
| `DB_DATABASE` | Database name | `postgres` |
| `DB_USERNAME` | Database username | `postgres` |
| `DB_PASSWORD` | Database password | `your_secure_password` |

### Application Configuration

```env
APP_NAME="Easykids Robotics"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-domain.com

APP_KEY=base64:your_generated_key_here
```

### Mail Configuration

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="noreply@easykids.com"
MAIL_FROM_NAME="Easykids Robotics"
```

### Google API Configuration

To set up Google API integration for Google Drive and Sheets synchronization:

#### Step 1: Create Google Cloud Project

1. Go to [Google Cloud Console](https://console.cloud.google.com/)
2. Create a new project
3. Enable the following APIs:
   - Google Drive API
   - Google Sheets API

#### Step 2: Create Service Account

1. Navigate to **Credentials** → **Create Credentials** → **Service Account**
2. Fill in the service account details
3. Create and download the JSON key file
4. Extract the credentials and add them to your `.env`:

```env
GOOGLE_DRIVE_CLIENT_ID=your_client_id
GOOGLE_DRIVE_CLIENT_SECRET=your_client_secret
GOOGLE_DRIVE_REFRESH_TOKEN=your_refresh_token
GOOGLE_DRIVE_FOLDER_ID=your_folder_id
```

#### Step 3: Configure Folder IDs

You need to create two folders in Google Drive for data organization:

```env
# Teams registration data folder
GOOGLE_DRIVE_TEAMS_FOLDER_ID=your_teams_folder_id

# Tax invoice data folder
GOOGLE_DRIVE_TAXES_FOLDER_ID=your_taxes_folder_id
```

To get folder IDs:
1. Open the folder in Google Drive
2. Copy the ID from the URL: `https://drive.google.com/drive/folders/{FOLDER_ID}`

### OAuth 2.0 Social Login (Google)

Configure Google OAuth for social login:

```env
GOOGLE_CLIENT_ID=your_oauth_client_id
GOOGLE_CLIENT_SECRET=your_oauth_client_secret
GOOGLE_REDIRECT_URI=https://your-domain.com/auth/google/callback
```

### Queue Configuration (Optional)

For processing heavy operations asynchronously:

```env
QUEUE_CONNECTION=database
# or
QUEUE_CONNECTION=redis
REDIS_HOST=127.0.0.1
REDIS_PORT=6379
```

---

## 🗄️ Database Migration

The application uses Laravel migrations to manage database schema. To run all pending migrations:

```bash
php artisan migrate
```

### Available Migration Commands

```bash
# Run all pending migrations
php artisan migrate

# Rollback last batch of migrations
php artisan migrate:rollback

# Rollback all migrations
php artisan migrate:reset

# Rollback and re-run all migrations
php artisan migrate:refresh

# Seed the database with test data
php artisan migrate:seed
```

### Database Schema Overview

The system includes the following main tables:

- **users** - User accounts and authentication
- **teams** - Robotics team information
- **team_members** - Team member details
- **competitions** - Competition events
- **competition_classes** - Competition categories
- **game_types** - Robot game types
- **registrations** - User registrations for competitions
- **payment_transactions** - Payment records
- **robot_models** - Robot model specifications

---

## 📝 Project Structure

```
app/
├── app/
│   ├── Http/
│   │   ├── Controllers/     # Request handlers
│   │   ├── Middleware/      # HTTP middleware
│   │   └── Requests/        # Form request validation
│   ├── Mail/                # Mailable classes
│   ├── Models/              # Eloquent models
│   ├── Services/            # Business logic services
│   └── Providers/           # Service providers
├── bootstrap/               # Application bootstrap
├── config/                  # Configuration files
├── database/
│   ├── factories/           # Model factories
│   ├── migrations/          # Database migrations
│   └── seeders/             # Database seeders
├── public/                  # Public assets
├── resources/
│   ├── css/                 # Stylesheets
│   ├── js/                  # JavaScript
│   └── views/               # Blade templates
├── routes/                  # Route definitions
├── storage/                 # File storage
├── tests/                   # Test cases
├── vite.config.js           # Vite configuration
└── tailwind.config.js       # Tailwind CSS configuration
```

---

## 🧪 Testing

Run the test suite:

```bash
composer run test
```

Or directly with PHPUnit:

```bash
php artisan test
```

---

## 📦 Deployment

### Preparation

1. Set production environment variables in `.env`
2. Set `APP_DEBUG=false` and `APP_ENV=production`
3. Run migrations: `php artisan migrate --force`
4. Build assets: `npm run build`

### On Your Server

```bash
# Clone repository
git clone your-repo-url
cd ezkid-competitions-center/app

# Install dependencies
composer install --no-dev --optimize-autoloader
npm install
npm run build

# Setup environment
cp .env.example .env
php artisan key:generate

# Database
php artisan migrate --force

# Clear caches
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

---

## 🐛 Troubleshooting

### Common Issues

**Q: "Class not found" error**  
A: Run `composer dump-autoload`

**Q: Mail not sending**  
A: Check your `.env` mail configuration and ensure your SMTP credentials are correct

**Q: Google API connection error**  
A: Verify your Google API credentials and folder IDs in `.env`

**Q: Database connection failed**  
A: Ensure your Supabase credentials are correct and database is accessible

---

## 📞 Contact & Support

For questions, feature requests, or bug reports:

- 📧 Email: support@easykids.com
- 🌐 Website: https://easykids.com
- 📱 Phone: +66-XX-XXX-XXXX

---

## 📄 License

This project is licensed under the MIT License - see the LICENSE file for details.

---

---

# 🇹🇭 ไทย

## 📋 เกี่ยวกับโครงการ

**ระบบการลงทะเบียนการแข่งขันหุ่นยนต์ Easykids** เป็นแอปพลิเคชันเว็บที่ครอบคลุมซึ่งออกแบบมาเพื่อทำให้กระบวนการลงทะเบียนการแข่งขันหุ่นยนต์เป็นไปอย่างง่ายดาย แพลตฟอร์มนี้ช่วยให้ผู้ใช้สามารถสร้างและจัดการทีม ลงทะเบียนสำหรับชั้นการแข่งขันที่เฉพาะเจาะจง ประมวลผลการชำระเงินแบบกลุ่มผ่านระบบรถเข็น ขอใบแจ้งหนี้ภาษี และอัปโหลดเอกสารการชำระเงิน

ในด้านการบริหารจัดการ ระบบจะมีแดชบอร์ดที่ทรงพลังสำหรับการตรวจสอบการชำระเงิน อนุมัติหรือปฏิเสธการลงทะเบียน และทำให้การซิงค์ข้อมูลอัตโนมัติไปยัง Google Sheets และ Google Drive สำหรับบันทึกทีมและเอกสารทางการเงินเป็นไปโดยอัตโนมัติ

### ไฮไลต์หลัก
✨ **ประสบการณ์ผู้ใช้ที่ไม่มีรอยต่อ** - การจัดการทีมและสมาชิกที่ใช้งานง่าย  
✨ **การชำระเงินที่ปลอดภัย** - การชำระเงินแบบกลุ่มพร้อมการตรวจสอบใบเสร็จ  
✨ **ระบบอัตโนมัติ** - การยืนยันอีเมลอัตโนมัติและการซิงค์ข้อมูล  
✨ **การควบคุมของผู้ดูแล** - ระบบการตรวจสอบและอนุมัติการชำระเงินที่ครอบคลุม  
✨ **เอกสารมืออาชีพ** - การสร้างใบแจ้งหนี้ภาษีและตั๋วอิเล็กทรอนิกส์อัตโนมัติ  

---

## ✨ คุณสมบัติหลัก

### 👥 พอร์ทัลผู้ใช้
- **ระบบการยืนยันตัวตน**
  - การรับรองความถูกต้องของอีเมล/รหัสผ่าน
  - การรวมเข้าสู่ระบบ Google OAuth 2.0
  - การจัดการเซสชันที่ปลอดภัย

- **การจัดการทีมและสมาชิก**
  - สร้างและจัดการทีมหุ่นยนต์
  - เพิ่ม/ลบสมาชิกทีมพร้อมการกำหนดบทบาท
  - แก้ไขข้อมูลทีมและรายละเอียดสมาชิก

- **การลงทะเบียนการแข่งขัน**
  - ท่องดูชั้นการแข่งขันที่มีอยู่
  - ลงทะเบียนตามกลุ่มอายุและความเข้ากันได้ของประเภทหุ่นยนต์
  - ดูรายละเอียดการแข่งขัน ตารางเวลา และข้อกำหนด
  - การตรวจสอบความพร้อมใช้งานแบบเรียลไทม์

- **ระบบการชำระเงินแบบกลุ่ม**
  - รถเข็นช้อปปิงสำหรับการลงทะเบียนหลายรายการ
  - การชำระเงินแบบกลุ่ม
  - ฟังก์ชันการอัปโหลด E-slip (ใบเสร็จชำระเงิน)
  - ขออนุญาตใบแจ้งหนี้ภาษีระหว่างการชำระเงิน

- **ตั๋วอิเล็กทรอนิกส์และการยืนยัน**
  - การสร้างตั๋วอิเล็กทรอนิกส์โดยอัตโนมัติเมื่ออนุมัติ
  - การยืนยันทางอีเมลสำหรับการลงทะเบียนที่สำเร็จ
  - การแจ้งเตือนการปฏิเสธพร้อมเหตุผลโดยละเอียด

### 🔐 แดชบอร์ดผู้ดูแล
- **การจัดการการชำระเงิน**
  - ดูการชำระเงินทั้งหมดที่รอการอนุมัติ อนุมัติแล้ว และถูกปฏิเสธ
  - ความสามารถในการกรองและค้นหาขั้นสูง
  - การติดตามสถานะการชำระเงิน

- **การตรวจสอบการชำระเงิน**
  - โมดัลหน้าจออย่างละเอียดเพื่อเปรียบเทียบใบเสร็จชำระเงิน
  - ข้อมูลธุรกรรมโดยละเอียด
  - การตรวจสอบจำนวนเงินและบัญชี

- **การอนุมัติธุรกรรม**
  - อนุมัติด้วยคลิกเดียวพร้อมกล่องยืนยัน
  - ฟังก์ชันการปฏิเสธพร้อมบันทึกเหตุผลที่กำหนดเอง
  - การแจ้งเตือนอีเมลอัตโนมัติให้ผู้ใช้

### ⚙️ คุณสมบัติการทำให้เป็นอัตโนมัติ
- **การรวมเข้าไดรฟ์ Google**
  - ซิงค์ข้อมูลการลงทะเบียนทีมโดยอัตโนมัติไปยังโฟลเดอร์ Google Drive ที่ขนานนอม
  - ซิงค์ข้อมูลใบแจ้งหนี้ภาษีโดยอัตโนมัติไปยังโฟลเดอร์การเงิน
  - การจัดระเบียบโฟลเดอร์และการตั้งชื่อไฟล์อัตโนมัติ

- **การแจ้งเตือนทางอีเมล**
  - อีเมลยืนยันการอนุมัติ
  - อีเมลแจ้งเตือนการปฏิเสธพร้อมเหตุผล
  - อีเมลต้อนรับและการส่งการลงทะเบียน

- **การซิงค์ข้อมูล**
  - ซิงค์แบบเรียลไทม์ไปยัง Google Sheets เพื่อการรายงาน
  - การจัดรูปแบบข้อมูลและการจัดระเบียบอัตโนมัติ
  - การรักษาข้อมูลทางประวัติศาสตร์

---

## 🛠️ สร้างขึ้นด้วย

### แบ็กเอนด์
- **[Laravel 12.0](https://laravel.com/)** - เฟรมเวิร์ก PHP สมัยใหม่
- **[PHP 8.2+](https://www.php.net/)** - ภาษาฝั่งเซิร์ฟเวอร์
- **[PostgreSQL](https://www.postgresql.org/)** - ฐานข้อมูล (ผ่าน Supabase)
- **[Google API Client 2.19](https://github.com/googleapis/google-api-php-client)** - การรวมเข้าบริการ Google
- **[Laravel Socialite 5.25](https://laravel.com/docs/socialite)** - ผู้ให้บริการ OAuth
- **[Flysystem Google Drive Extension 2.4](https://packagist.org/packages/masbug/flysystem-google-drive-ext)** - การจัดเก็บในคลาउด์
- **[Simple QR Code 4.2](https://packagist.org/packages/simplesoftwareio/simple-qrcode)** - การสร้างโค้ด QR

### ฟรอนต์เอนด์
- **[Blade Templates](https://laravel.com/docs/blade)** - เอนจิ่นเทมเพลต
- **[Tailwind CSS 3.1](https://tailwindcss.com/)** - CSS แบบยูทิลิตี้ฟิร์สต์
- **[Alpine.js 3.4](https://alpinejs.dev/)** - เฟรมเวิร์ก JavaScript เบา
- **[Vite 7.0](https://vitejs.dev/)** - เครื่องมือ Build
- **[Axios](https://axios-http.com/)** - ไคลเอนต์ HTTP

### เครื่องมือการพัฒนา
- **[Laravel Debugbar 4.1](https://github.com/barryvdh/laravel-debugbar)** - ยูทิลิตี้การพัฒนา
- **[PHPUnit 11.5](https://phpunit.de/)** - เฟรมเวิร์กการทดสอบ
- **[Laravel Pint 1.29](https://laravel.com/docs/pint)** - ฟอร์แมตเตอร์รูปแบบโค้ด
- **[Laravel Sail 1.41](https://laravel.com/docs/sail)** - สภาพแวดล้อมการพัฒนา Docker

---

## 🚀 เริ่มต้นใช้งาน

### ข้อกำหนดเบื้องต้น

ก่อนที่คุณจะเริ่ม ตรวจสอบให้แน่ใจว่าคุณได้ติดตั้งสิ่งต่อไปนี้:

- **PHP** 8.2 หรือสูงกว่า
- **Composer** (เวอร์ชันล่าสุด)
- **Node.js** 18+ และ npm
- **Git**
- **PostgreSQL** (หรือใช้ Supabase)
- **บัญชี Google** (สำหรับข้อมูลประจำตัว API)

### ขั้นตอนการติดตั้ง

#### 1. โคลนที่เก็บข้อมูล

```bash
git clone https://github.com/yourusername/ezkid-competitions-center.git
cd ezkid-competitions-center/app
```

#### 2. ติดตั้ง PHP Dependencies

```bash
composer install
```

#### 3. ติดตั้ง Node.js Dependencies

```bash
npm install
npm run build
```

#### 4. สร้างไฟล์สภาพแวดล้อม

```bash
cp .env.example .env
```

#### 5. สร้างคีย์แอปพลิเคชัน

```bash
php artisan key:generate
```

#### 6. กำหนดค่าการเชื่อมต่อฐานข้อมูล

แก้ไขไฟล์ `.env` และกำหนดค่าการเชื่อมต่อ Supabase (PostgreSQL):

```env
DB_CONNECTION=pgsql
DB_HOST=your-supabase-host.supabase.co
DB_PORT=5432
DB_DATABASE=your_database_name
DB_USERNAME=your_postgres_user
DB_PASSWORD=your_postgres_password
```

> **หมายเหตุ:** รับข้อมูลประจำตัวเหล่านี้จากการตั้งค่า [โครงการ Supabase](https://supabase.com/dashboard)

#### 7. เรียกใช้ Database Migrations

```bash
php artisan migrate
```

#### 8. เริ่มเซิร์ฟเวอร์พัฒนา

สำหรับเริ่มต้นอย่างรวดเร็ว:
```bash
php artisan serve
```

สำหรับการพัฒนาแบบเต็มด้วย Vite และการฟังอนุสัญญา:
```bash
npm run dev
```

หรือใช้สคริปต์ composer แบบกำหนดเอง:
```bash
composer run dev
```

---

## 🔧 ตัวแปรสภาพแวดล้อม

### การกำหนดค่าฐานข้อมูล

| ตัวแปร | คำอธิบาย | ตัวอย่าง |
|----------|-------------|---------|
| `DB_CONNECTION` | ไดรเวอร์ฐานข้อมูล | `pgsql` |
| `DB_HOST` | โฮสต์ Supabase PostgreSQL | `abc123.supabase.co` |
| `DB_PORT` | พอร์ตฐานข้อมูล | `5432` |
| `DB_DATABASE` | ชื่อฐานข้อมูล | `postgres` |
| `DB_USERNAME` | ชื่อผู้ใช้ฐานข้อมูล | `postgres` |
| `DB_PASSWORD` | รหัสผ่านฐานข้อมูล | `your_secure_password` |

### การกำหนดค่าแอปพลิเคชัน

```env
APP_NAME="Easykids Robotics"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-domain.com

APP_KEY=base64:your_generated_key_here
```

### การกำหนดค่าเมล

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="noreply@easykids.com"
MAIL_FROM_NAME="Easykids Robotics"
```

### การกำหนดค่า Google API

หากต้องการตั้งค่าการรวมเข้า Google API สำหรับการซิงค์ Google Drive และ Sheets:

#### ขั้นตอนที่ 1: สร้างโครงการ Google Cloud

1. ไปที่ [Google Cloud Console](https://console.cloud.google.com/)
2. สร้างโครงการใหม่
3. เปิดใช้งาน API ต่อไปนี้:
   - Google Drive API
   - Google Sheets API

#### ขั้นตอนที่ 2: สร้างบัญชีบริการ

1. ไปที่ **ข้อมูลประจำตัว** → **สร้างข้อมูลประจำตัว** → **บัญชีบริการ**
2. กรอกรายละเอียดบัญชีบริการ
3. สร้างและดาวน์โหลดไฟล์คีย์ JSON
4. แยกข้อมูลประจำตัวและเพิ่มลงใน `.env`:

```env
GOOGLE_DRIVE_CLIENT_ID=your_client_id
GOOGLE_DRIVE_CLIENT_SECRET=your_client_secret
GOOGLE_DRIVE_REFRESH_TOKEN=your_refresh_token
GOOGLE_DRIVE_FOLDER_ID=your_folder_id
```

#### ขั้นตอนที่ 3: กำหนดค่ารหัสโฟลเดอร์

คุณต้องสร้างสองโฟลเดอร์ใน Google Drive สำหรับการจัดการข้อมูล:

```env
# โฟลเดอร์ข้อมูลการลงทะเบียนทีม
GOOGLE_DRIVE_TEAMS_FOLDER_ID=your_teams_folder_id

# โฟลเดอร์ข้อมูลใบแจ้งหนี้ภาษี
GOOGLE_DRIVE_TAXES_FOLDER_ID=your_taxes_folder_id
```

เพื่อรับรหัสโฟลเดอร์:
1. เปิดโฟลเดอร์ใน Google Drive
2. คัดลอกรหัสจากลิงก์: `https://drive.google.com/drive/folders/{FOLDER_ID}`

### OAuth 2.0 Social Login (Google)

กำหนดค่า Google OAuth สำหรับการเข้าสู่ระบบสังคม:

```env
GOOGLE_CLIENT_ID=your_oauth_client_id
GOOGLE_CLIENT_SECRET=your_oauth_client_secret
GOOGLE_REDIRECT_URI=https://your-domain.com/auth/google/callback
```

### การกำหนดค่าอนุสัญญา (ตัวเลือก)

สำหรับการประมวลผลการดำเนินการที่หนักแน่นโดยอัตโนมัติ:

```env
QUEUE_CONNECTION=database
# หรือ
QUEUE_CONNECTION=redis
REDIS_HOST=127.0.0.1
REDIS_PORT=6379
```

---

## 🗄️ Database Migration

แอปพลิเคชันใช้ Laravel migrations เพื่อจัดการโครงร่างฐานข้อมูล หากต้องการเรียกใช้การอพเดททั้งหมดที่รออยู่:

```bash
php artisan migrate
```

### คำสั่ง Migration ที่มีอยู่

```bash
# เรียกใช้การอพเดททั้งหมดที่รออยู่
php artisan migrate

# ย้อนกลับการอพเดทแบตช์สุดท้าย
php artisan migrate:rollback

# ย้อนกลับการอพเดททั้งหมด
php artisan migrate:reset

# ย้อนกลับและเรียกใช้การอพเดททั้งหมดใหม่
php artisan migrate:refresh

# เสริมฐานข้อมูลด้วยข้อมูลทดสอบ
php artisan migrate:seed
```

### ภาพรวมโครงร่างฐานข้อมูล

ระบบรวมตารางหลักต่อไปนี้:

- **users** - บัญชีผู้ใช้และการรับรองความถูกต้อง
- **teams** - ข้อมูลทีมหุ่นยนต์
- **team_members** - รายละเอียดสมาชิกทีม
- **competitions** - เหตุการณ์การแข่งขัน
- **competition_classes** - หมวดหมู่การแข่งขัน
- **game_types** - ประเภทเกมหุ่นยนต์
- **registrations** - การลงทะเบียนผู้ใช้สำหรับการแข่งขัน
- **payment_transactions** - บันทึกการชำระเงิน
- **robot_models** - ข้อมูลจำเพาะของรูปแบบหุ่นยนต์

---

## 📝 โครงสร้างโครงการ

```
app/
├── app/
│   ├── Http/
│   │   ├── Controllers/     # ตัวจัดการคำขอ
│   │   ├── Middleware/      # Middleware HTTP
│   │   └── Requests/        # การตรวจสอบความถูกต้องของแบบฟอร์ม
│   ├── Mail/                # ชั้นเรียนที่ส่งได้
│   ├── Models/              # รุ่น Eloquent
│   ├── Services/            # บริการตรรกะทางธุรกิจ
│   └── Providers/           # ผู้ให้บริการบริการ
├── bootstrap/               # Bootstrap แอปพลิเคชัน
├── config/                  # ไฟล์การกำหนดค่า
├── database/
│   ├── factories/           # แฟกทอรี่โมเดล
│   ├── migrations/          # Database migrations
│   └── seeders/             # Database seeders
├── public/                  # สินทรัพย์สาธารณะ
├── resources/
│   ├── css/                 # สไตล์ชีต
│   ├── js/                  # JavaScript
│   └── views/               # เทมเพลต Blade
├── routes/                  # คำจำกัดความเส้นทาง
├── storage/                 # การจัดเก็บไฟล์
├── tests/                   # กรณีทดสอบ
├── vite.config.js           # การกำหนดค่า Vite
└── tailwind.config.js       # การกำหนดค่า Tailwind CSS
```

---

## 🧪 การทดสอบ

เรียกใช้ชุดการทดสอบ:

```bash
composer run test
```

หรือโดยตรงด้วย PHPUnit:

```bash
php artisan test
```

---

## 📦 การปรับใช้

### การเตรียมการ

1. ตั้งค่าตัวแปรสภาพแวดล้อมการผลิตใน `.env`
2. ตั้งค่า `APP_DEBUG=false` และ `APP_ENV=production`
3. เรียกใช้ migrations: `php artisan migrate --force`
4. สร้างสินทรัพย์: `npm run build`

### บนเซิร์ฟเวอร์ของคุณ

```bash
# โคลนที่เก็บข้อมูล
git clone your-repo-url
cd ezkid-competitions-center/app

# ติดตั้ง dependencies
composer install --no-dev --optimize-autoloader
npm install
npm run build

# ตั้งค่าสภาพแวดล้อม
cp .env.example .env
php artisan key:generate

# ฐานข้อมูล
php artisan migrate --force

# ล้างแคช
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

---

## 🐛 การแก้ไขปัญหา

### ปัญหาที่พบบ่อย

**คำถาม: ข้อผิดพลาด "Class not found"**  
ตอบ: เรียกใช้ `composer dump-autoload`

**คำถาม: ไม่ส่งเมล**  
ตอบ: ตรวจสอบการกำหนดค่าเมล `.env` ของคุณและตรวจสอบให้แน่ใจว่าข้อมูลประจำตัว SMTP ถูกต้อง

**คำถาม: ข้อผิดพลาดการเชื่อมต่อ Google API**  
ตอบ: ตรวจสอบข้อมูลประจำตัว Google API และรหัสโฟลเดอร์ของคุณใน `.env`

**คำถาม: ล้มเหลวในการเชื่อมต่อฐานข้อมูล**  
ตอบ: ตรวจสอบให้แน่ใจว่าข้อมูลประจำตัว Supabase ถูกต้องและสามารถเข้าถึงฐานข้อมูลได้

---

## 📞 ติดต่อและสนับสนุน

สำหรับคำถาม คำขอคุณสมบัติ หรือรายงานข้อบัญชี:

- 📧 อีเมล: support@easykids.com
- 🌐 เว็บไซต์: https://easykids.com
- 📱 โทรศัพท์: +66-XX-XXX-XXXX

---

## 📄 ใบอนุญาต

โครงการนี้อยู่ภายใต้ใบอนุญาต MIT - ดูไฟล์ LICENSE สำหรับรายละเอียด

---

**ขอบคุณที่ใช้ Easykids Robotics Competition Registration System!** 🤖🎉
