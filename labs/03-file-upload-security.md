# Lab 03: File Upload Security in Laravel

## Overview

File uploads should be treated as untrusted input. A safer flow validates type and size, stores files privately, and serves downloads through application routes.

## Login required: Yes

Upload validation is public for this demo. Controlled downloads require a customer login.

| User | Email | Password |
| --- | --- | --- |
| Customer One | `customer1@example.com` | `password` |

## Vulnerable code

```php
Route::post('/profile/document', function (Request $request) {
    $request->file('document')->move(
        public_path('uploads'),
        $request->file('document')->getClientOriginalName()
    );

    return back();
});
```

## Secure code

```php
Route::post('/labs/file-upload-security/secure', function (Request $request) {
    $request->validate([
        'document' => ['required', 'file', 'mimes:pdf', 'extensions:pdf', 'max:2048'],
    ]);

    $storedDocumentPath = $request->file('document')->store('documents', 'private');

    return response()->json([
        'path' => $storedDocumentPath,
        'filename' => basename($storedDocumentPath),
    ], 201);
});

Route::middleware('auth:customer')->group(function () {
    Route::get('/labs/file-upload-security/download', function (Request $request) {
        $downloadPath = $request->validate([
            'path' => ['required', 'string', 'starts_with:documents/'],
        ])['path'];

        abort_unless(Storage::disk('private')->exists($downloadPath), 404);

        return Storage::disk('private')->download($downloadPath);
    });
});
```

## Key difference

The vulnerable example trusts the original filename and stores publicly. The secure route validates PDFs, uses a generated filename, stores privately, and requires login for downloads.

## How to test

1. Upload a small PDF to `/labs/file-upload-security/secure`.
2. Confirm the JSON path starts with `documents/`.
3. Upload a non-PDF and confirm validation rejects it.
4. Upload a file over 2 MB and confirm validation rejects it.
5. Request `/labs/file-upload-security/download` while logged out and confirm it redirects to `/login`.

## Security lesson

Validate uploads, store them outside the public web root, and authorize downloads through application code.
