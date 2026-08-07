# SIAP – Sistem Informasi Administrasi Rapat

![Laravel](https://img.shields.io/badge/Laravel-10-red?logo=laravel)
![PHP](https://img.shields.io/badge/PHP-8.2+-777BB4?logo=php)
![Bootstrap](https://img.shields.io/badge/Bootstrap-5-7952B3?logo=bootstrap)
![MySQL](https://img.shields.io/badge/MySQL-MariaDB-4479A1?logo=mysql)
![License](https://img.shields.io/badge/License-Internal-orange)

---

## Overview

SIAP (Sistem Informasi Administrasi Rapat) adalah aplikasi berbasis web untuk mengelola administrasi rapat secara terpusat, meliputi peminjaman ruang rapat, penjadwalan, complaint fasilitas, absensi QR Code, manajemen karyawan, serta konfigurasi sistem.

---

## Key Features

- Dashboard Monitoring
- Room Booking Management
- Meeting Schedule Calendar
- Complaint Management
- QR Code Attendance
- Employee & User Management
- Room & Facility Management
- Notification System
- Master Data Management
- Role-Based Access Control (RBAC)

---

## Technology Stack

| Layer | Technology |
|--------|------------|
| Framework | Laravel 10 |
| Language | PHP 8.2+ |
| Database | MySQL / MariaDB |
| ORM | Eloquent ORM |
| Frontend | Blade Template |
| UI Framework | Bootstrap 5 |
| Icons | Bootstrap Icons |
| Styling | HTML5, CSS3, JavaScript |

---

## System Requirements

- PHP 8.2 atau lebih baru
- Composer 2.x
- MySQL 8.x atau MariaDB 10.x
- Git
- Web Server (Apache, Nginx, atau Laragon)

---

# Installation

## 1. Clone Repository

```bash
git clone https://github.com/muharienal/siap.git
cd siap
```

---

## 2. Install Dependencies

Install seluruh dependency Laravel menggunakan Composer.

```bash
composer install
```

---

## 3. Configure Environment

Salin file environment.

**Linux / macOS**

```bash
cp .env.example .env
```

**Windows Command Prompt**

```cmd
copy .env.example .env
```

**Windows PowerShell**

```powershell
Copy-Item .env.example .env
```

---

## 4. Configure Database

Buat database baru pada MySQL atau MariaDB, kemudian sesuaikan konfigurasi database pada file `.env`.

Contoh konfigurasi:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=siap
DB_USERNAME=root
DB_PASSWORD=
```

---

## 5. Generate Application Key

```bash
php artisan key:generate
```

---

## 6. Run Database Migration

Jika project menggunakan seeder:

```bash
php artisan migrate --seed
```

Jika tidak menggunakan seeder:

```bash
php artisan migrate
```

---

## 7. Create Storage Link

Jalankan perintah berikut apabila aplikasi menggunakan penyimpanan file pada direktori `storage/app/public`.

```bash
php artisan storage:link
```

---

## 8. Start Development Server

```bash
php artisan serve
```

Aplikasi dapat diakses melalui:

```
http://127.0.0.1:8000
```

---

## Demo Login

> Akun berikut tersedia apabila data awal (Seeder) telah dijalankan.

| Role | Username (NIK) | Password |
|------|----------------|----------|
| Administrator | `ADM001` | `password` |
| Karyawan | `ITD004` | `password` |

---

## Business Rules

| Item | Value |
|------|-------|
| Operating Hours | 07:00 – 16:00 WIB |
| Booking Interval | 30 Minutes |
| Working Days | Monday – Friday |
| Weekend Booking | Not Allowed |
| Conflict Detection | Automatic |
| Booking Status | Pending, Approved, Rejected |

---

## Database Structure

| Table | Description |
|--------|-------------|
| users | User and employee accounts |
| rooms | Meeting room master data |
| room_photos | Meeting room photos |
| facilities | Facility master data |
| bookings | Room booking records |
| booking_facilities | Booking facility details |
| complaints | Facility complaint records |
| attendances | Meeting attendance records |
| divisions | Division master data |
| positions | Position master data |
| notifications | System notifications |

---

## Project Structure

```
app/
bootstrap/
config/
database/
public/
resources/
routes/
storage/
tests/
```

---

## Security

- Authentication
- Role-Based Access Control (RBAC)
- Password Hashing (Bcrypt)
- CSRF Protection
- Route Middleware
- Request Validation
- Session Authentication

---

## License

This project is proprietary software developed exclusively for internal operational use within **IT PT Petrokopindo Cipta Selaras**.

© 2026 IT PT Petrokopindo Cipta Selaras.
