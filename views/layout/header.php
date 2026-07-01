<!DOCTYPE html>
<html lang="fr" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="FitConnect — Gestion du réseau de salles de sport : adhérents, abonnements et séances.">
    <title><?= isset($pageTitle) ? htmlspecialchars($pageTitle) . ' — FitConnect' : 'FitConnect' ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/style.css">
    <script>
        // Apply saved theme immediately to avoid flash
        (function() {
            const saved = localStorage.getItem('fc-theme') || 'light';
            document.documentElement.setAttribute('data-theme', saved);
        })();
    </script>
</head>
<body>

<!-- Navigation -->
<nav class="navbar" id="navbar">
    <div class="nav-container">
        <a href="<?= BASE_URL ?>" class="nav-brand" id="nav-brand">
            <span class="brand-icon">⚡</span>
            <span class="brand-name">FitConnect</span>
        </a>

        <ul class="nav-links" id="nav-links">
            <li>
                <a href="<?= BASE_URL ?>?page=dashboard"
                   id="nav-dashboard"
                   class="nav-link <?= ($currentPage ?? '') === 'dashboard' ? 'active' : '' ?>">
                    <span class="nav-icon">📊</span> Dashboard
                </a>
            </li>
            <li>
                <a href="<?= BASE_URL ?>?page=adherents"
                   id="nav-adherents"
                   class="nav-link <?= ($currentPage ?? '') === 'adherents' ? 'active' : '' ?>">
                    <span class="nav-icon">👥</span> Adhérents
                </a>
            </li>
            <li>
                <a href="<?= BASE_URL ?>?page=abonnements"
                   id="nav-abonnements"
                   class="nav-link <?= ($currentPage ?? '') === 'abonnements' ? 'active' : '' ?>">
                    <span class="nav-icon">🎫</span> Abonnements
                </a>
            </li>
            <li>
                <a href="<?= BASE_URL ?>?page=seances"
                   id="nav-seances"
                   class="nav-link <?= ($currentPage ?? '') === 'seances' ? 'active' : '' ?>">
                    <span class="nav-icon">🏋️</span> Séances
                </a>
            </li>
        </ul>

        <div class="nav-actions">
            <button class="theme-toggle" id="theme-toggle" title="Basculer le thème" aria-label="Toggle dark mode">
                🌙
            </button>
            <button class="nav-hamburger" id="nav-hamburger" aria-label="Menu" aria-expanded="false">
                <span></span>
                <span></span>
                <span></span>
            </button>
        </div>
    </div>
</nav>

<main class="main-content">
    <?php if (!empty($_SESSION['flash'])): ?>
        <div class="container">
            <div class="flash flash-<?= $_SESSION['flash']['type'] ?>">
                <span><?= htmlspecialchars($_SESSION['flash']['msg']) ?></span>
                <button class="flash-close" onclick="this.parentElement.remove()" aria-label="Fermer">✕</button>
            </div>
        </div>
        <?php unset($_SESSION['flash']); ?>
    <?php endif; ?>

<script>
(function() {
    // Theme toggle
    const toggle = document.getElementById('theme-toggle');
    const html   = document.documentElement;

    function applyTheme(t) {
        html.setAttribute('data-theme', t);
        toggle.textContent = t === 'dark' ? '☀️' : '🌙';
        localStorage.setItem('fc-theme', t);
    }

    // Init icon
    applyTheme(html.getAttribute('data-theme') || 'light');

    toggle.addEventListener('click', function() {
        const current = html.getAttribute('data-theme');
        applyTheme(current === 'dark' ? 'light' : 'dark');
    });

    // Mobile hamburger
    const hamburger = document.getElementById('nav-hamburger');
    const navLinks  = document.getElementById('nav-links');

    hamburger.addEventListener('click', function() {
        const isOpen = navLinks.classList.toggle('open');
        hamburger.setAttribute('aria-expanded', isOpen);
        const spans = hamburger.querySelectorAll('span');
        if (isOpen) {
            spans[0].style.cssText = 'transform: rotate(45deg) translate(5px, 5px)';
            spans[1].style.cssText = 'opacity: 0';
            spans[2].style.cssText = 'transform: rotate(-45deg) translate(5px, -5px)';
        } else {
            spans.forEach(s => s.style.cssText = '');
        }
    });

    // Close mobile menu on link click
    navLinks.querySelectorAll('.nav-link').forEach(function(link) {
        link.addEventListener('click', function() {
            navLinks.classList.remove('open');
            hamburger.setAttribute('aria-expanded', 'false');
            hamburger.querySelectorAll('span').forEach(s => s.style.cssText = '');
        });
    });

    // Auto-dismiss flash messages after 5s
    setTimeout(function() {
        document.querySelectorAll('.flash').forEach(function(el) {
            el.style.transition = 'opacity 0.4s ease';
            el.style.opacity = '0';
            setTimeout(() => el.remove(), 400);
        });
    }, 5000);
})();
</script>
