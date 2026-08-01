<?php
/**
 * Vue - Page d'accueil (Home)
 */
require APPROOT . '/views/layout/header.php';

$heroTitreRaw = $siteSettings['accueil_hero_titre'] ?? '';
if (empty($heroTitreRaw) || strripos($heroTitreRaw, 'Shawarma') !== false || strripos($heroTitreRaw, 'Chawarma Premium') !== false) {
    $heroTitre = 'Franco Fast-Food : L\'Expérience Gourmande Ultime !';
} else {
    $heroTitre = $heroTitreRaw;
}

$heroSousTitreRaw = $siteSettings['accueil_hero_soustitre'] ?? '';
if (empty($heroSousTitreRaw) || strripos($heroSousTitreRaw, 'marinades traditionnelles') !== false) {
    $heroSousTitre = 'Succombez à nos recettes artisanales préparées à la minute : chawarmas juteux, paninis fondants et sandwichs croustillants. Commandez en 1 clic !';
} else {
    $heroSousTitre = $heroSousTitreRaw;
}

$aProposTitre  = $siteSettings['accueil_a_propos_titre']      ?? 'La passion du goût chez Franco Fast-Food';
$aProposDesc   = $siteSettings['accueil_a_propos_description'] ?? 'Chez Franco fast-food, nous réinventons la restauration rapide avec des ingrédients frais, des sauces faites maison et un savoir-faire authentique.';
$adresse       = $siteSettings['contact_adresse']              ?? 'Cocotomey, Bénin';
$horaires      = $siteSettings['site_horaires']                ?? 'Lun - Dim: 11h30 - 23h30';
$whatsappPhone = $siteSettings['whatsapp_phone']               ?? DEFAULT_PHONE;
?>

<!-- 1. SECTION HERO BANNER -->
<section class="hero" aria-label="Bannière principale">
    <!-- Décorations géométriques d'arrière-plan -->
    <div class="hero-decorations" aria-hidden="true">
        <div class="hero-deco-circle"></div>
        <div class="hero-deco-circle"></div>
    </div>

    <div class="container hero-grid">
        <!-- Contenu textuel -->
        <div class="hero-content" data-reveal="fade-right">
            <h1><span class="hero-highlight">Franco Fast-Food</span> : L'Expérience Gourmande Ultime !</h1>
            <p><?php echo htmlspecialchars($heroSousTitre); ?></p>
            <div class="hero-buttons">
                <a href="<?php echo DYNAMIC_URLROOT; ?>/menu" class="btn btn-primary" id="heroCTAPrimary">
                    <i class="fa-solid fa-utensils" aria-hidden="true"></i> Découvrir le Menu
                </a>
                <a href="<?php echo DYNAMIC_URLROOT; ?>/a-propos" class="btn btn-secondary" id="heroCTASecondary">
                    Notre Histoire
                </a>
            </div>
        </div>

        <!-- Illustration / Image d'accroche -->
        <div class="hero-image-wrapper" data-reveal="fade-left" data-reveal-delay="100">
            <div class="hero-blob" aria-hidden="true"></div>
            <img src="<?php echo DYNAMIC_URLROOT; ?>/assets/images/hero_chawarma.png"
                 alt="Spécialités Franco fast-food — Fast-food de qualité"
                 class="hero-image"
                 width="500"
                 height="500"
                 onerror="this.src='https://images.unsplash.com/photo-1561651823-34fed022540d?w=500&auto=format&fit=crop&q=60';">
        </div>
    </div>
</section>

<!-- 2. SECTION CARACTÉRISTIQUES (FEATURES) -->
<section class="features-sec" aria-labelledby="featuresSectionTitle">
    <div class="container">
        <div class="section-header" data-reveal="fade-up">
            <span class="tag">Pourquoi nous choisir</span>
            <h2 id="featuresSectionTitle">Nos engagements qualité</h2>
            <p>La qualité n'est pas une option. Nous mettons tout en œuvre pour vous offrir des plats savoureux et sains.</p>
        </div>

        <div class="features-grid">
            <!-- Feature 1 -->
            <div class="feature-card" data-reveal="fade-up" data-reveal-delay="100">
                <div class="feature-icon" aria-hidden="true"><i class="fa-solid fa-lemon"></i></div>
                <h3>Produits 100% Frais</h3>
                <p>Nos légumes sont livrés chaque matin et nos viandes sont sélectionnées avec le plus grand soin auprès de producteurs certifiés.</p>
            </div>
            <!-- Feature 2 -->
            <div class="feature-card" data-reveal="fade-up" data-reveal-delay="200">
                <div class="feature-icon" aria-hidden="true"><i class="fa-solid fa-mortar-pestle"></i></div>
                <h3>Recettes Secrètes</h3>
                <p>Nos marinades de viande reposent pendant 24 heures avec des épices importées pour un goût tendre et inimitable.</p>
            </div>
            <!-- Feature 3 -->
            <div class="feature-card" data-reveal="fade-up" data-reveal-delay="300">
                <div class="feature-icon" aria-hidden="true"><i class="fa-solid fa-bolt"></i></div>
                <h3>Rapide &amp; Chaud</h3>
                <p>Commandez en 2 clics sur WhatsApp et récupérez votre commande croustillante, cuite à la perfection sur place ou à emporter.</p>
            </div>
        </div>
    </div>
</section>

<!-- 3. SECTION APERÇU CATÉGORIES PHARES -->
<section class="about-teaser-sec" style="background-color: rgba(255,255,255,0.01);" aria-labelledby="categoriesSectionTitle">
    <div class="container">
        <div class="section-header" data-reveal="fade-up">
            <span class="tag">Nos Catégories</span>
            <h2 id="categoriesSectionTitle">Envie de quoi aujourd'hui ?</h2>
            <p>Découvrez nos spécialités préparées à la commande pour tous les goûts.</p>
        </div>

        <div class="categories-filter" style="margin-bottom: 0;" data-reveal="fade-up" data-reveal-delay="100">
            <?php foreach ($categories as $cat): ?>
                <a href="<?php echo DYNAMIC_URLROOT; ?>/menu?cat=<?php echo (int)$cat['id']; ?>"
                   class="filter-tab"
                   id="catTab_<?php echo (int)$cat['id']; ?>">
                    <i class="fa-solid fa-arrow-right" style="font-size: 0.8rem; margin-right: 4px; color: var(--primary);" aria-hidden="true"></i>
                    <?php echo htmlspecialchars($cat['nom']); ?>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- 4. SECTION PRODUITS VEDETTES (MEILLEURES VENTES) -->
<section class="features-sec" aria-labelledby="featuredSectionTitle">
    <div class="container">
        <div class="section-header" data-reveal="fade-up">
            <span class="tag">À la une</span>
            <h2 id="featuredSectionTitle">Nos Meilleures Ventes</h2>
            <p>Les incontournables plébiscités par nos clients réguliers. Laissez-vous tenter !</p>
        </div>

        <div class="products-grid">
            <?php if (empty($featuredProducts)): ?>
                <div style="grid-column: 1/-1; text-align: center; color: #a0a0a5; padding: 40px 0;" data-reveal="fade-up">
                    <i class="fa-solid fa-circle-exclamation" style="font-size: 2rem; margin-bottom: 12px; color: var(--primary);" aria-hidden="true"></i>
                    <p>Aucun produit n'est disponible pour le moment.</p>
                </div>
            <?php else: ?>
                <?php $delay = 0; foreach ($featuredProducts as $product): $delay += 100; ?>
                    <article class="product-card"
                             data-reveal="fade-up"
                             data-reveal-delay="<?php echo min($delay, 500); ?>">
                        <div class="product-card-img-wrapper">
                            <div class="product-badge-overlay">
                                <span class="badge badge-popular">Populaire</span>
                            </div>
                            <?php if ($product['image']): ?>
                                <img data-src="<?php echo DYNAMIC_URLROOT; ?>/uploads/<?php echo htmlspecialchars($product['image']); ?>"
                                     src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 1 1'%3E%3C/svg%3E"
                                     alt="<?php echo htmlspecialchars($product['nom']); ?>"
                                     width="280" height="200"
                                     onerror="this.src='https://images.unsplash.com/photo-1561651823-34fed022540d?w=500&auto=format&fit=crop&q=60';">
                            <?php else: ?>
                                <img src="https://images.unsplash.com/photo-1561651823-34fed022540d?w=500&auto=format&fit=crop&q=60"
                                     alt="Image par défaut" width="280" height="200">
                            <?php endif; ?>
                        </div>

                        <div class="product-card-content">
                            <h3 class="product-card-title"><?php echo htmlspecialchars($product['nom']); ?></h3>
                            <p class="product-card-desc"><?php echo htmlspecialchars($product['description']); ?></p>

                            <div class="product-card-footer">
                                <span class="product-card-price">
                                    <?php echo number_format($product['prix'], 0, ',', ' '); ?> <span>FCFA</span>
                                </span>
                                <a href="<?php echo DYNAMIC_URLROOT; ?>/menu?product=<?php echo (int)$product['id']; ?>"
                                   class="btn btn-outline btn-sm"
                                   id="homeProductBtn_<?php echo (int)$product['id']; ?>">
                                    <i class="fa-solid fa-plus" aria-hidden="true"></i> Commander
                                </a>
                            </div>
                        </div>
                    </article>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <div style="text-align: center; margin-top: 48px;" data-reveal="fade-up">
            <a href="<?php echo DYNAMIC_URLROOT; ?>/menu" class="btn btn-secondary" id="viewFullMenuBtn">
                <i class="fa-solid fa-list-ul" aria-hidden="true"></i> Voir tout le catalogue
            </a>
        </div>
    </div>
</section>

<!-- 5. SECTION PRÉSENTATION HISTOIRE (ABOUT TEASER) -->
<section class="about-teaser-sec" style="background-color: rgba(255, 255, 255, 0.01); border-top: 1px solid var(--border-color); border-bottom: 1px solid var(--border-color);" aria-labelledby="aboutSectionTitle">
    <div class="container about-teaser-grid">
        <div class="about-teaser-img" data-reveal="fade-right">
            <img src="https://images.unsplash.com/photo-1555396273-367ea4eb4db5?w=600&auto=format&fit=crop&q=60"
                 alt="Cuisine artisanale au restaurant Chawarma Premium"
                 loading="lazy"
                 onerror="this.src='https://images.unsplash.com/photo-1504674900247-0877df9cc836?w=600&auto=format&fit=crop&q=60';">
        </div>
        <div class="about-teaser-content" data-reveal="fade-left" data-reveal-delay="100">
            <span class="tag" style="color: var(--primary); font-family: var(--font-titles); font-weight: 600; text-transform: uppercase; font-size: 0.85rem; display: block; margin-bottom: 8px;">Notre Histoire</span>
            <h2 id="aboutSectionTitle"><?php echo htmlspecialchars($aProposTitre); ?></h2>
            <p><?php echo nl2br(htmlspecialchars($aProposDesc)); ?></p>
            <a href="<?php echo DYNAMIC_URLROOT; ?>/a-propos" class="btn btn-outline" id="homeAboutBtn">En savoir plus</a>
        </div>
    </div>
</section>

<!-- 6. SECTION INFOS PRATIQUES (CONTACT & HORAIRES) -->
<section class="promo-sec" aria-labelledby="infosSectionTitle">
    <div class="container contact-grid">
        <div data-reveal="fade-right">
            <span class="tag" style="color: var(--primary); font-family: var(--font-titles); font-weight: 600; text-transform: uppercase; font-size: 0.85rem; display: block; margin-bottom: 8px;">Où nous trouver ?</span>
            <h2 id="infosSectionTitle" style="font-size: 2.2rem; margin-bottom: 24px;">Informations Pratiques</h2>

            <div class="contact-info-list">
                <div class="contact-info-item">
                    <div class="contact-info-icon" aria-hidden="true"><i class="fa-solid fa-location-dot"></i></div>
                    <div class="contact-info-text">
                        <h4>Notre Adresse</h4>
                        <p><?php echo htmlspecialchars($adresse); ?></p>
                    </div>
                </div>
                <div class="contact-info-item">
                    <div class="contact-info-icon" aria-hidden="true"><i class="fa-solid fa-clock"></i></div>
                    <div class="contact-info-text">
                        <h4>Horaires d'Ouverture</h4>
                        <p><?php echo htmlspecialchars($horaires); ?></p>
                    </div>
                </div>
                <div class="contact-info-item">
                    <div class="contact-info-icon" aria-hidden="true"><i class="fa-solid fa-phone"></i></div>
                    <div class="contact-info-text">
                        <h4>Téléphone / WhatsApp</h4>
                        <p>
                            <a href="tel:+<?php echo preg_replace('/[^0-9]/', '', $whatsappPhone); ?>" style="color: inherit;">
                                <?php echo htmlspecialchars($whatsappPhone); ?>
                            </a>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <div class="map-container" data-reveal="fade-left" data-reveal-delay="100" style="height: 350px;">
            <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d15860.840228308826!2d2.298038837130543!3d6.366887532398516!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x1023f03b57351ad7%3A0xe54d3cd616d610cb!2sCocotomey!5e0!3m2!1sfr!2sbj!4v1721473000000!5m2!1sfr!2sbj" 
                    width="100%" height="100%" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
        </div>
    </div>
</section>

<?php require APPROOT . '/views/layout/footer.php'; ?>
