@extends('layouts.app')

@section('title', 'Laravel Security Lab')

@section('navigation')
    <a href="/">Home</a>
    <a href="/labs/idor">IDOR Lab</a>
    <a href="/labs/mass-assignment">Mass Assignment Lab</a>
    <a href="/labs/file-upload-security">File Upload Lab</a>
    <a href="https://github.com/M2sri/laravel-security-lab">GitHub</a>
@endsection

@section('content')
    <main>
        <section class="hero" aria-labelledby="homepage-title">
            <h1 id="homepage-title">Laravel Security Lab</h1>
            <p class="subtitle">Practical Laravel security labs for learning secure coding and web application security.</p>
            <p class="author">Built by Mohamed Adam</p>
            <p class="intro">
                Explore small, focused Laravel labs that show a vulnerable pattern, the secure fix, and how to test the behavior.
                The goal is practical learning through clear code examples.
            </p>
        </section>

        <section class="section" aria-labelledby="labs-title">
            <div class="section-header">
                <h2 id="labs-title">Labs</h2>
            </div>

            <div class="grid">
                <article class="card lab-card">
                    <h3>Lab 01 &mdash; IDOR Protection</h3>
                    <p>Learn how broken access control can expose another user's data.</p>
                    <a class="button" href="/labs/idor">Open Lab</a>
                </article>

                <article class="card lab-card">
                    <h3>Lab 02 &mdash; Mass Assignment</h3>
                    <p>Learn how unsafe model assignment can expose sensitive fields.</p>
                    <a class="button" href="/labs/mass-assignment">Open Lab</a>
                </article>

                <article class="card lab-card">
                    <h3>Lab 03 &mdash; File Upload Security</h3>
                    <p>Learn how unsafe uploads can expose files and how to store documents safely.</p>
                    <a class="button" href="/labs/file-upload-security">Open Lab</a>
                </article>
            </div>
        </section>
    </main>
@endsection
