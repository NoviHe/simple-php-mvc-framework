<div style="text-align: center; padding: 4rem 1rem;">
    <div style="font-size: 5rem; font-weight: 800; color: #4f46e5; line-height: 1;">404</div>
    <h1 style="font-size: 1.85rem; font-weight: 700; color: #0f172a; margin-top: 1rem; margin-bottom: 0.75rem;">
        Page Not Found
    </h1>
    <p style="color: #64748b; font-size: 1.05rem; max-width: 480px; margin: 0 auto 2rem auto;">
        <?= htmlspecialchars($errorMessage ?? 'The page or resource you requested could not be found or has been moved.', ENT_QUOTES, 'UTF-8') ?>
    </p>
    <a href="<?= base_url() ?>" class="btn btn-primary" style="display: inline-block;">
        &larr; Return to Home
    </a>
</div>
