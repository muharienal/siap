# SIAP – Sistem Informasi Administrasi Rapat

![Laravel](https://img.shields.io/badge/Laravel-10-red?logo=laravel)
![PHP](https://img.shields.io/badge/PHP-8.2+-777BB4?logo=php)
![Bootstrap](https://img.shields.io/badge/Bootstrap-5-7952B3?logo=bootstrap)
![MySQL](https://img.shields.io/badge/MySQL-MariaDB-4479A1?logo=mysql)
![License](https://img.shields.io/badge/License-Internal-orange)

---

## Overview

SIAP (Sistem Informasi Administrasi Rapat) adalah aplikasi berbasis web yang dirancang untuk mengelola proses administrasi rapat secara terpusat. Sistem menyediakan pengelolaan peminjaman ruang rapat, penjadwalan, complaint fasilitas, absensi berbasis QR Code, manajemen data karyawan, serta konfigurasi master data dalam satu platform yang terintegrasi.

---

## Key Features

- Dashboard monitoring peminjaman ruang secara real-time
- Manajemen peminjaman ruang rapat
- Kalender penggunaan ruang
- Complaint management
- QR Code attendance
- Employee & User Management
- Room & Facility Management
- Notification System
- Master Data Configuration
- Role-Based Access Control (RBAC)

## Technology Stack

| Layer | Technology |
|--------|------------|
| Backend | Laravel 10 |
| Language | PHP 8.2+ |
| Database | MySQL / MariaDB |
| ORM | Eloquent ORM |
| Frontend | Blade Template |
| UI Framework | Bootstrap 5 |
| Icons | Bootstrap Icons |
| Styling | HTML5, CSS3, JavaScript |
| Build Tool | Vite |

## System Requirements

- PHP 8.2 atau lebih baru
- Composer 2.x
- Node.js 18+
- npm 9+
- MySQL 8.x atau MariaDB 10.x
- Laravel 10

## Business Rules

| Item | Value |
|------|-------|
| Operating Hours | 07:00 – 16:00 WIB |
| Booking Interval | 30 Minutes |
| Working Days | Monday – Friday |
| Weekend Booking | Not Allowed |
| Conflict Detection | Automatic |
| Booking Status | Pending, Approved, Rejected |

## Security

- Authentication
- Authorization (RBAC)
- CSRF Protection
- Password Hashing (Bcrypt)
- Request Validation
- Session Management
- Route Middleware

## Demo Login

| Role | Username | Password |
|------|----------|----------|
| Administrator | `ADM001` | `password` |
| Karyawan | `ITD004` | `password` |

## License

This project is proprietary software developed exclusively for internal operational use within PT Petrokopindo Cipta Selaras.

Unauthorized copying, distribution, modification, or public disclosure of any part of this project is prohibited without prior written permission.

© 2026 PT Petrokopindo Cipta Selaras. All rights reserved.
