<?php

use App\Models\Invoice;
use App\Models\Profile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/login', function () {
    return view('auth.customer-login');
})->name('login');

Route::post('/login', function (Request $request) {
    $credentials = $request->validate([
        'email' => ['required', 'email'],
        'password' => ['required', 'string'],
    ]);

    if (! Auth::guard('customer')->attempt($credentials)) {
        return back()
            ->withErrors(['email' => 'The provided credentials do not match the lab customers.'])
            ->onlyInput('email');
    }

    $request->session()->regenerate();

    return redirect('/labs/idor');
});

Route::post('/logout', function (Request $request) {
    Auth::guard('customer')->logout();

    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return redirect('/labs/idor');
});

Route::get('/labs/idor', function () {
    $labInvoices = Invoice::query()
        ->with('customer')
        ->whereIn('invoice_number', ['IDOR-LAB-001', 'IDOR-LAB-002'])
        ->orderBy('invoice_number')
        ->get();

    return view('labs.idor', [
        'authenticatedCustomer' => Auth::guard('customer')->user(),
        'labInvoices' => $labInvoices,
    ]);
});

Route::get('/labs/mass-assignment', function () {
    return view('labs.mass-assignment');
});

Route::get('/labs/file-upload-security', function () {
    return view('labs.file-upload-security');
});

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
    Route::get('/labs/idor/vulnerable/invoices/{invoice}', function (int $invoice) {
        $invoiceRecord = Invoice::findOrFail($invoice);

        return response()->json([
            'invoice_number' => $invoiceRecord->invoice_number,
            'customer_id' => $invoiceRecord->customer_id,
            'amount' => $invoiceRecord->amount,
        ]);
    });

    Route::get('/labs/idor/secure/invoices/{invoice}', function (int $invoice) {
        $customer = Auth::guard('customer')->user();
        $invoiceRecord = $customer->invoices()->findOrFail($invoice);

        return response()->json([
            'invoice_number' => $invoiceRecord->invoice_number,
            'customer_id' => $invoiceRecord->customer_id,
            'amount' => $invoiceRecord->amount,
        ]);
    });

    Route::get('/labs/file-upload-security/download', function (Request $request) {
        $downloadPath = $request->validate([
            'path' => ['required', 'string', 'starts_with:documents/'],
        ])['path'];

        abort_unless(Storage::disk('private')->exists($downloadPath), 404);

        return Storage::disk('private')->download($downloadPath);
    });
});
