# Lab 04: Broken Access Control in Laravel

## Overview

Broken access control happens when a user can access an action or resource they are not allowed to use.

## Login required: Yes

Use `/login` with either seeded application user.

| User | Email | Password |
| --- | --- | --- |
| Admin user | `admin@example.com` | `password` |
| Normal user | `user@example.com` | `password` |

## Vulnerable code

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

## Secure code

```php
use App\Models\User;
use Illuminate\Support\Facades\Gate;

Gate::define('view-admin-report', fn (User $user): bool => $user->role === 'admin');

Route::middleware('auth')->group(function () {
    Route::get('/labs/broken-access-control/secure/admin-report', function () {
        return response()->json([
            'report' => 'Quarterly admin revenue report',
            'access_pattern' => 'Authorized admin user.',
        ]);
    })->can('view-admin-report');
});
```

## Key difference

Authentication proves who is logged in. Authorization proves what that user is allowed to access.

## How to test

1. Request the vulnerable admin report while logged out and confirm it redirects to `/login`.
2. Log in as `user@example.com` and confirm the vulnerable report route succeeds.
3. Confirm `user@example.com` receives 403 from the secure report route.
4. Log in as `admin@example.com` and confirm the secure report route succeeds.

## Security lesson

Admin-only routes need explicit authorization checks, not only login checks.
