<?php
/**
 * Vue - Mentions Légales
 */
require APPROOT . '/views/layout/header.php';

$adresse = $siteSettings['contact_adresse'] ?? '123 Avenue de la Gastronomie, Paris';
$email = $siteSettings['contact_email'] ?? 'contact@chawarmapremium.com';
?>

<section class="legal-page">
    <div class="container legal-content">
        <h1>Mentions Légales & Politique de Confidentialité</h1>
        
        <section>
            <h2>1. Édition du site</h2>
            <p>
                Le présent site web, accessible à l'adresse <code><?php echo htmlspecialchars(DYNAMIC_URLROOT); ?></code>, est édité par :
            </p>
            <p>
                <strong>Chawarma Premium</strong><br>
                Adresse : <?php echo htmlspecialchars($adresse); ?><br>
                E-mail de contact : <?php echo htmlspecialchars($email); ?><br>
                Directeur de la publication : Le propriétaire du restaurant.
            </p>
        </section>

        <section>
            <h2>2. Hébergement du site</h2>
            <p>
                Le site est hébergé par la société :
            </p>
            <p>
                <strong>Alwaysdata</strong><br>
                SARL au capital de 200 000 €<br>
                Siège social : 91 rue du Faubourg Saint-Honoré, 75008 Paris - France<br>
                Site web : <a href="https://www.alwaysdata.com" target="_blank" style="color: var(--primary); text-decoration: underline;">www.alwaysdata.com</a>
            </p>
        </section>

        <section>
            <h2>3. Propriété intellectuelle</h2>
            <p>
                L'ensemble du contenu de ce site (textes, images, graphismes, logos, icônes) est la propriété exclusive de Chawarma Premium, sauf mentions contraires. Toute reproduction, distribution ou modification de ces éléments est strictement interdite sans accord écrit préalable.
            </p>
        </section>

        <section>
            <h2>4. Données personnelles & Commandes</h2>
            <p>
                Ce site n'utilise pas de base de données persistante pour stocker les informations personnelles des clients. Vos sélections de produits sont mémorisées dans le cache local de votre propre navigateur (<code>localStorage</code>) et effacées à votre convenance.
            </p>
            <p>
                Lorsque vous cliquez sur "Commander via WhatsApp", les informations saisies (nom, mode de retrait, notes) sont encapsulées directement dans une URL WhatsApp afin de vous rediriger vers l'application de messagerie WhatsApp. Aucune donnée nominative n'est conservée sur nos serveurs.
            </p>
        </section>

        <section>
            <h2>5. Cookies</h2>
            <p>
                Le site n'utilise pas de cookies de ciblage publicitaire. Des cookies techniques peuvent être générés par le serveur d'hébergement ou la session d'administration (obligatoire pour maintenir la sécurité de la session admin), sans récolte de données personnelles.
            </p>
        </section>
    </div>
</section>

<?php require APPROOT . '/views/layout/footer.php'; ?>
