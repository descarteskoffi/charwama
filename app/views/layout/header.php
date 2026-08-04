<?php
/**
 * Layout commun - En-tête (Header)
 */
$activePage = $activePage ?? '';
$title = $title ?? 'Franco fast-food';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($title); ?> — Franco fast-food | Commander sur WhatsApp</title>

    <!-- Favicon Fast-Food -->
    <link rel="icon" href="data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22><text y=%22.9em%22 font-size=%2290%22>🍔</text></svg>">

    <!-- Meta SEO -->
    <meta name="description" content="Découvrez le menu de Franco fast-food : chawarmas juteux, paninis croustillants et sandwichs gourmands. Commandez facilement via WhatsApp !">
    <meta name="robots" content="index, follow">
    <meta name="theme-color" content="#ff6b08">

    <!-- Open Graph / Réseaux sociaux -->
    <meta property="og:type" content="website">
    <meta property="og:title" content="<?php echo htmlspecialchars($title); ?> — Franco fast-food">
    <meta property="og:description" content="Franco fast-food - Chawarma, paninis et sandwichs artisanaux. Commandez maintenant via WhatsApp !">
    <meta property="og:locale" content="fr_FR">

    <!-- Préchargement des polices pour performance -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Feuilles de style -->
    <link class="css-theme" rel="stylesheet" href="<?php echo DYNAMIC_URLROOT; ?>/assets/css/style.css?v=1.5">
    <link class="css-anim" rel="stylesheet" href="<?php echo DYNAMIC_URLROOT; ?>/assets/css/animations.css?v=1.5">

    <!-- Script inline : appliquer le thème sauvegardé AVANT l'affichage (évite le flash) -->
    <script>
        (function() {
            var saved = localStorage.getItem('chawarma_theme');
            if (saved === 'light') {
                document.documentElement.setAttribute('data-theme', 'light');
            }
        })();
    </script>
</head>
<body>

    <!-- Lien d'accessibilité : passer directement au contenu -->
    <a href="#mainContent" class="skip-to-content">Aller au contenu</a>

    <!-- Barre de progression du scroll -->
    <div id="scrollProgressBar" aria-hidden="true"></div>

    <!-- Page Loader Premium -->
    <div id="pageLoader" aria-hidden="true">
        <div class="loader-logo">
            <i class="fa-solid fa-fire-burner"></i>
            Franco <span>fast-food</span>
        </div>
        <div class="loader-spinner"></div>
    </div>

    <!-- Conteneur de Toast Notifications -->
    <div id="toastContainer" aria-live="polite" aria-atomic="false"></div>

    <!-- Barre de Navigation -->
    <nav class="navbar" role="navigation" aria-label="Navigation principale">
        <div class="container navbar-container">
            <!-- Logo / Marque -->
            <a href="<?php echo DYNAMIC_URLROOT; ?>/" class="nav-brand" aria-label="Franco fast-food - Accueil">
                <i class="fa-solid fa-fire-burner" aria-hidden="true"></i>
                <span class="nav-brand-text">Franco <span>fast-food</span></span>
            </a>

            <!-- Menu de Navigation -->
            <ul class="nav-menu" id="navMenu" role="menubar">
                <li role="none">
                    <a href="<?php echo DYNAMIC_URLROOT; ?>/" 
                       class="nav-link <?php echo $activePage === 'home' ? 'active' : ''; ?>"
                       role="menuitem"
                       <?php echo $activePage === 'home' ? 'aria-current="page"' : ''; ?>>
                        Accueil
                    </a>
                </li>
                <li role="none">
                    <a href="<?php echo DYNAMIC_URLROOT; ?>/menu" 
                       class="nav-link <?php echo $activePage === 'menu' ? 'active' : ''; ?>"
                       role="menuitem"
                       <?php echo $activePage === 'menu' ? 'aria-current="page"' : ''; ?>>
                        Notre Menu
                    </a>
                </li>
                <li role="none">
                    <a href="<?php echo DYNAMIC_URLROOT; ?>/a-propos" 
                       class="nav-link <?php echo $activePage === 'about' ? 'active' : ''; ?>"
                       role="menuitem"
                       <?php echo $activePage === 'about' ? 'aria-current="page"' : ''; ?>>
                        À Propos
                    </a>
                </li>
                <li role="none">
                    <a href="<?php echo DYNAMIC_URLROOT; ?>/contact" 
                       class="nav-link <?php echo $activePage === 'contact' ? 'active' : ''; ?>"
                       role="menuitem"
                       <?php echo $activePage === 'contact' ? 'aria-current="page"' : ''; ?>>
                        Contact
                    </a>
                </li>
            </ul>

            <!-- Actions de Navigation (Panier + Toggle thème + Bouton mobile) -->
            <div class="nav-actions">
                <a href="<?php echo DYNAMIC_URLROOT; ?>/panier"
                   class="cart-icon-btn"
                   title="Mon panier"
                   aria-label="Voir mon panier">
                    <i class="fa-solid fa-bag-shopping" aria-hidden="true"></i>
                    <span class="cart-badge" id="cartBadge" aria-label="Nombre d'articles dans le panier">0</span>
                    <span class="cart-label">Panier</span>
                </a>

                <!-- Bouton Dark / Light Mode -->
                <button class="theme-toggle"
                        id="themeToggleBtn"
                        title="Changer le thème"
                        aria-label="Changer le thème">
                    <i class="fa-solid fa-moon" id="themeIcon" aria-hidden="true"></i>
                </button>

                <button class="mobile-nav-toggle"
                        id="mobileNavToggle"
                        aria-label="Ouvrir le menu de navigation"
                        aria-expanded="false"
                        aria-controls="navMenu">
                    <i class="fa-solid fa-bars" aria-hidden="true"></i>
                </button>
            </div>
        </div>
    </nav>

    <!-- Contenu principal -->
    <main id="mainContent">
