# Laravel Security Lab

Laravel Security Lab is a small defensive training project for learning common web application security issues in Laravel.

The repo is intentionally hands-on. Each lab includes vulnerable code, a safer Laravel implementation, seeded demo data, and tests where practical.

## What This Repo Teaches

- How common web vulnerabilities appear in Laravel applications.
- How to compare insecure and secure route patterns.
- How to use Eloquent relationships for authorization-aware data access.
- How to write defensive feature tests around security behavior.
- How to keep security exercises local and controlled.

## Current Labs

| Lab | Topic | Routes | Notes |
| --- | --- | --- | --- |
| 01 | IDOR Protection | `/labs/idor`, `/labs/idor/vulnerable/invoices/{invoice}`, `/labs/idor/secure/invoices/{invoice}` | Demonstrates why direct object lookup with `Invoice::findOrFail($id)` is risky and how to scope invoice access to the authenticated customer. |
| 02 | Mass Assignment | `/labs/mass-assignment`, `/labs/mass-assignment/vulnerable`, `/labs/mass-assignment/secure` | Demonstrates why trusting `$request->all()` is risky and how to store only validated profile fields. |
| 03 | File Upload Security | `/labs/file-upload-security`, `/labs/file-upload-security/secure`, `/labs/file-upload-security/download` | Demonstrates PDF validation, generated filenames, private storage, and authenticated downloads. |
| 04 | Broken Access Control | `/labs/broken-access-control`, `/labs/broken-access-control/vulnerable/admin-report`, `/labs/broken-access-control/secure/admin-report` | Demonstrates why login is not enough for admin-only routes and how to add a Gate authorization check. |

## Lab 01: IDOR Protection

Lab 01 shows an Insecure Direct Object Reference issue using customer invoices.

Seeded customer accounts:

| Customer | Email | Password |
| --- | --- | --- |
| Customer One | `customer1@example.com` | `password` |
| Customer Two | `customer2@example.com` | `password` |

Seeded invoices:

| Invoice | Owner |
| --- | --- |
| `IDOR-LAB-001` | Customer One |
| `IDOR-LAB-002` | Customer Two |

Use `/login` to sign in as a lab customer, then open `/labs/idor` to compare vulnerable and secure invoice links.

The vulnerable invoice route loads invoices directly by ID. The secure invoice route requires the customer guard and searches through the authenticated customer's `invoices()` relationship.

The lab notes are in [labs/01-idor.md](labs/01-idor.md).

## Lab 02: Mass Assignment

Lab 02 shows how trusting request input can allow protected profile attributes to be changed.

The vulnerable endpoint uses `Profile::create($request->all())`, so submitted `role` and `is_verified` values are stored. The secure endpoint validates and stores only `name` and `email`, leaving protected fields at their defaults.

The lab page is available at `/labs/mass-assignment`, and the lab notes are in [labs/02-mass-assignment.md](labs/02-mass-assignment.md).

## Lab 03: File Upload Security

Lab 03 shows how unsafe upload handling can expose files.

The secure upload endpoint validates that the uploaded document is a PDF, limits the size to 2 MB, stores the file on the `private` disk, and uses Laravel-generated filenames. The controlled download route requires a logged-in lab customer.

The lab page is available at `/labs/file-upload-security`, and the lab notes are in [labs/03-file-upload-security.md](labs/03-file-upload-security.md).

## Lab 04: Broken Access Control

Lab 04 shows how an admin-only report can be exposed when a route checks authentication but not authorization.

Seeded application user accounts:

| Role | Email | Password |
| --- | --- | --- |
| Admin | `admin@example.com` | `password` |
| User | `user@example.com` | `password` |

The vulnerable admin report route allows any authenticated application user. The secure route uses the `view-admin-report` Gate and only allows users whose `role` is `admin`.

The lab page is available at `/labs/broken-access-control`, and the lab notes are in [labs/04-broken-access-control.md](labs/04-broken-access-control.md).

## Install Locally

Requirements:

- PHP 8.2 or newer
- Composer
- A database supported by Laravel; the default `.env.example` uses SQLite

Clone the repo, install PHP dependencies, and create your local environment file:

```bash
composer install
cp .env.example .env
php artisan key:generate
```

On Windows PowerShell, use this instead of `cp`:

```powershell
Copy-Item .env.example .env
php artisan key:generate
```

If you use the default SQLite connection, create the database file before running migrations:

```powershell
New-Item -ItemType File database/database.sqlite
```

On macOS or Linux:

```bash
touch database/database.sqlite
```

## Run Migrations and Seeders

Run all migrations and seed the demo data:

```bash
php artisan migrate --seed
```

To refresh the local database from scratch:

```bash
php artisan migrate:fresh --seed
```

`DatabaseSeeder` creates the Lab 04 admin and normal user accounts, then registers `IdorLabSeeder`, which creates the two lab customers and two lab invoices.

## Run the App

Start the Laravel development server:

```bash
php artisan serve
```

Then visit:

- `/login` for the lab login form
- `/labs/idor` for the IDOR lab home page
- `/labs/broken-access-control` for the broken access control lab

## Run Tests

Run the full test suite:

```bash
php artisan test
```

Run only the IDOR feature tests:

```bash
php artisan test --filter=IdorProtectionTest
```

Run only the Mass Assignment feature tests:

```bash
php artisan test --filter=MassAssignmentTest
```

Run only the Broken Access Control feature tests:

```bash
php artisan test --filter=BrokenAccessControlTest
```

## Security Disclaimer

This repository is for defensive learning only. Use it to understand, test, and fix vulnerabilities in local lab environments. Do not use these examples to target systems you do not own or have explicit permission to test.
