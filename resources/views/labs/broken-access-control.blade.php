@extends('layouts.app')

@section('title', 'Broken Access Control Lab')
@section('nav_label', 'Lab navigation')

@section('navigation')
    <a href="/">Home</a>
    <a href="/labs/file-upload-security">Previous Lab</a>
@endsection

@section('content')
    <main class="lab-page">
        <section class="lab-hero" aria-labelledby="broken-access-control-title">
            <h1 id="broken-access-control-title">Broken Access Control Lab</h1>
            <p class="lab-lede">
                Broken access control happens when an application checks that someone is logged in,
                but forgets to verify what that user is allowed to do.
            </p>
        </section>

        <div class="content-stack">
            <section class="card" aria-labelledby="overview-title">
                <h2 id="overview-title">Overview</h2>
                <p>
                    This lab compares an admin report route that checks authentication only with a secure route
                    that also checks the user's role through a Laravel Gate.
                </p>
            </section>

            <section class="card login-status" aria-labelledby="login-required-title">
                <h2 id="login-required-title">Login Required</h2>
                <dl>
                    <dt>Test email</dt>
                    <dd><code>user@example.com</code> or <code>admin@example.com</code></dd>
                    <dt>Test password</dt>
                    <dd><code>password</code></dd>
                    <dt>Role/user</dt>
                    <dd><code>user@example.com</code> is a normal user. <code>admin@example.com</code> is an admin.</dd>
                </dl>
                <p>
                    <a href="/login">Log in at /login</a>. Login is needed because the vulnerable example still
                    requires authentication; the security failure is that it forgets the admin authorization check.
                </p>
            </section>

            <section class="card" aria-labelledby="vulnerable-code-title">
                <span class="badge danger">&#9888; Vulnerable Example</span>
                <h2 id="vulnerable-code-title">Vulnerable Example</h2>
                <p>
                    This route allows any logged-in application user to read the admin report because it only
                    applies the default authentication middleware.
                </p>
                <pre><code><span class="code-keyword">Route</span>::middleware(<span class="code-string">'auth'</span>)->group(<span class="code-keyword">function</span> () {
    <span class="code-keyword">Route</span>::get(<span class="code-string">'/labs/broken-access-control/vulnerable/admin-report'</span>, <span class="code-keyword">function</span> () {
        <span class="code-keyword">return</span> response()->json([
            <span class="code-string">'report'</span> => <span class="code-string">'Quarterly admin revenue report'</span>,
        ]);
    });
});</code></pre>
                <p>
                    <a href="/labs/broken-access-control/vulnerable/admin-report">Open vulnerable admin report</a>
                </p>
            </section>

            <section class="card" aria-labelledby="secure-code-title">
                <span class="badge success">&#10003; Secure Example</span>
                <h2 id="secure-code-title">Secure Example</h2>
                <p>
                    The secure route still requires login, then asks Laravel's authorization layer whether the
                    logged-in user may view the admin report.
                </p>
                <pre><code><span class="code-keyword">use</span> <span class="code-class">App\Models\User</span>;
<span class="code-keyword">use</span> <span class="code-class">Illuminate\Support\Facades\Gate</span>;

<span class="code-keyword">Gate</span>::define(<span class="code-string">'view-admin-report'</span>, <span class="code-keyword">fn</span> (<span class="code-class">User</span> $user): <span class="code-keyword">bool</span> =&gt; $user-&gt;role === <span class="code-string">'admin'</span>);

<span class="code-keyword">Route</span>::middleware(<span class="code-string">'auth'</span>)->group(<span class="code-keyword">function</span> () {
    <span class="code-keyword">Route</span>::get(<span class="code-string">'/labs/broken-access-control/secure/admin-report'</span>, <span class="code-keyword">function</span> () {
        <span class="code-keyword">return</span> response()->json([
            <span class="code-string">'report'</span> => <span class="code-string">'Quarterly admin revenue report'</span>,
        ]);
    })-&gt;can(<span class="code-string">'view-admin-report'</span>);
});</code></pre>
                <p>
                    <a href="/labs/broken-access-control/secure/admin-report">Open secure admin report</a>
                </p>
            </section>

            <section class="card" aria-labelledby="difference-title">
                <h2 id="difference-title">Key Difference</h2>
                <ul class="lesson-list">
                    <li>Authentication proves who the user is.</li>
                    <li>Authorization proves what the user is allowed to access.</li>
                    <li>The secure route checks both before returning admin-only data.</li>
                </ul>
            </section>

            <section class="card" aria-labelledby="lesson-title">
                <h2 id="lesson-title">Security Lesson</h2>
                <p>
                    Protect sensitive actions with explicit authorization checks. A route that is behind login can
                    still be vulnerable if it does not check role, ownership, policy, or capability.
                </p>
            </section>

            <section class="card" aria-labelledby="testing-title">
                <h2 id="testing-title">Testing Guide</h2>
                <table>
                    <thead>
                        <tr>
                            <th>Scenario</th>
                            <th>Endpoint</th>
                            <th>Expected Behavior</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Guest</td>
                            <td><code>GET /labs/broken-access-control/vulnerable/admin-report</code></td>
                            <td>Redirects to <code>/login</code>.</td>
                        </tr>
                        <tr>
                            <td>Normal user</td>
                            <td><code>GET /labs/broken-access-control/vulnerable/admin-report</code></td>
                            <td>Returns the report because only login is checked.</td>
                        </tr>
                        <tr>
                            <td>Normal user</td>
                            <td><code>GET /labs/broken-access-control/secure/admin-report</code></td>
                            <td>Returns 403 because the user is not an admin.</td>
                        </tr>
                        <tr>
                            <td>Admin user</td>
                            <td><code>GET /labs/broken-access-control/secure/admin-report</code></td>
                            <td>Returns the report.</td>
                        </tr>
                    </tbody>
                </table>
            </section>

            <div class="action-row">
                <a class="button secondary" href="/">Back to Home</a>
                <a class="button" href="/labs/file-upload-security">Previous Lab</a>
            </div>
        </div>
    </main>
@endsection
