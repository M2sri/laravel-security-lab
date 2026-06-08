# Lab 02: Mass Assignment in Laravel

## Overview

Mass assignment is risky when request input is passed directly into `create()` or `update()`. A user can submit fields the form never intended to expose.

## Login required: No

No login required for this lab demo.

## Vulnerable code

```php
Route::post('/labs/mass-assignment/vulnerable', function (Request $request) {
    $profile = Profile::create($request->all());
    $profile->refresh();

    return response()->json([
        'name' => $profile->name,
        'email' => $profile->email,
        'role' => $profile->role,
        'is_verified' => $profile->is_verified,
    ], 201);
});
```

## Secure code

```php
Route::post('/labs/mass-assignment/secure', function (Request $request) {
    $validatedProfileFields = $request->validate([
        'name' => ['required', 'string', 'max:255'],
        'email' => ['required', 'email', 'max:255'],
    ]);

    $profile = Profile::create($validatedProfileFields);
    $profile->refresh();

    return response()->json([
        'name' => $profile->name,
        'email' => $profile->email,
        'role' => $profile->role,
        'is_verified' => $profile->is_verified,
    ], 201);
});
```

## Key difference

The vulnerable route saves every submitted field. The secure route saves only validated `name` and `email` fields.

## How to test

1. Send `name`, `email`, `role`, and `is_verified` to `/labs/mass-assignment/vulnerable`.
2. Confirm the vulnerable response stores `role` or `is_verified`.
3. Send the same payload to `/labs/mass-assignment/secure`.
4. Confirm the secure response keeps protected fields at their defaults.

## Security lesson

Validate input and pass only intended fields into model writes.
