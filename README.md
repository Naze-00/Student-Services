# Student Services Management System

A web-based Student Services Management Module built for a school system. Staff users can manage students, view service requests, and approve or reject them. Built with Laravel 13, Livewire 3, and MySQL.

---

## Tech Stack

| Layer      | Technology                  |
|------------|-----------------------------|
| Backend    | Laravel 13.8                |
| Frontend   | Livewire 3 + Blade          |
| Styling    | Tailwind CSS                |
| Database   | MySQL 8                     |
| Queue      | Laravel Database Queue      |
| Excel      | Maatwebsite Laravel Excel   |
| API Auth   | Laravel Sanctum             |

---

## Features

- **Authentication** — Login system with Admin and Staff roles
- **Student Management** — Full CRUD with validation (Admin only for create/edit/delete)
- **Service Requests** — Create, view, approve, and reject requests (ID Replacement, Good Moral Certificate, Form 137)
- **Filtering** — Filter requests by status and date range
- **REST API** — JSON endpoints for students and service requests
- **Excel Import** — Bulk import via `.xlsx` file with background queue processing, data normalization, duplicate detection, and import logs

---

## Requirements

- PHP 8.2 or higher
- Composer
- Node.js 18+ and npm
- MySQL 8.0+
- Laravel Herd or any local PHP server

---

## Installation

### 1. Clone the repository

```bash
git clone https://github.com/YOUR_USERNAME/student-services-management.git
cd student-services-management
```

### 2. Install dependencies

```bash
composer install
npm install
```

### 3. Set up environment

```bash
cp .env.example .env
php artisan key:generate
```

Edit `.env` with your database credentials:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=test_information
DB_USERNAME=root
DB_PASSWORD=

QUEUE_CONNECTION=database
```

### 4. Run migrations and seed

```bash
php artisan migrate --seed
```

### 5. Build frontend assets

```bash
npm run dev
```

### 6. Start the queue worker

Open a **separate terminal** and keep this running for Excel imports to process:

```bash
php artisan queue:work --tries=3
```

### 7. Start the server

```bash
php artisan serve
```

Visit: **http://localhost:8000**

---

## Default Accounts

| Role  | Email            | Password |
|-------|------------------|----------|
| Admin | admin@school.com | password |
| Staff | staff@school.com | password |

---

## Role Permissions

| Feature                         | Admin | Staff |
|---------------------------------|-------|-------|
| Login                           | ✅    | ✅    |
| View students                   | ✅    | ✅    |
| Create / Edit / Delete students | ✅    | ❌    |
| View service requests           | ✅    | ✅    |
| Create service requests         | ✅    | ✅    |
| Approve / Reject requests       | ✅    | ✅    |
| Delete service requests         | ✅    | ❌    |
| Excel import                    | ✅    | ❌    |
| View import logs                | ✅    | ❌    |

---

## Excel Import Format

Upload a `.xlsx` file with the following columns:

| Column         | Required | Description                     |
|----------------|----------|---------------------------------|
| student_number | Yes      | Student's ID number             |
| service_type   | Yes      | Type of service being requested |
| requested_date | Yes      | Date of the request             |

### Accepted Service Type Variations

| Input                                        | Normalized To          |
|----------------------------------------------|------------------------|
| `goodmoral`, `good moral`, `Good Moral Cert` | Good Moral Certificate |
| `id`, `ID repl`, `ID replace`                | ID Replacement         |
| `form137`, `form 137`                        | Form 137               |

### Import Business Rules

- **Missing student number** → row is skipped and logged
- **Student not found** → new student is auto-created and flagged as imported
- **Inactive student** → row is skipped and logged
- **Unknown service type** → row is skipped and logged
- **Duplicate request** (same student + service type + date) → row is skipped
- All results are saved to the `import_logs` table with a full summary
- Imports are processed asynchronously via Laravel Queue to prevent blocking

---

## API Endpoints

All endpoints require a Sanctum bearer token.

| Method | Endpoint                             | Description           |
|--------|--------------------------------------|-----------------------|
| GET    | `/api/students`                      | List all students     |
| GET    | `/api/service-requests`              | List service requests |
| PATCH  | `/api/service-requests/{id}/approve` | Approve a request     |
| PATCH  | `/api/service-requests/{id}/reject`  | Reject a request      |

### Query Parameters

**GET /api/students**
| Parameter | Description              |
|-----------|--------------------------|
| `status`  | Filter by Active/Inactive |
| `search`  | Search by name           |

**GET /api/service-requests**
| Parameter   | Description          |
|-------------|----------------------|
| `status`    | Filter by status     |
| `date_from` | Filter from date     |
| `date_to`   | Filter to date       |

---

## Architecture Overview

This project follows a **Monolithic MVC Architecture** using Laravel 13 with Livewire 3 full-page components replacing traditional controllers for web routes.

### Key Layers

| Layer            | Description                                                  |
|------------------|--------------------------------------------------------------|
| Routing          | Web routes use Livewire components. API routes use controllers protected by Sanctum |
| Livewire Components | Act as both controller and view-model for all web pages   |
| Eloquent Models  | Represent User, Student, ServiceRequest, and ImportLog       |
| Queue Jobs       | ProcessServiceRequestImport handles async Excel processing   |
| Middleware       | AdminMiddleware and StaffMiddleware enforce role-based access |

### Model Relationships

- A `Student` has many `ServiceRequests`
- A `ServiceRequest` belongs to a `Student`
- An `ImportLog` belongs to a `User`

---

## Concurrency & Business Rules

### Queue Processing
Excel imports are dispatched as background jobs via Laravel's database queue driver. This prevents large file uploads from blocking the HTTP response. The queue worker must be running separately:

```bash
php artisan queue:work --tries=3
```

### Duplicate Detection
The import job checks for duplicate service requests using a compound key of `student_id + service_type + date_requested` before inserting each row.

### Atomic Status Updates
Service request approvals and rejections use a single Eloquent `update()` call, translating to one atomic SQL UPDATE to prevent partial writes.

### Import Log Status
Each import job sets its status to `Processing` immediately, then updates to `Completed` or `Failed` when done. The UI auto-refreshes every 3 seconds while a job is processing.

---

## Windows / Herd Troubleshooting

If you get an `Access is denied` error on cached views:

```bash
php artisan view:clear
php artisan cache:clear
php artisan config:clear
php artisan route:clear
```

Or manually delete the contents of:

```
storage/framework/views/
```

If the problem persists, right-click the `storage` folder → Properties → Security → grant your user **Full Control**.

---

## Project Structure

```
app/
├── Http/
│   ├── Controllers/Api/      # StudentApiController, ServiceRequestApiController
│   └── Middleware/           # AdminMiddleware, StaffMiddleware
├── Jobs/
│   └── ProcessServiceRequestImport.php
├── Livewire/
│   ├── Auth/                 # Login
│   ├── Dashboard.php
│   ├── Students/             # StudentIndex, StudentCreate, StudentEdit
│   ├── ServiceRequests/      # ServiceRequestIndex, ServiceRequestCreate, ServiceRequestShow
│   └── Imports/              # ImportIndex
└── Models/
    ├── User.php
    ├── Student.php
    ├── ServiceRequest.php
    └── ImportLog.php

resources/views/
├── layouts/
│   ├── app.blade.php         # Authenticated layout with navigation
│   └── guest.blade.php       # Unauthenticated layout for login
└── livewire/                 # One Blade view per Livewire component

routes/
├── web.php                   # Livewire full-page component routes
└── api.php                   # Sanctum-protected REST API routes

database/
├── migrations/               # All table migrations
└── seeders/
    └── UserSeeder.php        # Seeds admin and staff accounts
```

