<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Laravel Security Lab</title>
    <style>
        :root {
            color-scheme: dark;
            --bg: #020711;
            --bg-soft: #061224;
            --panel: rgba(9, 22, 41, 0.78);
            --panel-strong: rgba(13, 32, 57, 0.92);
            --line: rgba(103, 232, 249, 0.18);
            --line-strong: rgba(56, 189, 248, 0.42);
            --text: #eef7ff;
            --muted: #9fb4c8;
            --cyan: #22d3ee;
            --blue: #38bdf8;
            --green: #6ee7b7;
            --shadow: rgba(0, 0, 0, 0.38);
        }

        * {
            box-sizing: border-box;
        }

        html {
            scroll-behavior: smooth;
        }

        body {
            margin: 0;
            min-height: 100vh;
            background:
                radial-gradient(circle at 18% 8%, rgba(34, 211, 238, 0.18), transparent 28rem),
                radial-gradient(circle at 82% 12%, rgba(37, 99, 235, 0.2), transparent 30rem),
                linear-gradient(145deg, #01040b 0%, var(--bg) 44%, #06101f 100%);
            color: var(--text);
            font-family: Inter, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
        }

        body::before {
            position: fixed;
            inset: 0;
            z-index: -1;
            pointer-events: none;
            content: "";
            background-image:
                linear-gradient(rgba(56, 189, 248, 0.04) 1px, transparent 1px),
                linear-gradient(90deg, rgba(56, 189, 248, 0.04) 1px, transparent 1px);
            background-size: 56px 56px;
            mask-image: linear-gradient(to bottom, black, transparent 82%);
        }

        a {
            color: inherit;
            text-decoration: none;
        }

        .page {
            width: min(1160px, calc(100% - 40px));
            margin: 0 auto;
        }

        .site-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 24px;
            padding: 22px 0;
        }

        .brand {
            display: inline-flex;
            align-items: center;
            gap: 12px;
            font-weight: 800;
        }

        .brand-mark {
            display: grid;
            width: 38px;
            height: 38px;
            place-items: center;
            border: 1px solid var(--line-strong);
            border-radius: 8px;
            background: linear-gradient(135deg, rgba(34, 211, 238, 0.2), rgba(56, 189, 248, 0.08));
            color: var(--cyan);
            box-shadow: 0 16px 44px var(--shadow);
        }

        .nav-links {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: 10px;
            flex-wrap: wrap;
        }

        .nav-links a {
            padding: 9px 12px;
            border: 1px solid transparent;
            border-radius: 8px;
            color: var(--muted);
            font-size: 14px;
            font-weight: 700;
        }

        .nav-links a:hover,
        .nav-links a:focus-visible {
            border-color: var(--line);
            color: var(--text);
            background: rgba(255, 255, 255, 0.04);
        }

        .hero {
            display: grid;
            grid-template-columns: minmax(0, 1.18fr) minmax(320px, 0.82fr);
            gap: 32px;
            align-items: stretch;
            padding: 68px 0 40px;
        }

        .hero-copy {
            padding: 44px;
            border: 1px solid var(--line);
            border-radius: 8px;
            background: linear-gradient(155deg, rgba(9, 22, 41, 0.88), rgba(3, 9, 18, 0.7));
            box-shadow: 0 24px 70px var(--shadow);
        }

        .eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            margin: 0 0 18px;
            color: var(--green);
            font-size: 13px;
            font-weight: 800;
            text-transform: uppercase;
        }

        .eyebrow::before {
            width: 10px;
            height: 10px;
            border-radius: 999px;
            background: var(--green);
            content: "";
            box-shadow: 0 0 22px rgba(110, 231, 183, 0.8);
        }

        h1 {
            max-width: 780px;
            margin: 0;
            font-size: clamp(44px, 7vw, 82px);
            line-height: 0.96;
            letter-spacing: 0;
        }

        .subtitle {
            margin: 22px 0 0;
            color: var(--blue);
            font-size: clamp(19px, 3vw, 28px);
            font-weight: 800;
        }

        .hero-description {
            max-width: 680px;
            margin: 20px 0 0;
            color: var(--muted);
            font-size: 18px;
            line-height: 1.75;
        }

        .hero-actions {
            display: flex;
            gap: 14px;
            flex-wrap: wrap;
            margin-top: 30px;
        }

        .button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 44px;
            padding: 11px 16px;
            border: 1px solid var(--line-strong);
            border-radius: 8px;
            color: var(--text);
            font-weight: 800;
        }

        .button.primary {
            border-color: rgba(34, 211, 238, 0.7);
            background: linear-gradient(135deg, rgba(34, 211, 238, 0.28), rgba(37, 99, 235, 0.24));
            box-shadow: 0 16px 44px rgba(34, 211, 238, 0.16);
        }

        .terminal-card {
            display: flex;
            flex-direction: column;
            min-height: 100%;
            border: 1px solid var(--line);
            border-radius: 8px;
            overflow: hidden;
            background: rgba(1, 7, 15, 0.84);
            box-shadow: 0 24px 70px var(--shadow);
        }

        .terminal-top {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 14px 16px;
            border-bottom: 1px solid var(--line);
            background: rgba(255, 255, 255, 0.04);
        }

        .dot {
            width: 10px;
            height: 10px;
            border-radius: 999px;
            background: var(--cyan);
            opacity: 0.8;
        }

        .terminal-body {
            display: grid;
            gap: 16px;
            padding: 22px;
            color: #c8f7ff;
            font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", monospace;
            font-size: 14px;
            line-height: 1.7;
        }

        .terminal-body span {
            color: var(--green);
        }

        section {
            padding: 38px 0;
        }

        .section-heading {
            display: flex;
            align-items: end;
            justify-content: space-between;
            gap: 24px;
            margin-bottom: 20px;
        }

        h2 {
            margin: 0;
            font-size: clamp(26px, 4vw, 38px);
            letter-spacing: 0;
        }

        .section-heading p {
            max-width: 520px;
            margin: 0;
            color: var(--muted);
            line-height: 1.7;
        }

        .stat-grid,
        .lab-grid,
        .about-grid {
            display: grid;
            gap: 18px;
        }

        .stat-grid {
            grid-template-columns: repeat(4, minmax(0, 1fr));
        }

        .lab-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .about-grid {
            grid-template-columns: 1fr 1fr;
        }

        .card {
            border: 1px solid var(--line);
            border-radius: 8px;
            background: var(--panel);
            box-shadow: 0 18px 48px rgba(0, 0, 0, 0.28);
        }

        .stat-card {
            padding: 22px;
        }

        .stat-card strong {
            display: block;
            color: var(--cyan);
            font-size: 30px;
            line-height: 1;
        }

        .stat-card span {
            display: block;
            margin-top: 10px;
            color: var(--muted);
            font-weight: 700;
        }

        .lab-card {
            display: flex;
            flex-direction: column;
            gap: 16px;
            min-height: 250px;
            padding: 26px;
            background: linear-gradient(160deg, var(--panel-strong), rgba(4, 12, 24, 0.84));
        }

        .lab-number {
            color: var(--green);
            font-size: 13px;
            font-weight: 900;
            text-transform: uppercase;
        }

        .lab-card h3 {
            margin: 0;
            font-size: 26px;
        }

        .lab-card p,
        .about-card p,
        .check-list {
            margin: 0;
            color: var(--muted);
            line-height: 1.7;
        }

        .lab-card .button {
            width: fit-content;
            margin-top: auto;
        }

        .about-card {
            padding: 26px;
        }

        .check-list {
            display: grid;
            gap: 12px;
            padding: 0;
            list-style: none;
        }

        .check-list li {
            display: grid;
            grid-template-columns: 22px 1fr;
            gap: 10px;
        }

        .check-list li::before {
            width: 18px;
            height: 18px;
            margin-top: 3px;
            border: 1px solid var(--line-strong);
            border-radius: 999px;
            content: "";
            background: radial-gradient(circle, var(--cyan) 0 35%, transparent 38%);
        }

        footer {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            padding: 34px 0 46px;
            border-top: 1px solid var(--line);
            color: var(--muted);
        }

        footer strong {
            color: var(--text);
        }

        @media (max-width: 900px) {
            .site-header,
            .section-heading,
            footer {
                align-items: flex-start;
                flex-direction: column;
            }

            .hero,
            .lab-grid,
            .about-grid {
                grid-template-columns: 1fr;
            }

            .stat-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .hero-copy {
                padding: 32px;
            }
        }

        @media (max-width: 560px) {
            .page {
                width: min(100% - 28px, 1160px);
            }

            .hero {
                padding-top: 36px;
            }

            .hero-copy,
            .lab-card,
            .about-card {
                padding: 22px;
            }

            .stat-grid {
                grid-template-columns: 1fr;
            }

            .nav-links {
                justify-content: flex-start;
            }
        }
    </style>
</head>
<body>
    <div class="page">
        <header class="site-header">
            <a class="brand" href="/">
                <span class="brand-mark">LS</span>
                <span>Laravel Security Lab</span>
            </a>

            <nav class="nav-links" aria-label="Primary navigation">
                <a href="/">Home</a>
                <a href="/labs/idor">IDOR Lab</a>
                <a href="/labs/mass-assignment">Mass Assignment Lab</a>
                <a href="#structure">GitHub-ready structure</a>
            </nav>
        </header>

        <main>
            <section class="hero">
                <div class="hero-copy">
                    <p class="eyebrow">Defensive application security</p>
                    <h1>Laravel Security Lab</h1>
                    <p class="subtitle">Practical web security labs built by Mohamed Adam</p>
                    <p class="hero-description">
                        A hands-on defensive web security lab built with Laravel, focused on OWASP-inspired
                        vulnerabilities, secure coding, access control, file uploads, and API security.
                    </p>
                    <div class="hero-actions">
                        <a class="button primary" href="/labs/idor">Start Lab 01</a>
                        <a class="button" href="/labs/mass-assignment">Explore Lab 02</a>
                    </div>
                </div>

                <aside class="terminal-card" aria-label="Project focus">
                    <div class="terminal-top">
                        <span class="dot"></span>
                        <span class="dot"></span>
                        <span class="dot"></span>
                    </div>
                    <div class="terminal-body">
                        <div><span>$</span> php artisan test</div>
                        <div>Access control checks: passing</div>
                        <div>Mass assignment checks: passing</div>
                        <div>Secure implementation notes: documented</div>
                        <div><span>focus:</span> learn vulnerabilities by fixing them</div>
                    </div>
                </aside>
            </section>

            <section aria-labelledby="stats-heading">
                <div class="section-heading">
                    <h2 id="stats-heading">Built For Practice</h2>
                    <p>Short labs, clear vulnerable examples, safer Laravel patterns, and tests that prove the defensive behavior.</p>
                </div>

                <div class="stat-grid">
                    <div class="card stat-card">
                        <strong>02</strong>
                        <span>Current Labs</span>
                    </div>
                    <div class="card stat-card">
                        <strong>Core</strong>
                        <span>Secure Coding</span>
                    </div>
                    <div class="card stat-card">
                        <strong>OWASP</strong>
                        <span>Inspired</span>
                    </div>
                    <div class="card stat-card">
                        <strong>Tests</strong>
                        <span>Tested Examples</span>
                    </div>
                </div>
            </section>

            <section aria-labelledby="labs-heading">
                <div class="section-heading">
                    <h2 id="labs-heading">Current Labs</h2>
                    <p>Each lab pairs a risky implementation with a defensive Laravel fix and verification path.</p>
                </div>

                <div class="lab-grid">
                    <article class="card lab-card">
                        <span class="lab-number">Lab 01</span>
                        <h3>IDOR Protection</h3>
                        <p>Learn how broken object access can expose another user's data.</p>
                        <a class="button primary" href="/labs/idor">Open IDOR Lab</a>
                    </article>

                    <article class="card lab-card">
                        <span class="lab-number">Lab 02</span>
                        <h3>Mass Assignment</h3>
                        <p>Learn why trusting request()->all() can expose sensitive fields.</p>
                        <a class="button primary" href="/labs/mass-assignment">Open Mass Assignment Lab</a>
                    </article>
                </div>
            </section>

            <section id="structure" aria-labelledby="about-heading">
                <div class="section-heading">
                    <h2 id="about-heading">About The Project</h2>
                    <p>Laravel Security Lab is organized to be readable in a browser and useful in code review.</p>
                </div>

                <div class="about-grid">
                    <div class="card about-card">
                        <p>
                            This project demonstrates vulnerable examples, secure implementations, and tests.
                            The goal is defensive learning: understand how mistakes happen, then see the Laravel
                            pattern that prevents them.
                        </p>
                    </div>

                    <div class="card about-card">
                        <ul class="check-list">
                            <li>Blade lab pages for quick exploration.</li>
                            <li>Markdown notes under the labs directory.</li>
                            <li>Feature tests that exercise the security behavior.</li>
                            <li>GitHub-ready structure for sharing and extending labs.</li>
                        </ul>
                    </div>
                </div>
            </section>
        </main>

        <footer>
            <span>Built by <strong>Mohamed Adam</strong></span>
            <span>Laravel Security Lab</span>
        </footer>
    </div>
</body>
</html>
