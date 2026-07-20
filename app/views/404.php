<?php
/**
 * Vue - Erreur 404 (Page non trouvée)
 */
require APPROOT . '/views/layout/header.php';
?>

<section class="container" style="padding: 120px 24px; text-align: center;">
    <div style="font-size: 6rem; color: var(--primary); font-weight: 800; line-height: 1; margin-bottom: 24px; font-family: var(--font-titles);">
        404
    </div>
    <h1 style="font-size: 2.2rem; margin-bottom: 16px;">Oups ! Page Introuvable</h1>
    <p style="color: #a0a0a5; max-width: 500px; margin: 0 auto 36px; font-size: 1.05rem;">
        La page que vous recherchez a peut-être été déplacée, supprimée ou n'a jamais existé. Laissez-nous vous guider vers quelque chose de délicieux.
    </p>
    <div style="display: flex; justify-content: center; gap: 16px;">
        <a href="<?php echo DYNAMIC_URLROOT; ?>/" class="btn btn-primary">
            <i class="fa-solid fa-house"></i> Retour à l'accueil
        </a>
        <a href="<?php echo DYNAMIC_URLROOT; ?>/menu" class="btn btn-secondary">
            <i class="fa-solid fa-utensils"></i> Découvrir notre Carte
        </a>
    </div>
</section>

<?php require APPROOT . '/views/layout/footer.php'; ?>
