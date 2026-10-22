<div style="margin-bottom: 1.5rem;">
    <h1 style="font-size: 2rem; font-weight: 700; color: #1e1b4b; margin-bottom: 0.5rem;">
        About <?= e(APP_NAME) ?>
    </h1>
    <p style="color: #64748b; font-size: 1.05rem;">
        <?= e($data['description'] ?? 'A minimalist, educational, and clean MVC framework.') ?>
    </p>
</div>

<div class="card">
    <h3 style="margin-bottom: 0.75rem; font-size: 1.15rem; font-weight: 600;">Framework Directory Structure</h3>
    <pre><code>simple-php-mvc-framework/
├── application/
│   ├── controllers/         # Application Controllers (e.g., HomeController.php)
│   ├── functions/           # Helper functions (helpers.php)
│   ├── models/              # Active Record PDO Models (e.g., user.model.php)
│   └── views/               # PHP Templates and Views
├── config/
│   └── config.php           # App configuration, constants & .env loader
├── library/
│   ├── controller.class.php # Base Controller class
│   ├── model.class.php      # Base Model (PDO with Prepared Statements)
│   ├── security.class.php   # CSRF, XSS, and Hashing Security utilities
│   └── view.class.php       # View rendering and variable binding
├── public/
│   ├── .htaccess            # Clean URL rewriting
│   ├── index.php            # Front Controller & entry point
│   ├── css/                 # Stylesheets
│   └── js/                  # JavaScript files
├── tmp/
│   └── log/                 # Error logs (error.log)
├── .env                     # Local environment configuration
└── .env.example             # Template environment configuration</code></pre>
</div>

<div class="card">
    <h3 style="margin-bottom: 0.75rem; font-size: 1.15rem; font-weight: 600;">Why use this framework?</h3>
    <p style="color: #475569; margin-bottom: 1rem;">
        This framework was designed to provide the cleanest separation of concerns without the overwhelming complexity of heavy enterprise frameworks. It gives developers full control over the code while enforcing modern security practices (SQL Injection prevention, CSRF validation, and XSS escaping).
    </p>
    <a href="<?= base_url() ?>" class="btn btn-outline">&larr; Back to Home</a>
</div>
