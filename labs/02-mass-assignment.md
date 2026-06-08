# Lab 02: Mass Assignment in Laravel

## Explanation

Mass assignment is the practice of passing an array of input directly into a model create or update operation.

That can be convenient for normal form fields, but it becomes dangerous when the request contains attributes the user should not control. In this lab, `role` and `is_verified` are sensitive profile fields.

## Login Requirement

No login required for this lab demo.

## Vulnerable Code

The vulnerable endpoint trusts every submitted field:

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

An attacker-controlled local lab payload can include fields that a normal form would not show:

```json
{
  "name": "Demo User",
  "email": "demo@example.com",
  "role": "admin",
  "is_verified": true
}
```

Because the route uses `$request->all()`, the submitted `role` and `is_verified` values are stored.

## Secure Code

The secure endpoint validates and stores only the fields this workflow is meant to accept:

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

Unexpected fields are ignored because they are not part of the validated data passed to `Profile::create()`.

## Defensive Testing Approach

Test the behavior from the HTTP boundary:

1. Send a local lab request to the vulnerable endpoint with `role` set to `admin`.
2. Confirm the vulnerable endpoint stores the submitted role.
3. Send a local lab request to the vulnerable endpoint with `is_verified` set to `true`.
4. Confirm the vulnerable endpoint stores the submitted verification flag.
5. Send the same unexpected fields to the secure endpoint.
6. Confirm only `name` and `email` are accepted while `role` and `is_verified` keep their default values.

These tests are for defensive learning in this repository only. Do not use these examples against real systems.
