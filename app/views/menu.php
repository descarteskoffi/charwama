<?php
/**
 * Vue - Catalogue des Produits (Menu)
 */
require APPROOT . '/views/layout/header.php';
?>

<!-- En-tête de page -->
<section class="hero" style="padding: 60px 0 40px; text-align: center; background: radial-gradient(circle at 50% 50%, rgba(255, 107, 8, 0.08) 0%, transparent 60%);">
    <div class="container">
        <span class="tag" style="color: var(--primary); font-family: var(--font-titles); font-weight: 600; text-transform: uppercase; font-size: 0.85rem; letter-spacing: 2px;" data-reveal="fade-up">Notre Carte</span>
        <h1 style="font-size: 3rem; margin-top: 12px;" data-reveal="fade-up" data-reveal-delay="100">Découvrez nos Délices</h1>
    </div>
</section>

<section class="container" style="margin-bottom: 80px;">
    <!-- 1. BARRE DE RECHERCHE & FILTRES -->
    <div class="search-filter-wrapper" data-reveal="fade-up">
        <!-- Recherche textuelle -->
        <div class="search-bar">
            <i class="fa-solid fa-magnifying-glass" aria-hidden="true"></i>
            <input type="text" id="productSearchInput"
                   placeholder="Rechercher un produit (ex: Chawarma...)"
                   onkeyup="filterProducts()"
                   aria-label="Rechercher un produit dans le catalogue">
        </div>
        
        <!-- Nombre de produits affichés -->
        <div style="color: #a0a0a5; font-size: 0.9rem;" id="productCountLabel" aria-live="polite">
            Affichage de <?php echo count($products); ?> produit(s)
        </div>
    </div>

    <!-- Onglets des Catégories -->
    <div class="categories-filter" data-reveal="fade-up" data-reveal-delay="100" role="tablist" aria-label="Filtrer par catégorie">
        <button class="filter-tab <?php echo $selectedCatId === null ? 'active' : ''; ?>"
                onclick="selectCategory(null, this)"
                id="filterTabAll"
                role="tab"
                <?php echo $selectedCatId === null ? 'aria-selected="true"' : ''; ?>>
            Tous
        </button>
        <?php foreach ($categories as $cat): ?>
            <button class="filter-tab <?php echo $selectedCatId === (int)$cat['id'] ? 'active' : ''; ?>"
                    onclick="selectCategory(<?php echo (int)$cat['id']; ?>, this)"
                    id="filterTab_<?php echo (int)$cat['id']; ?>"
                    role="tab"
                    <?php echo $selectedCatId === (int)$cat['id'] ? 'aria-selected="true"' : ''; ?>>
                <?php echo htmlspecialchars($cat['nom']); ?>
            </button>
        <?php endforeach; ?>
    </div>

    <!-- 2. GRILLE DES PRODUITS -->
    <div class="products-grid" id="productsGrid">
        <?php if (empty($products)): ?>
            <div style="grid-column: 1/-1; text-align: center; color: #a0a0a5; padding: 80px 0;" id="emptySearchPlaceholder">
                <i class="fa-solid fa-face-frown" style="font-size: 4rem; margin-bottom: 24px; color: #3f3f46;"></i>
                <h2>Aucun produit trouvé</h2>
                <p style="margin-top: 8px;">Essayez d'ajuster votre recherche ou filtre.</p>
            </div>
        <?php else: ?>
            <?php $pIdx = 0; foreach ($products as $product): $pIdx++; ?>
                <article class="product-card"
                         data-category="<?php echo (int)$product['categorie_id']; ?>"
                         data-name="<?php echo htmlspecialchars(strtolower($product['nom'])); ?>"
                         data-name-real="<?php echo htmlspecialchars($product['nom']); ?>"
                         data-id="<?php echo (int)$product['id']; ?>"
                         data-desc="<?php echo htmlspecialchars($product['description']); ?>"
                         data-price="<?php echo (float)$product['prix']; ?>"
                         data-image="<?php echo $product['image'] ? DYNAMIC_URLROOT . '/uploads/' . htmlspecialchars($product['image']) : ''; ?>"
                         data-options='<?php echo htmlspecialchars(json_encode($product['options']), ENT_QUOTES, 'UTF-8'); ?>'
                         data-reveal="fade-up"
                         data-reveal-delay="<?php echo min(($pIdx % 4) * 100, 400); ?>">

                    <div class="product-card-img-wrapper">
                        <div class="product-badge-overlay">
                            <?php if ($product['prix'] >= 3000): ?>
                                <span class="badge badge-popular">Spécial</span>
                            <?php else: ?>
                                <span class="badge badge-new">Nouveau</span>
                            <?php endif; ?>
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
                        <span style="color: var(--primary); font-size: 0.8rem; font-weight: 600; text-transform: uppercase; margin-bottom: 4px; display: block;">
                            <?php echo htmlspecialchars($product['categorie_nom']); ?>
                        </span>
                        <h3 class="product-card-title"><?php echo htmlspecialchars($product['nom']); ?></h3>
                        <p class="product-card-desc"><?php echo htmlspecialchars($product['description']); ?></p>
                        
                        <div class="product-card-footer">
                            <span class="product-card-price">
                                <?php echo number_format($product['prix'], 0, ',', ' '); ?> <span>FCFA</span>
                            </span>
                            <button class="btn btn-primary btn-sm"
                                    onclick="openProductModal(this)"
                                    id="menuProductBtn_<?php echo (int)$product['id']; ?>"
                                    aria-label="Choisir <?php echo htmlspecialchars($product['nom']); ?>">
                                <i class="fa-solid fa-cart-plus" aria-hidden="true"></i> Choisir
                            </button>
                        </div>
                    </div>
                </article>
            <?php endforeach; ?>
        <?php endif; ?>
        
        <!-- Placeholder masqué par défaut pour recherche vide -->
        <div style="grid-column: 1/-1; text-align: center; color: #a0a0a5; padding: 80px 0; display: none;" id="jsEmptySearchPlaceholder">
            <i class="fa-solid fa-face-frown" style="font-size: 4rem; margin-bottom: 24px; color: #3f3f46;"></i>
            <h2>Aucun produit ne correspond</h2>
            <p style="margin-top: 8px;">Essayez d'autres mots-clés ou modifiez vos filtres.</p>
        </div>
    </div>
</section>

<!-- 3. MODAL DE DÉTAIL PRODUIT -->
<div class="modal" id="productModal">
    <div class="modal-overlay" onclick="closeProductModal()"></div>
    <div class="modal-container">
        <div class="modal-close" onclick="closeProductModal()">
            <i class="fa-solid fa-xmark"></i>
        </div>
        
        <div class="modal-grid">
            <!-- Partie Image -->
            <div class="modal-image-wrapper">
                <img id="modalProductImage" src="https://images.unsplash.com/photo-1561651823-34fed022540d?w=500&auto=format&fit=crop&q=60" alt="Produit">
            </div>
            
            <!-- Partie Contenu -->
            <div class="modal-body">
                <input type="hidden" id="modalProductId">
                <h3 class="modal-title" id="modalProductTitle">Nom du produit</h3>
                <div class="modal-price" id="modalProductPrice">0 FCFA</div>
                <p class="modal-desc" id="modalProductDesc">Description complète du produit.</p>
                
                <!-- Zone Suppléments / Options -->
                <div id="modalOptionsWrapper">
                    <h4 class="modal-options-title">Choisissez vos options / suppléments :</h4>
                    <div class="modal-options-list" id="modalOptionsList">
                        <!-- Généré dynamiquement en JS -->
                    </div>
                </div>
                
                <!-- Sélecteur de Quantité -->
                <div class="quantity-wrapper">
                    <span class="quantity-title">Quantité :</span>
                    <div class="quantity-selector">
                        <button class="quantity-btn" onclick="adjustModalQuantity(-1)"><i class="fa-solid fa-minus"></i></button>
                        <span class="quantity-val" id="modalQuantityVal">1</span>
                        <button class="quantity-btn" onclick="adjustModalQuantity(1)"><i class="fa-solid fa-plus"></i></button>
                    </div>
                </div>
                
                <!-- Bouton Ajouter -->
                <button class="btn btn-primary" style="width: 100%;" onclick="addModalProductToCart()">
                    <i class="fa-solid fa-cart-plus"></i> Ajouter à ma commande
                </button>
            </div>
        </div>
    </div>
</div>

<?php require APPROOT . '/views/layout/footer.php'; ?>
