<?php
/**
 * Layout commun - Pied de page (Footer)
 */
$whatsappPhone = $siteSettings['whatsapp_phone'] ?? DEFAULT_PHONE;
$adresse = $siteSettings['contact_adresse'] ?? '123 Avenue de la Gastronomie, Paris';
$horaires = $siteSettings['site_horaires'] ?? 'Lun - Dim: 11h30 - 23h30';
$fbLink = $siteSettings['lien_facebook'] ?? '#';
$instaLink = $siteSettings['lien_instagram'] ?? '#';
$cleanPhone = preg_replace('/[^0-9]/', '', $whatsappPhone);
$waText = rawurlencode("Bonjour, je souhaite consulter votre carte et commander !");
?>
    </main><!-- /mainContent -->

    <!-- Pied de page -->
    <footer class="footer" role="contentinfo">
        <div class="container footer-grid">
            <!-- Colonne 1 : Description -->
            <div data-reveal="fade-up">
                <div class="footer-brand">
                    <i class="fa-solid fa-fire-burner" aria-hidden="true"></i> Franco<span>fast-food</span>
                </div>
                <p class="footer-desc">
                    L'excellence du fast-food artisanal préparé avec des produits frais et des ingrédients de qualité.
                </p>
                <div class="social-links">
                    <a href="<?php echo htmlspecialchars($fbLink); ?>" target="_blank" rel="noopener noreferrer"
                       class="social-link" title="Suivez-nous sur Facebook" aria-label="Facebook">
                        <i class="fa-brands fa-facebook-f" aria-hidden="true"></i>
                    </a>
                    <a href="<?php echo htmlspecialchars($instaLink); ?>" target="_blank" rel="noopener noreferrer"
                       class="social-link" title="Suivez-nous sur Instagram" aria-label="Instagram">
                        <i class="fa-brands fa-instagram" aria-hidden="true"></i>
                    </a>
                    <a href="https://wa.me/<?php echo $cleanPhone; ?>?text=<?php echo $waText; ?>"
                       target="_blank" rel="noopener noreferrer"
                       class="social-link" title="Discuter sur WhatsApp" aria-label="WhatsApp"
                       style="background-color: rgba(37, 211, 102, 0.2); color: #25d366;">
                        <i class="fa-brands fa-whatsapp" aria-hidden="true"></i>
                    </a>
                </div>
            </div>

            <!-- Colonne 2 : Horaires & Contact -->
            <div data-reveal="fade-up" data-reveal-delay="100">
                <h3 class="footer-title">Horaires &amp; Contact</h3>
                <ul class="footer-links">
                    <li>
                        <i class="fa-solid fa-clock" style="color: var(--primary); margin-right: 8px;" aria-hidden="true"></i>
                        <?php echo htmlspecialchars($horaires); ?>
                    </li>
                    <li>
                        <i class="fa-solid fa-location-dot" style="color: var(--primary); margin-right: 8px;" aria-hidden="true"></i>
                        <?php echo htmlspecialchars($adresse); ?>
                    </li>
                    <li>
                        <i class="fa-solid fa-phone" style="color: var(--primary); margin-right: 8px;" aria-hidden="true"></i>
                        <a href="tel:+<?php echo $cleanPhone; ?>" style="color: inherit;">
                            <?php echo htmlspecialchars($whatsappPhone); ?>
                        </a>
                    </li>
                </ul>
            </div>

            <!-- Colonne 3 : Navigation secondaire -->
            <div data-reveal="fade-up" data-reveal-delay="200">
                <h3 class="footer-title">Informations</h3>
                <ul class="footer-links">
                    <li><a href="<?php echo DYNAMIC_URLROOT; ?>/menu">Consulter le Menu</a></li>
                    <li><a href="<?php echo DYNAMIC_URLROOT; ?>/a-propos">À propos de nous</a></li>
                    <li><a href="<?php echo DYNAMIC_URLROOT; ?>/contact">Nous Contacter</a></li>
                    <li><a href="<?php echo DYNAMIC_URLROOT; ?>/mentions-legales">Mentions Légales</a></li>
                    <li>
                        <a href="<?php echo DYNAMIC_URLROOT; ?>/admin/login" style="color: #71717a;">
                            <i class="fa-solid fa-lock" aria-hidden="true"></i> Espace Admin
                        </a>
                    </li>
                </ul>
            </div>
        </div>

        <div class="container footer-bottom">
            <p>&copy; <?php echo date('Y'); ?> Franco fast-food. Tous droits réservés.</p>
        </div>
    </footer>

    <!-- Bouton WhatsApp Flottant -->
    <a href="https://wa.me/<?php echo $cleanPhone; ?>?text=<?php echo $waText; ?>"
       class="floating-whatsapp"
       target="_blank"
       rel="noopener noreferrer"
       title="Commander ou discuter sur WhatsApp"
       id="floatingWhatsappBtn"
       aria-label="Ouvrir WhatsApp pour commander">
        <i class="fa-brands fa-whatsapp" aria-hidden="true"></i>
    </a>

    <!-- Variables JavaScript dynamiques -->
    <script>
        const DYNAMIC_URLROOT = "<?php echo DYNAMIC_URLROOT; ?>";
        const RECEIVING_WHATSAPP_PHONE = "<?php echo $cleanPhone; ?>";
        const DEFAULT_PHONE = "<?php echo $cleanPhone; ?>";
    </script>

    <!-- JavaScript global -->
    <script src="<?php echo DYNAMIC_URLROOT; ?>/assets/js/main.js?v=1.5" defer></script>
</body>
</html>
