<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Mass Assignment Lab</title>
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
            max-width: 760px;
            line-height: 1.6;
        }

        pre {
            overflow-x: auto;
            padding: 16px;
            background: #202124;
            color: #f7f7f4;
        }

        code {
            background: #ecece6;
            padding: 2px 5px;
            border-radius: 4px;
        }

        pre code {
            background: transparent;
            padding: 0;
            border-radius: 0;
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
    </style>
</head>
<body>
    <main>
        <h1>Mass Assignment Lab</h1>

        <p>
            Mass assignment happens when an application passes request input directly into a model create
            or update call. If sensitive attributes are accepted, a user can set fields the form never meant
            to expose.
        </p>

        <h2>Example Payload</h2>
        <pre><code>{
  "name": "Demo User",
  "email": "demo@example.com",
  "role": "admin",
  "is_verified": true
}</code></pre>

        <h2>Endpoints</h2>
        <table>
            <thead>
                <tr>
                    <th>Example</th>
                    <th>Endpoint</th>
                    <th>Behavior</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Vulnerable</td>
                    <td><code>POST /labs/mass-assignment/vulnerable</code></td>
                    <td>Uses <code>Profile::create($request-&gt;all())</code>, so submitted <code>role</code> and <code>is_verified</code> values are stored.</td>
                </tr>
                <tr>
                    <td>Secure</td>
                    <td><code>POST /labs/mass-assignment/secure</code></td>
                    <td>Validates and stores only <code>name</code> and <code>email</code>. The protected fields keep their database defaults.</td>
                </tr>
            </tbody>
        </table>

        <h2>Expected Defensive Result</h2>
        <p>
            The vulnerable route shows the mistake clearly. The secure route treats client input as untrusted,
            validates the intended profile fields, and never passes unexpected attributes into the model.
        </p>

        <p><a href="/labs/idor">Back to Lab 01</a></p>
    </main>
</body>
</html>
