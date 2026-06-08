@extends('layouts.app')

@section('title', 'IDOR Protection Lab')
@section('nav_label', 'Lab navigation')

@section('navigation')
    <a href="/">Home</a>
    <a href="/labs/mass-assignment">Next Lab</a>
@endsection

@section('content')
    <main class="lab-page">
        <section class="lab-hero" aria-labelledby="idor-title">
            <h1 id="idor-title">IDOR Protection Lab</h1>
            <p class="lab-lede">
                Insecure Direct Object Reference happens when a user can request a record by ID without proving ownership.
                This lab compares a direct invoice lookup with a customer-scoped lookup.
            </p>
        </section>

        <div class="content-stack">
            <section class="card" aria-labelledby="overview-title">
                <h2 id="overview-title">Vulnerability Overview</h2>
                <p>
                    The vulnerable route accepts an invoice ID and returns that invoice for any authenticated customer.
                    A secure route scopes the query through the logged-in customer before returning the record.
                </p>
            </section>

            <section class="card login-status" aria-labelledby="login-required-title">
                <h2 id="login-required-title">Login Required</h2>
                <dl>
                    <dt>Test email</dt>
                    <dd><code>customer1@example.com</code> or <code>customer2@example.com</code></dd>
                    <dt>Test password</dt>
                    <dd><code>password</code></dd>
                    <dt>Role/user</dt>
                    <dd>Lab customer with owned invoices.</dd>
                </dl>
                <p>
                    <a href="/login">Log in at /login</a>. Login is needed because both invoice demo routes use
                    the customer guard before comparing vulnerable direct lookup with secure owner-scoped lookup.
                </p>
            </section>

            <section class="card" aria-labelledby="vulnerable-code-title">
                <span class="badge danger">&#9888; Vulnerable Example</span>
                <h2 id="vulnerable-code-title">Vulnerable Code Example</h2>
                <pre><code><span class="code-keyword">Route</span>::get(<span class="code-string">'/labs/idor/vulnerable/invoices/{invoice}'</span>, <span class="code-keyword">function</span> (<span class="code-keyword">int</span> $invoice) {
    $invoiceRecord = <span class="code-class">Invoice</span>::findOrFail($invoice);

    <span class="code-keyword">return</span> response()->json([
        <span class="code-string">'invoice_number'</span> => $invoiceRecord->invoice_number,
        <span class="code-string">'customer_id'</span> => $invoiceRecord->customer_id,
        <span class="code-string">'amount'</span> => $invoiceRecord->amount,
    ]);
});</code></pre>
            </section>

            <section class="card" aria-labelledby="secure-code-title">
                <span class="badge success">&#10003; Secure Example</span>
                <h2 id="secure-code-title">Secure Code Example</h2>
                <pre><code><span class="code-keyword">Route</span>::get(<span class="code-string">'/labs/idor/secure/invoices/{invoice}'</span>, <span class="code-keyword">function</span> (<span class="code-keyword">int</span> $invoice) {
    $customer = <span class="code-class">Auth</span>::guard(<span class="code-string">'customer'</span>)->user();
    $invoiceRecord = $customer->invoices()->findOrFail($invoice);

    <span class="code-keyword">return</span> response()->json([
        <span class="code-string">'invoice_number'</span> => $invoiceRecord->invoice_number,
        <span class="code-string">'customer_id'</span> => $invoiceRecord->customer_id,
        <span class="code-string">'amount'</span> => $invoiceRecord->amount,
    ]);
});</code></pre>
            </section>

            <section class="card" aria-labelledby="difference-title">
                <h2 id="difference-title">Key Difference</h2>
                <ul class="lesson-list">
                    <li>Validates invoice ownership through the authenticated customer relationship.</li>
                    <li>Prevents users from reading another customer's invoice by changing the ID.</li>
                    <li>Returns a 404 when the invoice does not belong to the current customer.</li>
                </ul>
            </section>

            <section class="card" aria-labelledby="testing-title">
                <h2 id="testing-title">Testing</h2>
                <p>
                    Log in as one lab customer, then open both invoice links. The vulnerable endpoint can expose
                    another customer's invoice, while the secure endpoint only returns invoices owned by the logged-in customer.
                </p>

                <div class="account-bar">
                    @if ($authenticatedCustomer)
                        <span>Logged in as <strong>{{ $authenticatedCustomer->email }}</strong></span>
                        <form method="POST" action="/logout">
                            @csrf
                            <button type="submit">Log out</button>
                        </form>
                    @else
                        <span>You are not logged in as a lab customer.</span>
                        <a class="button secondary" href="/login">Log in</a>
                    @endif
                </div>

                <h3>Login Test Accounts</h3>
                <table>
                    <thead>
                        <tr>
                            <th>Customer</th>
                            <th>Email</th>
                            <th>Password</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Customer One</td>
                            <td><code>customer1@example.com</code></td>
                            <td><code>password</code></td>
                        </tr>
                        <tr>
                            <td>Customer Two</td>
                            <td><code>customer2@example.com</code></td>
                            <td><code>password</code></td>
                        </tr>
                    </tbody>
                </table>

                <h3>Invoice Links</h3>
                <table>
                    <thead>
                        <tr>
                            <th>Invoice</th>
                            <th>Owner</th>
                            <th>Links</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($labInvoices as $labInvoice)
                            <tr>
                                <td>{{ $loop->iteration === 1 ? 'Invoice #1' : 'Invoice #2' }}</td>
                                <td>{{ $labInvoice->customer->name }}<br><code>{{ $labInvoice->customer->email }}</code></td>
                                <td>
                                    <div class="link-row">
                                        <a href="/labs/idor/vulnerable/invoices/{{ $labInvoice->id }}">Vulnerable</a>
                                        <a href="/labs/idor/secure/invoices/{{ $labInvoice->id }}">Secure</a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3">Run <code>php artisan db:seed --class=IdorLabSeeder</code> to create the lab invoices.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </section>

            <section class="card" aria-labelledby="lesson-title">
                <h2 id="lesson-title">Security Lesson</h2>
                <p>
                    Never trust a user-controlled ID by itself. Query sensitive records through the authenticated user's
                    relationship or authorization policy before returning data.
                </p>
            </section>

            <div class="action-row">
                <a class="button secondary" href="/">Back to Home</a>
                <a class="button" href="/labs/mass-assignment">Next Lab</a>
            </div>
        </div>
    </main>
@endsection
