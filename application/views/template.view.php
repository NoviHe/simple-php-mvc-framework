<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="<?= csrf_token() ?>">
    <title><?= e($page_title ?? APP_NAME) ?></title>
    <link rel="shortcut icon" href="<?= asset('img/favicon.png') ?>">

    <!-- Modern Typography & UI Styling -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">

    <style>
        :root {
            --primary: #4f46e5;
            --primary-hover: #4338ca;
            --secondary: #64748b;
            --bg-body: #f8fafc;
            --bg-card: #ffffff;
            --border-color: #e2e8f0;
            --text-main: #0f172a;
            --text-muted: #64748b;
            --success: #10b981;
            --warning: #f59e0b;
            --danger: #ef4444;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background-color: var(--bg-body);
            color: var(--text-main);
            line-height: 1.6;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        a {
            color: var(--primary);
            text-decoration: none;
            transition: color 0.15s ease-in-out;
        }

        a:hover {
            color: var(--primary-hover);
        }

        /* Navbar */
        .navbar {
            background-color: var(--bg-card);
            border-bottom: 1px solid var(--border-color);
            padding: 0.85rem 1.5rem;
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .navbar-container {
            max-width: 1100px;
            margin: 0 auto;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .brand-logo {
            font-size: 1.15rem;
            font-weight: 700;
            color: var(--text-main);
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .brand-badge {
            background-color: #e0e7ff;
            color: var(--primary);
            font-size: 0.72rem;
            padding: 2px 7px;
            border-radius: 4px;
            font-weight: 600;
            text-transform: uppercase;
        }

        .nav-links {
            display: flex;
            list-style: none;
            gap: 1.25rem;
            align-items: center;
        }

        .nav-links a {
            color: var(--secondary);
            font-size: 0.92rem;
            font-weight: 500;
        }

        .nav-links a:hover,
        .nav-links .active a {
            color: var(--primary);
        }

        /* Main Content Container */
        .main-container {
            max-width: 1100px;
            margin: 2rem auto;
            padding: 0 1.5rem;
            flex: 1;
            width: 100%;
        }

        /* Card Component */
        .card {
            background-color: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: 10px;
            padding: 1.75rem;
            margin-bottom: 1.5rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
        }

        /* Buttons */
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 0.55rem 1.15rem;
            border-radius: 6px;
            font-weight: 500;
            font-size: 0.92rem;
            cursor: pointer;
            border: none;
            transition: all 0.15s ease-in-out;
        }

        .btn-primary {
            background-color: var(--primary);
            color: #ffffff !important;
        }

        .btn-primary:hover {
            background-color: var(--primary-hover);
        }

        .btn-outline {
            border: 1px solid var(--border-color);
            background-color: #ffffff;
            color: var(--text-main) !important;
        }

        .btn-outline:hover {
            background-color: #f1f5f9;
        }

        /* Badges */
        .badge {
            display: inline-block;
            padding: 3px 8px;
            font-size: 0.75rem;
            font-weight: 600;
            border-radius: 4px;
        }

        .badge-success {
            background-color: #d1fae5;
            color: #065f46;
        }

        .badge-warning {
            background-color: #fef3c7;
            color: #92400e;
        }

        .badge-info {
            background-color: #e0e7ff;
            color: #3730a3;
        }

        /* Code block */
        code, pre {
            font-family: 'JetBrains Mono', Consolas, Monaco, monospace;
        }

        pre {
            background-color: #0f172a;
            color: #e2e8f0;
            padding: 1rem 1.25rem;
            border-radius: 8px;
            overflow-x: auto;
            font-size: 0.88rem;
            line-height: 1.5;
            margin: 1rem 0;
        }

        /* Footer */
        .footer {
            border-top: 1px solid var(--border-color);
            background-color: var(--bg-card);
            padding: 1.5rem;
            text-align: center;
            font-size: 0.85rem;
            color: var(--text-muted);
        }

        @media (max-width: 768px) {
            .navbar-container {
                flex-direction: column;
                gap: 10px;
            }
            .nav-links {
                gap: 15px;
            }
        }
    </style>
</head>
<body>

    <!-- Header Navigation -->
    <header class="navbar">
        <div class="navbar-container">
            <a href="<?= base_url() ?>" class="brand-logo">
                <?= e(APP_NAME) ?>
                <span class="brand-badge">MVC Core</span>
            </a>
            <ul class="nav-links">
                <?= nav_item('', 'Home') ?>
                <?= nav_item('home/about', 'About') ?>
                <?= nav_item('home/api', 'JSON API') ?>
            </ul>
        </div>
    </header>

    <!-- Main Content Area -->
    <main class="main-container">
        <?php
        if (isset($viewName)) {
            $contentView = APP_PATH . DS . 'views' . DS . str_replace(['/', '\\'], DS, $viewName) . '.view.php';
            if (file_exists($contentView)) {
                require $contentView;
            } else {
                echo "<div class='card'><h3>View file not found: " . e($viewName) . "</h3></div>";
            }
        }
        ?>
    </main>

    <!-- Global Footer -->
    <footer class="footer">
        <p>&copy; <?= date('Y') ?> <strong><?= e(APP_NAME) ?></strong>. Built with modern, secure PHP MVC.</p>
    </footer>

</body>
</html>
