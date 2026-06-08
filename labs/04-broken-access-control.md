# Lab 04: Broken Access Control in Laravel

## What is Broken Access Control?

Broken access control happens when an application lets a user perform an action or read a resource they should not be allowed to access.

In Laravel, this often appears when a route checks authentication with `auth` middleware but does not check authorization with a Gate, policy, middleware, or ownership-scoped query.

## Authentication vs Authorization

Authentication answers: who is logged in?

Authorization answers: what is this logged-in user allowed to do?

The vulnerable route in this lab requires login, but any logged-in user can read the admin report. The secure route requires login and then verifies that the user has the `admin` role.

## Demo Accounts

| Role | Email | Password |
| --- | --- | --- |
| Admin user | `admin@example.com` | `password` |
| Normal user | `user@example.com` | `password` |

Use `/login` to sign in before testing the admin report routes.

## Vulnerable Laravel Example

The vulnerable example checks authentication only:

```php
Route::middleware('auth')->group(function () {
    Route::get('/labs/broken-access-control/vulnerable/admin-report', function () {
        return response()->json([
            'report' => 'Quarterly admin revenue report',
            'access_pattern' => 'Authenticated, but not authorized by role.',
        ]);
    });
});
```

This proves the request came from a logged-in user, but it never proves that the user is an admin.

## Secure Laravel Fix

The secure example uses a Laravel Gate:

```php
use App\Models\User;
use Illuminate\Support\Facades\Gate;

Gate::define('view-admin-report', fn (User $user): bool => $user->role === 'admin');
```

The route then applies the authorization check:

```php
Route::middleware('auth')->group(function () {
    Route::get('/labs/broken-access-control/secure/admin-report', function () {
        return response()->json([
            'report' => 'Quarterly admin revenue report',
            'access_pattern' => 'Authorized admin user.',
        ]);
    })->can('view-admin-report');
});
```

A normal logged-in user receives a 403 response. An admin user receives the report.

## Defensive Testing Approach

Test the behavior from the HTTP boundary:

1. Request the vulnerable admin report while logged out and confirm Laravel redirects to `/login`.
2. Log in as `user@example.com` and confirm the vulnerable report route succeeds.
3. Stay logged in as `user@example.com` and confirm the secure report route returns 403.
4. Log in as `admin@example.com` and confirm the secure report route succeeds.

These tests catch the missing authorization check without depending on private implementation details.

## SaaS/ERP Example

In a SaaS billing or ERP system, a normal employee account may need access to their own dashboard but not to company-wide revenue exports, payroll reports, or admin configuration. If those admin routes only check that the employee is logged in, the employee can reach actions intended for administrators.

The secure pattern is to check a role, permission, policy, or ownership rule before returning sensitive data or running privileged actions.
