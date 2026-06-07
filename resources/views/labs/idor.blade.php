<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>IDOR Protection Lab</title>
    <style>
        body {
            margin: 0;
            background: #f7f7f4;
            color: #202124;
            font-family: ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
        }

        main {
            max-width: 960px;
            margin: 0 auto;
            padding: 48px 20px;
        }

        h1 {
            margin: 0 0 12px;
            font-size: 32px;
            font-weight: 700;
        }

        h2 {
            margin: 32px 0 12px;
            font-size: 20px;
        }

        p {
            max-width: 720px;
            line-height: 1.6;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background: #ffffff;
            border: 1px solid #d8d8d3;
        }

        th,
        td {
            padding: 14px 16px;
            border-bottom: 1px solid #e7e7e2;
            text-align: left;
            vertical-align: top;
        }

        th {
            background: #ecece6;
            font-size: 13px;
            text-transform: uppercase;
        }

        a {
            color: #0f5f70;
            font-weight: 600;
        }

        code {
            background: #ecece6;
            padding: 2px 5px;
            border-radius: 4px;
        }

        button {
            padding: 8px 12px;
            border: 0;
            background: #0f5f70;
            color: #ffffff;
            cursor: pointer;
            font: inherit;
            font-weight: 700;
        }

        form {
            margin: 0;
        }

        .account-bar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            margin: 24px 0;
            padding: 16px;
            background: #ffffff;
            border: 1px solid #d8d8d3;
        }

        .links {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
        }
    </style>
</head>
<body>
    <main>
        <h1>IDOR Protection Lab</h1>

        <p>
            Use these accounts to compare a vulnerable direct invoice lookup with a secure lookup scoped
            to the authenticated customer.
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
                <a href="/login">Log in</a>
            @endif
        </div>

        <h2>Login Test Accounts</h2>
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

        <h2>Invoice Links</h2>
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
                            <div class="links">
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

        <h2>Expected Behavior</h2>
        <p>
            The vulnerable links use <code>Invoice::findOrFail($id)</code>, so any authenticated customer can
            request another customer's invoice by changing the ID. The secure links scope the lookup through
            the authenticated customer's invoices, so another customer's invoice returns a 404.
        </p>
        <p>
            This login flow and seeded data are intentionally simple and only for the lab/demo environment.
        </p>
    </main>
</body>
</html>
