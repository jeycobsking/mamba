# Mamba Village Nairobi — Tourism & Operations Platform

A complete, full-stack tourism, e-ticketing, and wildlife park operations management system built with **PHP (PDO)**, **MySQL**, **HTML5**, **CSS3**, **Bootstrap 5**, **Tailwind CSS**, and **Vanilla JavaScript**.

---

## Key Highlights

* **Real Photography & Real Icons:** Zero emojis used. Uses authentic high-resolution wildlife photography (Unsplash) and real vector icons (Bootstrap Icons & Font Awesome 6).
* **Separate Administrative Web Portal:** A dedicated, standalone back-office located at `/admin/` with role-based clearance for Administrators, Finance Managers, Event Planners, Tour Guides, and Supply Managers.
* **Authentication & Access Control:**
  * **Public Visitors:** Can explore attractions, feeding timetables, about, and reviews, but must authenticate (Login / Register) before booking tickets, accessing the digital QR pass wallet, submitting reviews, or booking institutional packages.
  * **Staff / Administration:** Only authenticated personnel with valid staff clearance can access the `/admin/` portal.
* **Security Hardening:**
  * Strict PDO prepared statements with parameter binding (immune to SQL injection).
  * Password hashing using `PASSWORD_BCRYPT`.
  * CSRF protection tokens on all POST forms (`hash_equals()` validation).
  * XSS prevention with `htmlspecialchars()`.
  * Session fixation mitigation with `session_regenerate_id(true)` and cookie hardening (`httponly`, `samesite=Lax`).
* **Lipa Na M-Pesa STK Push Integration:** Realistic Safaricom Daraja STK Push payment simulation generating authentic transaction references and immediate scannable SVG QR tickets.
* **Gate Turnstile QR Scanner:** Turnstile scanner in the admin portal validates QR ticket codes, admits guests, and flags duplicate/already-used passes.

---

## System Accounts & Credentials

| Role | Email | Password | Access Portal |
| :--- | :--- | :--- | :--- |
| **Super Administrator** | `admin@mambavillage.co.ke` | `Admin@2026` | Staff Portal (`/admin/login.php`) |
| **Finance Manager** | `finance@mambavillage.co.ke` | `Admin@2026` | Staff Portal (`/admin/login.php`) |
| **Event Manager** | `events@mambavillage.co.ke` | `Admin@2026` | Staff Portal (`/admin/login.php`) |
| **Tour Guide** | `guide@mambavillage.co.ke` | `Admin@2026` | Staff Portal (`/admin/login.php`) |
| **Supply Manager** | `supplier@mambavillage.co.ke` | `Admin@2026` | Staff Portal (`/admin/login.php`) |
| **Verified Visitor** | `visitor@gmail.com` | `Visitor@2026` | Public Site (`/login.php`) |

---

## Project Structure

```
mamba-village/
├── config/
│   ├── config.php          # Session settings, CSRF token generator, URL helpers
│   └── database.php        # Secure PDO MySQL singleton
├── includes/
│   ├── auth.php            # RBAC guards, session regeneration, login/logout
│   ├── functions.php       # Formatting, activity logging, sanitization
│   ├── header.php          # Global head with Bootstrap 5, Tailwind CDN, Icons
│   ├── navbar.php          # Navigation bar with user dropdown & login states
│   └── footer.php          # Global footer and scripts
├── assets/
│   ├── css/
│   │   └── custom.css      # Modern typography, glassmorphism, print styles
│   └── js/
│       └── main.js         # Alert auto-dismiss and UI utilities
├── admin/                  # SEPARATE ADMIN PORTAL
│   ├── includes/
│   │   ├── admin_auth.php  # Staff clearance guard
│   │   ├── admin_header.php# Top navigation & identity badge
│   │   ├── admin_sidebar.php# Role-filtered sidebar
│   │   └── admin_footer.php
│   ├── index.php           # Executive dashboard & KPIs
│   ├── login.php           # Dedicated staff login portal
│   ├── logout.php          # Staff logout
│   ├── bookings.php        # Gate register & 1-click admission
│   ├── scanner.php         # Dedicated turnstile QR pass scanner
│   ├── finances.php        # Finance ledger & CSV statement export
│   ├── events.php          # Feeding shows & event scheduler
│   ├── guides.php          # Tour guide allocations & roster
│   ├── suppliers.php       # Supply chain POs & delivery tracker
│   ├── users.php           # User & staff accounts management
│   └── reports.php         # Attendance flow chart & revenue share
├── index.php               # Public homepage with live status & metrics
├── attractions.php         # Sanctuary exhibits & guided tours catalog
├── schedule.php            # Feeding show timetable with category filters
├── book.php                # E-ticketing engine (Authentication required)
├── group-booking.php       # School & Corporate inquiries (Authentication required)
├── my-bookings.php         # User's digital ticket wallet (Authentication required)
├── ticket-view.php         # Official printable SVG QR pass & invoice
├── reviews.php             # Verified visitor ratings & reviews
├── about.php               # Conservation history & biodiversity mission
├── contact.php             # Direct inquiries & directions
├── login.php               # Visitor login
├── register.php            # Visitor registration
├── logout.php              # Visitor logout
├── setup.php               # One-click database installer & seeder
├── schema.sql              # Normalized MySQL relational schema
└── start_server.bat        # 1-click Windows PHP local launcher
```

---

## How to Preview & Run

### Option 1: Via XAMPP Apache (Standard Web Server)
1. Make sure **Apache** and **MySQL** are running in the **XAMPP Control Panel**.
2. Open your web browser and navigate to:
   * **Public Visitor Portal:** [http://localhost/mamba-village/](http://localhost/mamba-village/)
   * **Staff & Admin Portal:** [http://localhost/mamba-village/admin/](http://localhost/mamba-village/admin/)
   * **Database Installer (if re-seeding):** [http://localhost/mamba-village/setup.php](http://localhost/mamba-village/setup.php)

### Option 2: 1-Click Batch Launcher (PHP Built-in Server)
Double-click `start_server.bat` in `C:\xampp\htdocs\mamba-village\start_server.bat`. It will launch PHP's server on `http://localhost:8080` and open your default browser automatically.
