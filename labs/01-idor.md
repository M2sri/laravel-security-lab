# Lab 01: IDOR Protection in Laravel

## Overview

IDOR happens when an application accepts a record ID and does not verify that the logged-in user owns that record.

## Login required: Yes

Use `/login` with either seeded customer account.

| User | Email | Password |
| --- | --- | --- |
| Customer One | `customer1@example.com` | `password` |
| Customer Two | `customer2@example.com` | `password` |

## Vulnerable code

```php
Route::get('/labs/idor/vulnerable/invoices/{invoice}', function (int $invoice) {
    $invoiceRecord = Invoice::findOrFail($invoice);

    return response()->json([
        'invoice_number' => $invoiceRecord->invoice_number,
        'customer_id' => $invoiceRecord->customer_id,
        'amount' => $invoiceRecord->amount,
    ]);
});
```

## Secure code

```php
Route::get('/labs/idor/secure/invoices/{invoice}', function (int $invoice) {
    $customer = Auth::guard('customer')->user();
    $invoiceRecord = $customer->invoices()->findOrFail($invoice);

    return response()->json([
        'invoice_number' => $invoiceRecord->invoice_number,
        'customer_id' => $invoiceRecord->customer_id,
        'amount' => $invoiceRecord->amount,
    ]);
});
```

## Key difference

The vulnerable route loads any invoice by ID. The secure route searches only invoices owned by the authenticated customer.

## How to test

1. Log in as `customer1@example.com`.
2. Open Customer One and Customer Two invoice links on `/labs/idor`.
3. Confirm the vulnerable link can expose another customer's invoice.
4. Confirm the secure link only returns invoices owned by the logged-in customer.
5. Log out and confirm secure invoice routes redirect to `/login`.

## Security lesson

Never trust a user-controlled ID by itself. Scope sensitive records through the authenticated user or an authorization policy.
