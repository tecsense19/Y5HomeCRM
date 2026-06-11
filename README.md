# Y5Home CRM & Experience Center Management System

**Phase 1 – MVP Release**  
Built with Laravel 10 | Bootstrap 5 | MySQL

---

## 🗂 Project Overview

A centralized CRM system for Y5Home to manage leads, customers, experience centers, site visits, quotations, and sales pipeline — replacing Excel/WhatsApp-based tracking.

---

## ✅ Features Implemented (Phase 1)

| Module | Features |
|--------|----------|
| **Authentication** | Login, logout, profile, remember me |
| **Role-Based Access** | Super Admin, Sales Manager, Sales Executive, Experience Center |
| **Lead Management** | Create, assign, pipeline tracking, status updates, lost reasons |
| **Customer Management** | Full customer profiles, history |
| **Experience Centers** | Center master, documents, status management |
| **Site Visits** | Visit records, products required, file attachments |
| **Opportunities** | Auto-created on lead qualification, stage tracking |
| **Quotations** | PDF uploads, versioning, status flow |
| **Documents** | File manager per record (PDF, DOCX, XLSX, JPG, PNG, max 25MB) |
| **Dashboard** | HQ + EC dashboards with stats, pipeline, follow-ups |
| **Reports** | Lead Source, Experience Center Performance, Sales Pipeline + CSV export |
| **Automations** | Email triggers on lead create, assign, won |

## 🚀 Installation

### Requirements
- PHP 8.1+
- MySQL 8.0+
- Composer
- Node.js (optional, for asset compilation)

### Step 1: Clone & Install

```bash
git clone <repo-url> y5home-crm
cd y5home-crm
composer install
```

### Step 2: Environment Setup

```bash
cp .env.example .env
php artisan key:generate
```

Edit `.env` with your database credentials:
```env
DB_DATABASE=y5home_crm
DB_USERNAME=root
DB_PASSWORD=your_password
```

### Step 3: Database Setup

```bash
# Create database
mysql -u root -p -e "CREATE DATABASE y5home_crm CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

# Run migrations
php artisan migrate

# Seed default data
php artisan db:seed
```

### Step 4: Storage & Permissions

```bash
php artisan storage:link
chmod -R 775 storage bootstrap/cache
```

### Step 5: Run

```bash
php artisan serve
```

Open: http://localhost:8000

---

## 🔧 Key Configuration

### File Upload (`.env`)
```env
MAX_UPLOAD_SIZE=25600   # 25 MB in KB
```

*Y5Home CRM — Phase 1 MVP | Built per SRS v1.0*
