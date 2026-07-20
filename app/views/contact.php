<?php
/**
 * Vue - Page Contact
 */
require APPROOT . '/views/layout/header.php';

$adresse = $siteSettings['contact_adresse'] ?? '123 Avenue de la Gastronomie, Paris';
$horaires = $siteSettings['site_horaires'] ?? 'Lun - Dim: 11h30 - 23h30';
$whatsappPhone = $siteSettings['whatsapp_phone'] ?? DEFAULT_PHONE;
$email = $siteSettings['contact_email'] ?? 'contact@chawarmapremium.com';
?>

<!-- En-tête de page -->
<section class="hero" style="padding: 60px 0 40px; text-align: center; background: radial-gradient(circle at 50% 50%, rgba(255, 107, 8, 0.08) 0%, transparent 60%);">
    <div class="container">
        <span class="tag" style="color: var(--primary); font-family: var(--font-titles); font-weight: 600; text-transform: uppercase; font-size: 0.85rem; letter-spacing: 2px;">Nous joindre</span>
        <h1 style="font-size: 3rem; margin-top: 12px;">Contactez-nous</h1>
    </div>
</section>

<!-- Section Contact -->
<section class="contact-page" style="padding-top: 20px;">
    <div class="container contact-grid">
        <!-- Informations pratiques -->
        <div>
            <h2 style="font-size: 2rem; margin-bottom: 24px; color: var(--primary);">Nos Coordonnées</h2>
            <p style="color: #a0a0a5; margin-bottom: 32px;">
                Une question sur nos produits, un événement particulier ou une commande volumineuse ? N'hésitez pas à nous contacter par téléphone, via notre formulaire ou en nous rendant visite directement.
            </p>
            
            <div class="contact-info-list">
                <!-- Adresse -->
                <div class="contact-info-item">
                    <div class="contact-info-icon"><i class="fa-solid fa-location-dot"></i></div>
                    <div class="contact-info-text">
                        <h4>Notre Établissement</h4>
                        <p><?php echo htmlspecialchars($adresse); ?></p>
                    </div>
                </div>
                
                <!-- Horaires -->
                <div class="contact-info-item">
                    <div class="contact-info-icon"><i class="fa-solid fa-clock"></i></div>
                    <div class="contact-info-text">
                        <h4>Horaires d'Ouverture</h4>
                        <p><?php echo htmlspecialchars($horaires); ?></p>
                    </div>
                </div>
                
                <!-- WhatsApp -->
                <div class="contact-info-item">
                    <div class="contact-info-icon"><i class="fa-brands fa-whatsapp"></i></div>
                    <div class="contact-info-text">
                        <h4>Commandes WhatsApp / Téléphone</h4>
                        <p><?php echo htmlspecialchars($whatsappPhone); ?></p>
                    </div>
                </div>

                <!-- Email -->
                <div class="contact-info-item">
                    <div class="contact-info-icon"><i class="fa-solid fa-envelope"></i></div>
                    <div class="contact-info-text">
                        <h4>Adresse E-mail</h4>
                        <p><?php echo htmlspecialchars($email); ?></p>
                    </div>
                </div>
            </div>
            
            <!-- Carte interactive -->
            <div class="map-container" style="margin-top: 32px; height: 350px;">
                <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d15860.840228308826!2d2.298038837130543!3d6.366887532398516!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x1023f03b57351ad7%3A0xe54d3cd616d610cb!2sCocotomey!5e0!3m2!1sfr!2sbj!4v1721473000000!5m2!1sfr!2sbj" 
                        width="100%" height="100%" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
            </div>
        </div>
        
        <!-- Formulaire de contact -->
        <div class="form-card">
            <h3>Envoyez-nous un message</h3>
            <form action="#" method="POST" onsubmit="alert('Votre message a bien été envoyé ! Nous vous répondrons dans les plus brefs délais.'); return false;">
                <div class="form-group">
                    <label class="form-label" for="nom">Nom complet</label>
                    <input type="text" id="nom" name="nom" class="form-control" placeholder="Ex: Jean Dupont" required>
                </div>
                
                <div class="form-group">
                    <label class="form-label" for="email">Adresse e-mail</label>
                    <input type="email" id="email" name="email" class="form-control" placeholder="Ex: jean.dupont@email.com" required>
                </div>

                <div class="form-group">
                    <label class="form-label" for="sujet">Sujet</label>
                    <input type="text" id="sujet" name="sujet" class="form-control" placeholder="Ex: Renseignement événementiel" required>
                </div>
                
                <div class="form-group">
                    <label class="form-label" for="message">Message</label>
                    <textarea id="message" name="message" class="form-control" placeholder="Saisissez votre message ici..." required></textarea>
                </div>
                
                <button type="submit" class="btn btn-primary" style="width: 100%;">
                    <i class="fa-solid fa-paper-plane"></i> Envoyer le message
                </button>
            </form>
        </div>
    </div>
</section>

<?php require APPROOT . '/views/layout/footer.php'; ?>
