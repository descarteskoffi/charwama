<?php
/**
 * Vue - Administration - Paramètres Généraux
 */
require APPROOT . '/views/layout/admin_header.php';
?>

<!-- Alertes -->
<?php if (!empty($success)): ?>
    <div class="alert alert-success">
        <i class="fa-solid fa-circle-check"></i> <?php echo htmlspecialchars($success); ?>
    </div>
<?php endif; ?>

<?php if (!empty($error)): ?>
    <div class="alert alert-danger">
        <i class="fa-solid fa-triangle-exclamation"></i> <?php echo htmlspecialchars($error); ?>
    </div>
<?php endif; ?>

<div class="container" style="max-width: 900px; margin: 0 auto; margin-bottom: 60px;">
    <form action="<?php echo DYNAMIC_URLROOT; ?>/admin/parametres" method="POST">
        <!-- CSRF -->
        <input type="hidden" name="csrf_token" value="<?php echo $csrfToken; ?>">

        <!-- 1. BLOC COORDONNÉES ET FONCTIONNEMENT -->
        <div class="form-card" style="margin-bottom: 32px;">
            <h3><i class="fa-solid fa-address-book text-primary" style="margin-right: 8px;"></i> Coordonnées & Prise de Commande</h3>
            
            <div class="cart-layout" style="grid-template-columns: 1fr 1fr; gap: 24px; margin-bottom: 0;">
                <div class="form-group">
                    <label class="form-label" for="whatsapp_phone">Numéro WhatsApp Récepteur *</label>
                    <input type="text" id="whatsapp_phone" name="whatsapp_phone" class="form-control" 
                           value="<?php echo htmlspecialchars($siteSettings['whatsapp_phone'] ?? ''); ?>" 
                           placeholder="Ex: +33600000000" required>
                    <span style="font-size: 0.75rem; color: #71717a; margin-top: 4px; display: block;">
                        Le numéro avec indicatif pays (ex: +33, +225, etc.) sans espaces ni caractères spéciaux, auquel les commandes WhatsApp seront envoyées.
                    </span>
                </div>
                
                <div class="form-group">
                    <label class="form-label" for="contact_email">E-mail de Contact</label>
                    <input type="email" id="contact_email" name="contact_email" class="form-control" 
                           value="<?php echo htmlspecialchars($siteSettings['contact_email'] ?? ''); ?>" 
                           placeholder="Ex: contact@chawarmapremium.com">
                </div>
            </div>

            <div class="form-group">
                <label class="form-label" for="contact_adresse">Adresse Physique du Restaurant</label>
                <input type="text" id="contact_adresse" name="contact_adresse" class="form-control" 
                       value="<?php echo htmlspecialchars($siteSettings['contact_adresse'] ?? ''); ?>" 
                       placeholder="Ex: 123 Avenue de la Gastronomie, 75001 Paris">
            </div>

            <div class="form-group">
                <label class="form-label" for="site_horaires">Horaires d'Ouverture</label>
                <input type="text" id="site_horaires" name="site_horaires" class="form-control" 
                       value="<?php echo htmlspecialchars($siteSettings['site_horaires'] ?? ''); ?>" 
                       placeholder="Ex: Lundi au Dimanche de 11h30 à 23h30 non-stop">
            </div>

            <div class="cart-layout" style="grid-template-columns: 1fr 1fr; gap: 24px; margin-bottom: 0;">
                <div class="form-group">
                    <label class="form-label" for="lien_facebook">Lien Facebook</label>
                    <input type="url" id="lien_facebook" name="lien_facebook" class="form-control" 
                           value="<?php echo htmlspecialchars($siteSettings['lien_facebook'] ?? ''); ?>" 
                           placeholder="https://facebook.com/page...">
                </div>

                <div class="form-group">
                    <label class="form-label" for="lien_instagram">Lien Instagram</label>
                    <input type="url" id="lien_instagram" name="lien_instagram" class="form-control" 
                           value="<?php echo htmlspecialchars($siteSettings['lien_instagram'] ?? ''); ?>" 
                           placeholder="https://instagram.com/compte...">
                </div>
            </div>
        </div>

        <!-- 2. BLOC CONTENU DE LA PAGE D'ACCUEIL -->
        <div class="form-card" style="margin-bottom: 32px;">
            <h3><i class="fa-solid fa-file-pen text-primary" style="margin-right: 8px;"></i> Textes de la Page d'Accueil</h3>
            
            <div class="form-group">
                <label class="form-label" for="accueil_hero_titre">Titre Principal (Hero Banner)</label>
                <input type="text" id="accueil_hero_titre" name="accueil_hero_titre" class="form-control" 
                       value="<?php echo htmlspecialchars($siteSettings['accueil_hero_titre'] ?? ''); ?>">
            </div>

            <div class="form-group">
                <label class="form-label" for="accueil_hero_soustitre">Sous-titre Principal</label>
                <textarea id="accueil_hero_soustitre" name="accueil_hero_soustitre" class="form-control" style="min-height: 80px;"><?php echo htmlspecialchars($siteSettings['accueil_hero_soustitre'] ?? ''); ?></textarea>
            </div>

            <div class="form-group" style="border-top: 1px solid var(--border-color); padding-top: 24px; margin-top: 24px;">
                <label class="form-label" for="accueil_a_propos_titre">Titre Section "À Propos"</label>
                <input type="text" id="accueil_a_propos_titre" name="accueil_a_propos_titre" class="form-control" 
                       value="<?php echo htmlspecialchars($siteSettings['accueil_a_propos_titre'] ?? ''); ?>">
            </div>

            <div class="form-group">
                <label class="form-label" for="accueil_a_propos_description">Description / Récit de la Section "À Propos"</label>
                <textarea id="accueil_a_propos_description" name="accueil_a_propos_description" class="form-control" style="min-height: 120px;"><?php echo htmlspecialchars($siteSettings['accueil_a_propos_description'] ?? ''); ?></textarea>
            </div>
        </div>

        <button type="submit" class="btn btn-primary" style="width: 100%; padding: 16px; font-size: 1.1rem; box-shadow: var(--shadow-primary);">
            <i class="fa-solid fa-floppy-disk"></i> Sauvegarder la Configuration du Site
        </button>
    </form>
</div>

<?php require APPROOT . '/views/layout/admin_footer.php'; ?>
