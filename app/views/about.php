<?php
/**
 * Vue - Page À Propos (About)
 */
require APPROOT . '/views/layout/header.php';

$aProposTitre = $siteSettings['accueil_a_propos_titre'] ?? 'Une passion familiale pour le goût';
$aProposDesc = $siteSettings['accueil_a_propos_description'] ?? 'Depuis 2020, notre restaurant réinvente la street-food traditionnelle avec des viandes marinées 24h.';
?>

<!-- En-tête de page -->
<section class="hero" style="padding: 60px 0 40px; text-align: center; background: radial-gradient(circle at 50% 50%, rgba(255, 107, 8, 0.08) 0%, transparent 60%);">
    <div class="container">
        <span class="tag" style="color: var(--primary); font-family: var(--font-titles); font-weight: 600; text-transform: uppercase; font-size: 0.85rem; letter-spacing: 2px;">Notre Histoire</span>
        <h1 style="font-size: 3rem; margin-top: 12px;">Qui sommes-nous ?</h1>
    </div>
</section>

<!-- Contenu principal -->
<section class="about-teaser-sec" style="padding-top: 20px;">
    <div class="container about-teaser-grid">
        <div class="about-teaser-content">
            <h2 style="font-size: 2rem; margin-bottom: 24px; color: var(--primary);"><?php echo htmlspecialchars($aProposTitre); ?></h2>
            <p style="font-size: 1.05rem; line-height: 1.7; margin-bottom: 20px;"><?php echo nl2br(htmlspecialchars($aProposDesc)); ?></p>
            <p style="color: #a0a0a5; margin-bottom: 20px;">
                Chez <strong>JP-Charwama</strong>, notre vision de la restauration rapide est simple : allier rapidité et excellence gustative. Chaque jour, nos chefs découpent et préparent les broches de viande, marinent le poulet avec un bouquet d'épices soigneusement sélectionnées, et pétrissent notre sauce à l'ail maison pour relever vos sandwichs.
            </p>
            <p style="color: #a0a0a5;">
                Que vous soyez fan de panini fondant, amateur de sandwichs généreux ou passionné de chawarmas roulés dans leur pain libanais, vous trouverez votre bonheur dans nos recettes authentiques élaborées pour vous satisfaire à chaque bouchée.
            </p>
        </div>
        <div class="about-teaser-img">
            <img src="https://images.unsplash.com/photo-1541532713592-79a0317b6b77?w=600&auto=format&fit=crop&q=60" alt="Préparation de la viande" onerror="this.src='https://images.unsplash.com/photo-1561651823-34fed022540d?w=600&auto=format&fit=crop&q=60';">
        </div>
    </div>
</section>

<!-- Notre équipe / Valeurs -->
<section class="features-sec" style="background-color: rgba(255,255,255,0.01); border-top: 1px solid var(--border-color);">
    <div class="container">
        <div class="section-header">
            <span class="tag">Nos piliers</span>
            <h2>Nos Valeurs Fondamentales</h2>
            <p>Ce qui guide chacun de nos gestes en cuisine, de la sélection des matières premières à la remise de votre commande.</p>
        </div>
        
        <div class="features-grid">
            <div class="feature-card" style="text-align: center;">
                <div class="feature-icon" style="margin-left: auto; margin-right: auto;"><i class="fa-solid fa-heart"></i></div>
                <h3>L'Amour du Goût</h3>
                <p>Pas de raccourcis. Nos marinades et sauces sont réalisées maison chaque jour pour garantir des saveurs inimitables.</p>
            </div>
            
            <div class="feature-card" style="text-align: center;">
                <div class="feature-icon" style="margin-left: auto; margin-right: auto;"><i class="fa-solid fa-handshake"></i></div>
                <h3>La Confiance Locale</h3>
                <p>Nos légumes viennent d'agriculteurs locaux et nos viandes respectent des normes d'hygiène et de traçabilité strictes.</p>
            </div>
            
            <div class="feature-card" style="text-align: center;">
                <div class="feature-icon" style="margin-left: auto; margin-right: auto;"><i class="fa-solid fa-face-smile"></i></div>
                <h3>Le Service Client</h3>
                <p>Votre satisfaction est notre priorité absolue. Nous préparons vos commandes avec le plus grand soin et réagissons au quart de tour sur WhatsApp.</p>
            </div>
        </div>
    </div>
</section>

<?php require APPROOT . '/views/layout/footer.php'; ?>
