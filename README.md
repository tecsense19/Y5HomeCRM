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

---

## 🗄 Database Tables

### Phase 1 (Active)
- `tbl_users`
- `tbl_customers`
- `tbl_leads`
- `tbl_opportunities`
- `tbl_experience_centers`
- `tbl_builders`
- `tbl_architects`
- `tbl_site_visits`
- `tbl_quotations`
- `tbl_documents`

### Future Ready (Structure only)
- `tbl_projects`
- `tbl_installations`
- `tbl_warranty`
- `tbl_service_tickets`
- `tbl_amc`
- `tbl_partner_commissions`
- `tbl_inventory`
- `tbl_purchase_orders`

---

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

## 🔐 Default Login Credentials

| Role | Email | Password |
|------|-------|----------|
| Super Admin | admin@y5home.com | admin@123 |
| Sales Manager | manager@y5home.com | manager@123 |
| Sales Executive | executive@y5home.com | exec@123 |
| EC User (Ahmedabad) | ec.ahmedabad@y5home.com | ec@123 |

> ⚠️ Change all passwords immediately after first login.

---

## 🔒 Role Permissions

| Feature | Super Admin | Sales Manager | Sales Executive | EC User |
|---------|-------------|---------------|-----------------|---------|
| View All Leads | ✅ | ✅ | Own only | Center only |
| Create Leads | ✅ | ✅ | ✅ | ❌ |
| Assign Leads | ✅ | ✅ | ❌ | ❌ |
| Delete Leads | ✅ | ❌ | ❌ | ❌ |
| Manage Users | ✅ | ❌ | ❌ | ❌ |
| View Reports | ✅ | ✅ | ❌ | ❌ |
| Update Status | ✅ | ✅ | ✅ | ✅ |

---

## 📁 Project Structure

```
app/
  Http/Controllers/
    AuthController.php          # Login, logout, profile
    DashboardController.php     # HQ & EC dashboards
    LeadController.php          # Full lead CRUD + assignment
    CustomerController.php      # Customer management
    ExperienceCenterController.php
    OpportunityController.php
    QuotationController.php     # PDF upload support
    SiteVisitController.php     # Site visit records
    DocumentController.php      # File upload/download
    ReportController.php        # 3 report types + CSV export
    UserController.php          # User management (admin only)
  Models/
    User.php | Lead.php | Customer.php | ExperienceCenter.php
    Opportunity.php | Quotation.php | SiteVisit.php | Document.php

database/
  migrations/                   # All 10 tables
  seeders/DatabaseSeeder.php    # Default users + sample data

resources/views/
  layouts/app.blade.php         # Main layout with sidebar
  auth/login.blade.php
  dashboard/index.blade.php
  leads/ (index, create, show, edit)
  customers/ | opportunities/ | quotations/
  site_visits/ | experience_centers/
  reports/ (index, lead_source, experience_center, sales_pipeline)
  users/

routes/web.php                  # All routes
```

---

## 🔧 Key Configuration

### File Upload (`.env`)
```env
MAX_UPLOAD_SIZE=25600   # 25 MB in KB
```

### Mail Setup (for automations)
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your_user
MAIL_PASSWORD=your_pass
MAIL_FROM_ADDRESS=noreply@y5home.com
```

### Queue (for email automations)
```bash
# For production, use database or redis queue
QUEUE_CONNECTION=database
php artisan queue:table
php artisan migrate
php artisan queue:work
```

---

## 📊 Lead Pipeline Flow

```
New Lead → Contacted → Qualified → Site Visit Scheduled
→ Site Visit Completed → Quotation Sent → Negotiation → Won / Lost
```

**Auto-trigger:** When lead status = `Qualified`, an Opportunity is automatically created.

---

## 🔮 Future Phases (Ready for Extension)

The database already has placeholder tables for:
- Project Management (`tbl_projects`)
- Installation Tracking (`tbl_installations`)
- Warranty Management (`tbl_warranty`)
- Service Tickets / AMC (`tbl_service_tickets`, `tbl_amc`)
- Partner Commissions (`tbl_partner_commissions`)
- Inventory & Purchase Orders (`tbl_inventory`, `tbl_purchase_orders`)

---

## 📝 Phase 1 Acceptance Criteria

- [x] Lead can be created in < 2 minutes
- [x] Lead can be assigned to Experience Center
- [x] Experience Center can update status
- [x] Site visit records can be uploaded
- [x] Quotation PDFs can be uploaded
- [x] Management dashboard works
- [x] Reports generate correctly
- [x] User permissions work correctly
- [x] System supports future modules without data migration

---

## 🛡 Security

- Role-based access control on all routes
- CSRF protection on all forms
- Password hashing (bcrypt)
- Soft deletes (data preserved)
- SSL ready (configure in web server)
- Session-based authentication

---

*Y5Home CRM — Phase 1 MVP | Built per SRS v1.0*
