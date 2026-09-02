<?php
/**
 * Layout administration - En-tête (Admin Header)
 */
$activePage = $activePage ?? '';
$title = $title ?? 'Administration - JP-Charwama';
$adminName = $_SESSION['admin_name'] ?? 'Administratrice';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($title); ?></title>
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Stylesheet -->
    <link rel="stylesheet" href="<?php echo DYNAMIC_URLROOT; ?>/assets/css/style.css">

    <!-- Application immédiate du thème sauvegardé (évite le flash de la page) -->
    <script>
        (function() {
            var saved = localStorage.getItem('chawarma_theme') || localStorage.getItem('theme');
            if (saved === 'light') {
                document.documentElement.setAttribute('data-theme', 'light');
            } else {
                document.documentElement.setAttribute('data-theme', 'dark');
            }
        })();
    </script>
</head>
<body>

    <div class="admin-layout">
        <!-- Sidebar Navigation -->
        <aside class="admin-sidebar">
            <div class="admin-sidebar-header">
                <a href="<?php echo DYNAMIC_URLROOT; ?>/admin/dashboard" class="nav-brand" style="font-size: 1.25rem;">
                    <i class="fa-solid fa-fire-burner"></i> JP-<span>Charwama</span>
                </a>
            </div>
            <ul class="admin-menu">
                <li>
                    <a href="<?php echo DYNAMIC_URLROOT; ?>/admin/dashboard" class="admin-menu-link <?php echo $activePage === 'dashboard' ? 'active' : ''; ?>">
                        <i class="fa-solid fa-chart-line"></i> Tableau de bord
                    </a>
                </li>
                <li>
                    <a href="<?php echo DYNAMIC_URLROOT; ?>/admin/commandes" class="admin-menu-link <?php echo $activePage === 'orders' ? 'active' : ''; ?>">
                        <i class="fa-solid fa-receipt"></i> Commandes
                    </a>
                </li>
                <li>
                    <a href="<?php echo DYNAMIC_URLROOT; ?>/admin/categories" class="admin-menu-link <?php echo $activePage === 'categories' ? 'active' : ''; ?>">
                        <i class="fa-solid fa-tags"></i> Catégories
                    </a>
                </li>
                <li>
                    <a href="<?php echo DYNAMIC_URLROOT; ?>/admin/produits" class="admin-menu-link <?php echo $activePage === 'products' ? 'active' : ''; ?>">
                        <i class="fa-solid fa-hamburger"></i> Produits
                    </a>
                </li>
                <li>
                    <a href="<?php echo DYNAMIC_URLROOT; ?>/admin/horaires" class="admin-menu-link <?php echo $activePage === 'schedule' ? 'active' : ''; ?>">
                        <i class="fa-solid fa-clock"></i> Horaires
                    </a>
                </li>
                <li>
                    <a href="<?php echo DYNAMIC_URLROOT; ?>/admin/tables" class="admin-menu-link <?php echo $activePage === 'tables' ? 'active' : ''; ?>">
                        <i class="fa-solid fa-qrcode"></i> Tables &amp; QR Codes
                    </a>
                </li>
                <li>
                    <a href="<?php echo DYNAMIC_URLROOT; ?>/admin/parametres" class="admin-menu-link <?php echo $activePage === 'settings' ? 'active' : ''; ?>">
                        <i class="fa-solid fa-gears"></i> Paramètres
                    </a>
                </li>
                <li>
                    <a href="<?php echo DYNAMIC_URLROOT; ?>/" class="admin-menu-link" target="_blank">
                        <i class="fa-solid fa-earth-africa"></i> Voir le site public
                    </a>
                </li>
                <li style="margin-top: auto;">
                    <a href="<?php echo DYNAMIC_URLROOT; ?>/admin/logout" class="admin-menu-link logout">
                        <i class="fa-solid fa-right-from-bracket"></i> Déconnexion
                    </a>
                </li>
            </ul>
        </aside>

        <!-- Main Body Wrapper -->
        <div class="admin-content">
            <!-- Navbar -->
            <header class="admin-navbar">
                <div class="admin-navbar-title">
                    <h2><?php echo htmlspecialchars($title); ?></h2>
                </div>
                <div class="admin-user-info" style="display: flex; align-items: center; gap: 16px;">
                    <!-- Bouton Bascule Thème Clair / Sombre -->
                    <button type="button" id="adminThemeToggle" title="Changer le thème (Sombre / Clair)" 
                            style="background: var(--bg-input); border: 1px solid var(--border-color); color: var(--text-primary); border-radius: var(--radius-full); padding: 8px 16px; display: inline-flex; align-items: center; gap: 8px; cursor: pointer; transition: var(--transition-fast); font-size: 0.85rem; font-family: var(--font-titles); font-weight: 600;">
                        <i class="fa-solid fa-moon" id="adminThemeIcon" style="font-size: 1rem; color: var(--primary);"></i>
                        <span id="adminThemeLabel">Mode Clair</span>
                    </button>

                    <div style="display: flex; align-items: center; gap: 10px;">
                        <span class="admin-user-name"><?php echo htmlspecialchars($adminName); ?></span>
                        <div class="admin-user-avatar">
                            <?php echo strtoupper(substr($adminName, 0, 1)); ?>
                        </div>
                    </div>
                </div>
            </header>

            <script>
            document.addEventListener('DOMContentLoaded', function() {
                const toggleBtn = document.getElementById('adminThemeToggle');
                const icon = document.getElementById('adminThemeIcon');
                const label = document.getElementById('adminThemeLabel');

                function applyTheme(theme) {
                    if (theme === 'light') {
                        document.documentElement.setAttribute('data-theme', 'light');
                        localStorage.setItem('chawarma_theme', 'light');
                        localStorage.setItem('theme', 'light');
                        if (icon) {
                            icon.className = 'fa-solid fa-sun';
                            icon.style.color = '#ff9f45';
                        }
                        if (label) label.textContent = 'Mode Sombre';
                    } else {
                        document.documentElement.setAttribute('data-theme', 'dark');
                        localStorage.setItem('chawarma_theme', 'dark');
                        localStorage.setItem('theme', 'dark');
                        if (icon) {
                            icon.className = 'fa-solid fa-moon';
                            icon.style.color = 'var(--primary)';
                        }
                        if (label) label.textContent = 'Mode Clair';
                    }
                }

                // Initialisation au chargement de la page
                const initialTheme = document.documentElement.getAttribute('data-theme') || localStorage.getItem('chawarma_theme') || 'dark';
                applyTheme(initialTheme);

                if (toggleBtn) {
                    toggleBtn.addEventListener('click', function() {
                        const current = document.documentElement.getAttribute('data-theme') === 'light' ? 'light' : 'dark';
                        const newTheme = current === 'light' ? 'dark' : 'light';
                        applyTheme(newTheme);
                    });
                }
            });
            </script>

            <!-- Page content -->
            <main class="admin-page-body">
