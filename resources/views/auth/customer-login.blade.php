<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>IDOR Lab Login</title>
    <style>
        body {
            margin: 0;
            background: #f7f7f4;
            color: #202124;
            font-family: ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
        }

        main {
            max-width: 420px;
            margin: 0 auto;
            padding: 56px 20px;
        }

        h1 {
            margin: 0 0 10px;
            font-size: 30px;
        }

        p {
            line-height: 1.6;
        }

        form {
            display: grid;
            gap: 16px;
            margin-top: 24px;
            padding: 24px;
            background: #ffffff;
            border: 1px solid #d8d8d3;
        }

        label {
            display: grid;
            gap: 6px;
            font-weight: 600;
        }

        input {
            padding: 10px 12px;
            border: 1px solid #c9c9c3;
            font: inherit;
        }

        button {
            padding: 10px 14px;
            border: 0;
            background: #0f5f70;
            color: #ffffff;
            cursor: pointer;
            font: inherit;
            font-weight: 700;
        }

        a {
            color: #0f5f70;
            font-weight: 600;
        }

        .error {
            color: #9f1d20;
            font-weight: 600;
        }
    </style>
</head>
<body>
    <main>
        <h1>IDOR Lab Login</h1>
        <p>This customer login is only for the local IDOR lab demo.</p>

        @if ($errors->any())
            <p class="error">{{ $errors->first() }}</p>
        @endif

        <form method="POST" action="/login">
            @csrf

            <label>
                Email
                <input name="email" type="email" value="{{ old('email') }}" required autofocus>
            </label>

            <label>
                Password
                <input name="password" type="password" required>
            </label>

            <button type="submit">Log in</button>
        </form>

        <p><a href="/labs/idor">Back to the lab page</a></p>
    </main>
</body>
</html>
