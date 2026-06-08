@extends('layouts.app')

@section('title', 'File Upload Security Lab')
@section('nav_label', 'Lab navigation')

@section('navigation')
    <a href="/">Home</a>
    <a href="/labs/mass-assignment">Previous Lab</a>
    <a href="/labs/broken-access-control">Next Lab</a>
@endsection

@section('content')
    <main class="lab-page">
        <section class="lab-hero" aria-labelledby="file-upload-title">
            <h1 id="file-upload-title">File Upload Security Lab</h1>
            <p class="lab-lede">
                File uploads need strict validation, private storage, and authenticated downloads.
                This lab compares a risky public upload with a safer Laravel upload flow.
            </p>
        </section>

        <div class="content-stack">
            <section class="card" aria-labelledby="overview-title">
                <h2 id="overview-title">Overview</h2>
                <p>
                    Upload endpoints should treat every file as untrusted input. A secure flow validates the file,
                    stores it outside the public web root, and only serves it through application-controlled routes.
                </p>
            </section>

            <section class="card login-status" aria-labelledby="login-required-title">
                <h2 id="login-required-title">Login Required</h2>
                <dl>
                    <dt>Test email</dt>
                    <dd><code>customer1@example.com</code></dd>
                    <dt>Test password</dt>
                    <dd><code>password</code></dd>
                    <dt>Role/user</dt>
                    <dd>Lab customer allowed to request controlled downloads.</dd>
                </dl>
                <p>
                    <a href="/login">Log in at /login</a>. Upload validation is public for the demo, but the
                    controlled download route requires login so private files are served through application access checks.
                </p>
            </section>

            <section class="card" aria-labelledby="vulnerable-code-title">
                <span class="badge danger">&#9888; Vulnerable Example</span>
                <h2 id="vulnerable-code-title">Vulnerable Example</h2>
                <p>
                    This example accepts any uploaded file, trusts the client filename, and stores the file publicly.
                    It is displayed only to explain the mistake.
                </p>
                <pre><code><span class="code-keyword">Route</span>::post(<span class="code-string">'/profile/document'</span>, <span class="code-keyword">function</span> (<span class="code-class">Request</span> $request) {
    $request->file(<span class="code-string">'document'</span>)->move(
        public_path(<span class="code-string">'uploads'</span>),
        $request->file(<span class="code-string">'document'</span>)->getClientOriginalName()
    );

    <span class="code-keyword">return</span> back();
});</code></pre>
            </section>

            <section class="card" aria-labelledby="secure-code-title">
                <span class="badge success">&#10003; Secure Example</span>
                <h2 id="secure-code-title">Secure Example</h2>
                <p>
                    This lab route validates a PDF MIME type and extension, limits the size,
                    lets Laravel generate the filename, and stores the file on the private disk.
                </p>
                <pre><code><span class="code-keyword">Route</span>::post(<span class="code-string">'/labs/file-upload-security/secure'</span>, <span class="code-keyword">function</span> (<span class="code-class">Request</span> $request) {
    $request->validate([
        <span class="code-string">'document'</span> => [<span class="code-string">'required'</span>, <span class="code-string">'file'</span>, <span class="code-string">'mimes:pdf'</span>, <span class="code-string">'extensions:pdf'</span>, <span class="code-string">'max:2048'</span>],
    ]);

    $storedDocumentPath = $request->file(<span class="code-string">'document'</span>)
        ->store(<span class="code-string">'documents'</span>, <span class="code-string">'private'</span>);

    <span class="code-keyword">return</span> response()->json([
        <span class="code-string">'path'</span> => $storedDocumentPath,
        <span class="code-string">'filename'</span> => basename($storedDocumentPath),
    ], 201);
});</code></pre>

                <h3>Controlled Download</h3>
                <pre><code><span class="code-keyword">Route</span>::middleware(<span class="code-string">'auth:customer'</span>)->group(<span class="code-keyword">function</span> () {
    <span class="code-keyword">Route</span>::get(<span class="code-string">'/labs/file-upload-security/download'</span>, <span class="code-keyword">function</span> (<span class="code-class">Request</span> $request) {
        $downloadPath = $request->validate([
            <span class="code-string">'path'</span> => [<span class="code-string">'required'</span>, <span class="code-string">'string'</span>, <span class="code-string">'starts_with:documents/'</span>],
        ])[<span class="code-string">'path'</span>];

        abort_unless(<span class="code-class">Storage</span>::disk(<span class="code-string">'private'</span>)->exists($downloadPath), 404);

        <span class="code-keyword">return</span> <span class="code-class">Storage</span>::disk(<span class="code-string">'private'</span>)->download($downloadPath);
    });
});</code></pre>
            </section>

            <section class="card" aria-labelledby="checklist-title">
                <h2 id="checklist-title">Security Checklist</h2>
                <ul class="lesson-list">
                    <li>Validate MIME type.</li>
                    <li>Validate extension.</li>
                    <li>Limit file size.</li>
                    <li>Store privately.</li>
                    <li>Use generated filenames.</li>
                    <li>Scan uploads if needed.</li>
                    <li>Authorize downloads.</li>
                    <li>Never trust client filenames.</li>
                </ul>
            </section>

            <section class="card" aria-labelledby="testing-title">
                <h2 id="testing-title">Testing Guide</h2>
                <p>
                    Upload a small PDF to the secure endpoint and confirm Laravel returns a generated path under
                    <code>documents/</code>. Try a non-PDF file or a PDF over 2 MB and confirm validation rejects it.
                    If a temporary sharing flow is needed, use a signed application route that still checks access before returning the file.
                </p>
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
                            <td>Secure upload</td>
                            <td><code>POST /labs/file-upload-security/secure</code></td>
                            <td>Stores valid PDFs on the private disk with generated filenames.</td>
                        </tr>
                        <tr>
                            <td>Controlled download</td>
                            <td><code>GET /labs/file-upload-security/download</code></td>
                            <td>Requires an authenticated lab customer before serving a stored document.</td>
                        </tr>
                    </tbody>
                </table>
            </section>

            <div class="action-row">
                <a class="button secondary" href="/">Back to Home</a>
                <a class="button" href="/labs/mass-assignment">Previous Lab</a>
                <a class="button" href="/labs/broken-access-control">Next Lab</a>
            </div>
        </div>
    </main>
@endsection
