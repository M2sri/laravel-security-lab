# Lab 01: IDOR Protection in Laravel

## What is IDOR?

Insecure Direct Object Reference, or IDOR, happens when an application exposes a direct identifier for a record and does not verify that the current user is allowed to access that record.

## Login Required

Use `/login` with either seeded lab customer:

| Customer | Email | Password | Represents |
| --- | --- | --- | --- |
| Customer One | `customer1@example.com` | `password` | Customer who owns invoice `IDOR-LAB-001` |
| Customer Two | `customer2@example.com` | `password` | Customer who owns invoice `IDOR-LAB-002` |

Login is needed because the vulnerable and secure invoice routes both require the `customer` guard before they demonstrate the difference between direct lookup and owner-scoped lookup.

For example, a customer might load:

```text
/labs/idor/secure/invoices/10
```

If changing `10` to `11` reveals another customer's invoice, the application has an IDOR vulnerability.

## Vulnerable Example

The vulnerable route accepts an invoice ID from the URL and loads it directly:

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

`Invoice::findOrFail($invoice)` only proves that the invoice exists. It does not prove that the authenticated customer owns it.

## Why It Is Dangerous

An attacker does not need advanced tools to exploit this pattern. If they are logged in as one customer, they can change the ID in the URL and try to access invoices that belong to other customers.

This can expose sensitive billing details, customer identifiers, payment amounts, and any other data stored on the invoice.

## Secure Laravel Fix

Scope the invoice lookup through the authenticated customer:

```php
Route::get('/labs/idor/secure/invoices/{invoice}', function (int $invoice) {
    $customer = auth('customer')->user();
    $invoiceRecord = $customer->invoices()->findOrFail($invoice);

    return response()->json([
        'invoice_number' => $invoiceRecord->invoice_number,
        'customer_id' => $invoiceRecord->customer_id,
        'amount' => $invoiceRecord->amount,
    ]);
});
```

This query only searches invoices owned by the authenticated customer. If the invoice exists but belongs to someone else, Laravel returns a 404 response.

## How to Test It Defensively

Create two customers and one invoice for each customer.

1. Log in as customer A.
2. Request customer A's invoice from `/labs/idor/secure/invoices/{invoice}` and confirm the request succeeds.
3. Still logged in as customer A, request customer B's invoice from the same secure route and confirm the request fails.
4. Request the secure route while logged out and confirm Laravel redirects to `/login`.

These tests verify authorization behavior from the customer's perspective instead of testing framework internals.
