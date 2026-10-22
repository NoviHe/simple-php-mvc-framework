<?php
$diag = $data['diagnostics'] ?? [];
?>

<div style="margin-bottom: 2rem;">
    <h1 style="font-size: 2.25rem; font-weight: 800; color: #1e1b4b; margin-bottom: 0.5rem;">
        Welcome to <?= e(APP_NAME) ?>
    </h1>
    <p style="font-size: 1.1rem; color: #64748b;">
        A minimal, secure, and modern Model-View-Controller framework built with vanilla PHP.
    </p>
</div>

<!-- Diagnostics Grid -->
<div class="card">
    <h3 style="margin-bottom: 1rem; font-size: 1.15rem; font-weight: 600;">System Diagnostics</h3>
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1rem;">
        <div style="background: #f8fafc; padding: 1rem; border-radius: 8px; border: 1px solid #e2e8f0;">
            <div style="font-size: 0.8rem; color: #64748b; text-transform: uppercase; font-weight: 600;">PHP Version</div>
            <div style="font-size: 1.1rem; font-weight: 700; color: #0f172a; margin-top: 4px;"><?= e($diag['php_version'] ?? PHP_VERSION) ?></div>
        </div>

        <div style="background: #f8fafc; padding: 1rem; border-radius: 8px; border: 1px solid #e2e8f0;">
            <div style="font-size: 0.8rem; color: #64748b; text-transform: uppercase; font-weight: 600;">Environment</div>
            <div style="margin-top: 4px;">
                <span class="badge badge-info"><?= e($diag['app_env'] ?? 'development') ?></span>
                <span class="badge badge-warning" style="margin-left: 4px;">Debug: <?= e($diag['app_debug'] ?? 'Off') ?></span>
            </div>
        </div>

        <div style="background: #f8fafc; padding: 1rem; border-radius: 8px; border: 1px solid #e2e8f0;">
            <div style="font-size: 0.8rem; color: #64748b; text-transform: uppercase; font-weight: 600;">Database (PDO)</div>
            <div style="font-size: 0.95rem; font-weight: 600; margin-top: 4px; color: <?= str_contains($diag['database_status'] ?? '', 'Connected') ? '#059669' : '#d97706' ?>;">
                <?= e($diag['database_status'] ?? 'Not Connected') ?>
            </div>
        </div>

        <div style="background: #f8fafc; padding: 1rem; border-radius: 8px; border: 1px solid #e2e8f0;">
            <div style="font-size: 0.8rem; color: #64748b; text-transform: uppercase; font-weight: 600;">Timezone</div>
            <div style="font-size: 1.1rem; font-weight: 700; color: #0f172a; margin-top: 4px;"><?= e($diag['timezone'] ?? date_default_timezone_get()) ?></div>
        </div>
    </div>
</div>

<!-- Architecture & Features -->
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 1.5rem;">
    <div class="card">
        <h3 style="font-size: 1.1rem; font-weight: 600; margin-bottom: 0.75rem;">🛡️ Built-in Security</h3>
        <ul style="padding-left: 1.25rem; color: #475569; display: flex; flex-direction: column; gap: 0.5rem;">
            <li><strong>Prepared Statements (PDO)</strong>: Queries use parameterized statements preventing SQL Injection.</li>
            <li><strong>CSRF Protection</strong>: Helper <code>csrf_field()</code> and <code>csrf_verify()</code> built-in.</li>
            <li><strong>XSS Mitigation</strong>: Global helper <code>e($value)</code> with UTF-8 character encoding.</li>
            <li><strong>Secure Sessions</strong>: Cookie HTTP-only, SameSite, and Strict cookie constraints.</li>
        </ul>
    </div>

    <div class="card">
        <h3 style="font-size: 1.1rem; font-weight: 600; margin-bottom: 0.75rem;">⚡ Core Capabilities</h3>
        <ul style="padding-left: 1.25rem; color: #475569; display: flex; flex-direction: column; gap: 0.5rem;">
            <li><strong>Front Controller Pattern</strong>: Clean URL dispatch via <code>/controller/action/param</code>.</li>
            <li><strong>Lightweight Environment</strong>: Native <code>.env</code> file parser without external vendor dependencies.</li>
            <li><strong>JSON Responses</strong>: Instant REST API responses via <code>$this->json($data)</code>.</li>
            <li><strong>Active Record Model</strong>: Simple CRUD helpers (<code>all</code>, <code>find</code>, <code>where</code>, <code>create</code>, <code>update</code>, <code>delete</code>).</li>
        </ul>
    </div>
</div>

<!-- Quick Start Guide -->
<div class="card">
    <h3 style="font-size: 1.15rem; font-weight: 600; margin-bottom: 0.5rem;">Quick Start: How to Create a Route & Controller</h3>
    <p style="color: #64748b; font-size: 0.95rem;">Create a controller class in <code>application/controllers/</code> extending <code>Controller</code>:</p>

<pre><code>&lt;?php

class ProductController extends Controller
{
    public function index(): void
    {
        // 1. Fetch data from model (PDO Prepared Query)
        $productModel = $this-&gt;model('Product');
        $items = $productModel-&gt;all();

        // 2. Render view inside template layout
        $this-&gt;template('product/index', [
            'page_title' =&gt; 'All Products',
            'items' =&gt; $items
        ]);
    }
}
</code></pre>

    <div style="margin-top: 1.25rem; display: flex; gap: 10px;">
        <a href="<?= base_url('home/about') ?>" class="btn btn-primary">View About Page &rarr;</a>
        <a href="<?= base_url('home/api') ?>" target="_blank" class="btn btn-outline">Test JSON API &rarr;</a>
    </div>
</div>
