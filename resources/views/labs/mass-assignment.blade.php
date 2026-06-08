@extends('layouts.app')

@section('title', 'Mass Assignment Lab')
@section('nav_label', 'Lab navigation')

@section('navigation')
    <a href="/">Home</a>
    <a href="/labs/idor">Previous Lab</a>
    <a href="/labs/file-upload-security">Next Lab</a>
@endsection

@section('content')
    <main class="lab-page">
        <section class="lab-hero" aria-labelledby="mass-assignment-title">
            <h1 id="mass-assignment-title">Mass Assignment Lab</h1>
            <p class="lab-lede">
                Mass assignment happens when request input is passed directly into a model create or update call.
                This can let a user set sensitive fields that were never meant to be editable.
            </p>
        </section>

        <div class="content-stack">
            <section class="card" aria-labelledby="overview-title">
                <h2 id="overview-title">Vulnerability Overview</h2>
                <p>
                    The vulnerable route stores every submitted field, including <code>role</code> and <code>is_verified</code>.
                    The secure route validates the intended fields and only saves those values.
                </p>
            </section>

            <section class="card login-status no-login" aria-labelledby="login-status-title">
                <h2 id="login-status-title">No Login Required</h2>
                <p>No login required for this lab demo.</p>
            </section>

            <section class="card" aria-labelledby="vulnerable-code-title">
                <span class="badge danger">&#9888; Vulnerable Example</span>
                <h2 id="vulnerable-code-title">Vulnerable Code Example</h2>
                <pre><code><span class="code-keyword">Route</span>::post(<span class="code-string">'/labs/mass-assignment/vulnerable'</span>, <span class="code-keyword">function</span> (<span class="code-class">Request</span> $request) {
    $profile = <span class="code-class">Profile</span>::create($request->all());
    $profile->refresh();

    <span class="code-keyword">return</span> response()->json([
        <span class="code-string">'name'</span> => $profile->name,
        <span class="code-string">'email'</span> => $profile->email,
        <span class="code-string">'role'</span> => $profile->role,
        <span class="code-string">'is_verified'</span> => $profile->is_verified,
    ], 201);
});</code></pre>
            </section>

            <section class="card" aria-labelledby="secure-code-title">
                <span class="badge success">&#10003; Secure Example</span>
                <h2 id="secure-code-title">Secure Code Example</h2>
                <pre><code><span class="code-keyword">Route</span>::post(<span class="code-string">'/labs/mass-assignment/secure'</span>, <span class="code-keyword">function</span> (<span class="code-class">Request</span> $request) {
    $validatedProfileFields = $request->validate([
        <span class="code-string">'name'</span> => [<span class="code-string">'required'</span>, <span class="code-string">'string'</span>, <span class="code-string">'max:255'</span>],
        <span class="code-string">'email'</span> => [<span class="code-string">'required'</span>, <span class="code-string">'email'</span>, <span class="code-string">'max:255'</span>],
    ]);

    $profile = <span class="code-class">Profile</span>::create($validatedProfileFields);
    $profile->refresh();

    <span class="code-keyword">return</span> response()->json([
        <span class="code-string">'name'</span> => $profile->name,
        <span class="code-string">'email'</span> => $profile->email,
        <span class="code-string">'role'</span> => $profile->role,
        <span class="code-string">'is_verified'</span> => $profile->is_verified,
    ], 201);
});</code></pre>
            </section>

            <section class="card" aria-labelledby="difference-title">
                <h2 id="difference-title">Key Difference</h2>
                <ul class="lesson-list">
                    <li>Accepts only fields the profile form is supposed to control.</li>
                    <li>Rejects invalid names and email addresses before saving.</li>
                    <li>Keeps sensitive fields such as <code>role</code> and <code>is_verified</code> at their defaults.</li>
                </ul>
            </section>

            <section class="card" aria-labelledby="testing-title">
                <h2 id="testing-title">Testing</h2>
                <p>Send this payload to both endpoints and compare the JSON responses.</p>
                <pre><code>{
  <span class="code-string">"name"</span>: <span class="code-string">"Demo User"</span>,
  <span class="code-string">"email"</span>: <span class="code-string">"demo@example.com"</span>,
  <span class="code-string">"role"</span>: <span class="code-string">"admin"</span>,
  <span class="code-string">"is_verified"</span>: <span class="code-keyword">true</span>
}</code></pre>

                <h3>Endpoints</h3>
                <table>
                    <thead>
                        <tr>
                            <th>Example</th>
                            <th>Endpoint</th>
                            <th>Expected Behavior</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Vulnerable</td>
                            <td><code>POST /labs/mass-assignment/vulnerable</code></td>
                            <td>Stores submitted <code>role</code> and <code>is_verified</code> values.</td>
                        </tr>
                        <tr>
                            <td>Secure</td>
                            <td><code>POST /labs/mass-assignment/secure</code></td>
                            <td>Stores only <code>name</code> and <code>email</code>; sensitive fields keep their defaults.</td>
                        </tr>
                    </tbody>
                </table>
            </section>

            <section class="card" aria-labelledby="lesson-title">
                <h2 id="lesson-title">Security Lesson</h2>
                <p>
                    Treat client input as untrusted. Validate the intended fields and pass only those fields into
                    model creation or updates.
                </p>
            </section>

            <div class="action-row">
                <a class="button secondary" href="/">Back to Home</a>
                <a class="button" href="/labs/idor">Previous Lab</a>
                <a class="button" href="/labs/file-upload-security">Next Lab</a>
            </div>
        </div>
    </main>
@endsection
