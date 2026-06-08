<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Laravel Security Lab')</title>
    <style>
        :root {
            color-scheme: light;
            --background: #ffffff;
            --surface: #ffffff;
            --surface-muted: #f6f8fa;
            --border: #d8dee4;
            --border-soft: #eaeef2;
            --text: #1f2328;
            --muted: #59636e;
            --accent: #0f766e;
            --accent-strong: #0f5f59;
            --danger: #b42318;
            --danger-bg: #fff1f0;
            --success: #1f7a4d;
            --success-bg: #ecfdf3;
            --code: #24292f;
            --code-keyword: #8250df;
            --code-class: #0550ae;
            --code-string: #0a7f42;
            --shadow: 0 10px 24px rgba(31, 35, 40, 0.06);
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            background: var(--background);
            color: var(--text);
            font-family: Inter, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            line-height: 1.6;
        }

        a {
            color: var(--accent);
            text-decoration: none;
        }

        a:hover,
        a:focus-visible {
            color: var(--accent-strong);
            text-decoration: underline;
        }

        h1,
        h2,
        h3,
        p {
            margin-top: 0;
        }

        h1 {
            margin-bottom: 14px;
            font-size: clamp(38px, 7vw, 62px);
            line-height: 1.05;
            letter-spacing: 0;
        }

        h2 {
            margin-bottom: 12px;
            font-size: clamp(24px, 4vw, 32px);
            line-height: 1.2;
            letter-spacing: 0;
        }

        h3 {
            margin-bottom: 8px;
            font-size: 20px;
            line-height: 1.3;
        }

        code {
            padding: 2px 5px;
            border-radius: 5px;
            background: var(--surface-muted);
            color: var(--code);
            font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", monospace;
            font-size: 0.92em;
        }

        pre {
            margin: 0;
            overflow-x: auto;
            border: 1px solid var(--border-soft);
            border-radius: 8px;
            background: var(--surface-muted);
        }

        pre code {
            display: block;
            padding: 18px;
            background: transparent;
            line-height: 1.65;
            white-space: pre;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            border: 1px solid var(--border);
            border-radius: 8px;
            background: var(--surface);
            overflow: hidden;
        }

        th,
        td {
            padding: 14px 16px;
            border-bottom: 1px solid var(--border-soft);
            text-align: left;
            vertical-align: top;
        }

        th {
            background: var(--surface-muted);
            color: var(--muted);
            font-size: 13px;
            font-weight: 700;
            text-transform: uppercase;
        }

        tr:last-child td {
            border-bottom: 0;
        }

        button,
        .button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 42px;
            padding: 10px 14px;
            border: 1px solid var(--accent);
            border-radius: 7px;
            background: var(--accent);
            color: #ffffff;
            cursor: pointer;
            font: inherit;
            font-weight: 700;
            text-decoration: none;
        }

        button:hover,
        button:focus-visible,
        .button:hover,
        .button:focus-visible {
            background: var(--accent-strong);
            color: #ffffff;
            text-decoration: none;
        }

        .button.secondary {
            background: #ffffff;
            color: var(--accent);
        }

        .button.secondary:hover,
        .button.secondary:focus-visible {
            background: var(--surface-muted);
            color: var(--accent-strong);
        }

        .page {
            width: min(100% - 40px, 1040px);
            margin: 0 auto;
        }

        .site-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
            padding: 22px 0;
            border-bottom: 1px solid var(--border-soft);
        }

        .brand {
            color: var(--text);
            font-size: 16px;
            font-weight: 800;
        }

        .brand:hover,
        .brand:focus-visible {
            color: var(--accent);
            text-decoration: none;
        }

        .nav-links,
        .action-row,
        .link-row {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
        }

        .nav-links a {
            padding: 7px 10px;
            border-radius: 7px;
            color: var(--muted);
            font-size: 14px;
            font-weight: 700;
        }

        .nav-links a:hover,
        .nav-links a:focus-visible {
            background: var(--surface-muted);
            color: var(--text);
            text-decoration: none;
        }

        .hero {
            max-width: 780px;
            margin: 0 auto;
            padding: 72px 0 46px;
            text-align: center;
        }

        .subtitle {
            margin-bottom: 10px;
            color: var(--text);
            font-size: clamp(18px, 3vw, 24px);
            font-weight: 650;
        }

        .author,
        .muted {
            color: var(--muted);
        }

        .intro {
            max-width: 690px;
            margin: 28px auto 0;
            color: var(--muted);
            font-size: 17px;
        }

        .section {
            padding: 34px 0;
        }

        .section-header {
            max-width: 720px;
            margin-bottom: 18px;
        }

        .grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 18px;
        }

        .card {
            padding: 24px;
            border: 1px solid var(--border);
            border-radius: 8px;
            background: var(--surface);
            box-shadow: var(--shadow);
        }

        .lab-card {
            display: flex;
            flex-direction: column;
            min-height: 220px;
        }

        .lab-card p {
            color: var(--muted);
        }

        .lab-card .button {
            width: fit-content;
            margin-top: auto;
        }

        .lab-page {
            padding: 0 0 64px;
        }

        .lab-page h1 {
            font-size: clamp(38px, 7vw, 62px);
        }

        .narrow-page {
            max-width: 460px;
        }

        .lab-lede {
            max-width: 780px;
            margin-right: auto;
            margin-left: auto;
            color: var(--muted);
            font-size: 18px;
        }

        .lab-hero {
            max-width: 780px;
            margin: 0 auto;
            padding: 72px 0 18px;
            text-align: center;
        }

        .content-stack {
            display: grid;
            gap: 22px;
            margin-top: 34px;
        }

        .badge {
            display: inline-flex;
            width: fit-content;
            margin-bottom: 14px;
            padding: 5px 9px;
            border-radius: 999px;
            font-size: 13px;
            font-weight: 800;
        }

        .badge.danger {
            background: var(--danger-bg);
            color: var(--danger);
        }

        .badge.success {
            background: var(--success-bg);
            color: var(--success);
        }

        .code-keyword {
            color: var(--code-keyword);
            font-weight: 700;
        }

        .code-class {
            color: var(--code-class);
            font-weight: 700;
        }

        .code-string {
            color: var(--code-string);
        }

        .lesson-list {
            margin: 12px 0 0;
            padding-left: 22px;
        }

        .account-bar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            margin-top: 18px;
            padding: 16px;
            border: 1px solid var(--border);
            border-radius: 8px;
            background: var(--surface-muted);
        }

        .form-panel {
            display: grid;
            gap: 16px;
            margin-top: 24px;
            padding: 24px;
            border: 1px solid var(--border);
            border-radius: 8px;
            background: var(--surface);
            box-shadow: var(--shadow);
        }

        .form-panel label {
            display: grid;
            gap: 6px;
            font-weight: 700;
        }

        .form-panel input {
            min-height: 42px;
            padding: 10px 12px;
            border: 1px solid var(--border);
            border-radius: 7px;
            font: inherit;
        }

        .error {
            color: var(--danger);
            font-weight: 700;
        }

        .spaced-link {
            margin-top: 18px;
        }

        .site-footer {
            padding: 28px 0 40px;
            border-top: 1px solid var(--border-soft);
            color: var(--muted);
            font-size: 14px;
        }

        @media (max-width: 760px) {
            .page {
                width: min(100% - 28px, 1040px);
            }

            .site-header,
            .account-bar {
                align-items: flex-start;
                flex-direction: column;
            }

            .hero {
                padding-top: 46px;
                text-align: left;
            }

            .lab-hero {
                padding-top: 46px;
                text-align: left;
            }

            .intro {
                margin-left: 0;
            }

            .lab-lede {
                margin-left: 0;
            }

            .grid {
                grid-template-columns: 1fr;
            }

            .card {
                padding: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="page">
        <header class="site-header">
            <a class="brand" href="/">Laravel Security Lab</a>

            <nav class="nav-links" aria-label="@yield('nav_label', 'Primary navigation')">
                @yield('navigation')
            </nav>
        </header>

        @yield('content')

        <footer class="site-footer">
            Built by <strong>Mohamed Adam</strong>
        </footer>
    </div>
</body>
</html>
