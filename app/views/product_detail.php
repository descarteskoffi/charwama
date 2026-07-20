<?php
/**
 * Vue - Fiche Produit Détaillée (Fallback SEO pour /produit/[id])
 */
require APPROOT . '/views/layout/header.php';
?>

<section class="container" style="padding: 40px 24px 80px;">
    <div style="margin-bottom: 24px;" data-reveal="fade-right">
        <a href="<?php echo DYNAMIC_URLROOT; ?>/menu"
           style="color: var(--primary); font-family: var(--font-titles); font-weight: 600; display: inline-flex; align-items: center; gap: 8px;">
            <i class="fa-solid fa-arrow-left" aria-hidden="true"></i> Retour au catalogue
        </a>
    </div>

    <div class="cart-layout" style="grid-template-columns: 1fr 1.1fr; gap: 60px;">
        <!-- Colonne Gauche : Image -->
        <div class="about-teaser-img" style="height: 450px; background-color: #1a1a20;" data-reveal="fade-right">
            <?php if ($product['image']): ?>
                <img src="<?php echo DYNAMIC_URLROOT; ?>/uploads/<?php echo htmlspecialchars($product['image']); ?>"
                     alt="<?php echo htmlspecialchars($product['nom']); ?>"
                     style="width: 100%; height: 100%; object-fit: cover;"
                     loading="lazy"
                     onerror="this.src='https://images.unsplash.com/photo-1561651823-34fed022540d?w=800&auto=format&fit=crop&q=60';">
            <?php else: ?>
                <img src="https://images.unsplash.com/photo-1561651823-34fed022540d?w=800&auto=format&fit=crop&q=60"
                     alt="Image par défaut" style="width: 100%; height: 100%; object-fit: cover;">
            <?php endif; ?>
        </div>

        <!-- Colonne Droite : Infos -->
        <div class="form-card" style="padding: 40px; background-color: var(--bg-card); border-radius: var(--radius-lg);" data-reveal="fade-left" data-reveal-delay="100">
            <span style="color: var(--primary); font-size: 0.85rem; font-weight: 600; text-transform: uppercase; margin-bottom: 8px; display: block;">
                Menu Spécialité
            </span>
            <h1 style="font-size: 2.5rem; margin-bottom: 12px; font-family: var(--font-titles); color: #ffffff;">
                <?php echo htmlspecialchars($product['nom']); ?>
            </h1>
            
            <div style="font-family: var(--font-titles); font-size: 1.8rem; font-weight: 700; color: var(--primary); margin-bottom: 24px;">
                <?php echo number_format($product['prix'], 0, ',', ' '); ?> <span style="font-size: 1rem; color: #a0a0a5;">FCFA</span>
            </div>

            <p style="color: #a0a0a5; line-height: 1.7; margin-bottom: 32px;">
                <?php echo htmlspecialchars($product['description']); ?>
            </p>

            <!-- Options / Suppléments -->
            <?php if (!empty($product['options'])): ?>
                <div style="margin-bottom: 32px;">
                    <h3 style="font-family: var(--font-titles); font-size: 1rem; margin-bottom: 16px; color: #ffffff;">
                        Personnalisez votre plat :
                    </h3>
                    <div class="modal-options-list">
                        <?php foreach ($product['options'] as $idx => $opt): ?>
                            <label class="option-item" for="detail_opt_<?php echo $idx; ?>">
                                <div class="option-checkbox-wrapper">
                                    <input type="checkbox" id="detail_opt_<?php echo $idx; ?>" 
                                           class="detail-option-checkbox" 
                                           data-name="<?php echo htmlspecialchars($opt['nom_option']); ?>" 
                                           data-price="<?php echo $opt['prix_supplement']; ?>" 
                                           style="display: none;" 
                                           onchange="toggleDetailCheckboxStyle(this)">
                                    <div class="option-checkbox">
                                        <i class="fa-solid fa-check"></i>
                                    </div>
                                    <span><?php echo htmlspecialchars($opt['nom_option']); ?></span>
                                </div>
                                <?php if ($opt['prix_supplement'] > 0): ?>
                                    <span class="option-price">+<?php echo number_format($opt['prix_supplement'], 0, ',', ' '); ?> F</span>
                                <?php else: ?>
                                    <span class="option-price" style="color: #71717a; font-weight: normal;">Gratuit</span>
                                <?php endif; ?>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Quantité -->
            <div class="quantity-wrapper" style="margin-bottom: 32px;">
                <span class="quantity-title">Quantité :</span>
                <div class="quantity-selector">
                    <button class="quantity-btn" onclick="adjustDetailQuantity(-1)"><i class="fa-solid fa-minus"></i></button>
                    <span class="quantity-val" id="detailQuantityVal">1</span>
                    <button class="quantity-btn" onclick="adjustDetailQuantity(1)"><i class="fa-solid fa-plus"></i></button>
                </div>
            </div>

            <!-- Boutons d'Action -->
            <div style="display: flex; gap: 16px;">
                <button class="btn btn-primary" style="flex-grow: 1;" onclick="addDetailPageToCart()">
                    <i class="fa-solid fa-cart-plus"></i> Ajouter au Panier
                </button>
                <a href="<?php echo DYNAMIC_URLROOT; ?>/menu" class="btn btn-secondary">
                    Continuer mes achats
                </a>
            </div>
        </div>
    </div>
</section>

<!-- Données JSON du produit injectées en JS pour traitement du panier -->
<script>
    const DETAIL_PRODUCT_DATA = {
        id: <?php echo $product['id']; ?>,
        nom: <?php echo json_encode($product['nom']); ?>,
        prix: <?php echo $product['prix']; ?>,
        image: <?php echo json_encode($product['image'] ? DYNAMIC_URLROOT . '/uploads/' . $product['image'] : ''); ?>
    };
    
    function toggleDetailCheckboxStyle(chk) {
        const item = chk.closest('.option-item');
        // Aucun traitement spécial requis, le sélecteur CSS gère le design,
        // mais cela peut servir d'accroche pour d'éventuels calculs de prix futurs.
    }
    
    function adjustDetailQuantity(val) {
        const qtySpan = document.getElementById('detailQuantityVal');
        let qty = parseInt(qtySpan.innerText) + val;
        if (qty < 1) qty = 1;
        qtySpan.innerText = qty;
    }
    
    function addDetailPageToCart() {
        const qty = parseInt(document.getElementById('detailQuantityVal').innerText);
        
        // Récupérer les options sélectionnées
        const selectedOptions = [];
        document.querySelectorAll('.detail-option-checkbox:checked').forEach(chk => {
            selectedOptions.push({
                nom: chk.getAttribute('data-name'),
                prix: parseFloat(chk.getAttribute('data-price'))
            });
        });
        
        // Ajouter au panier (utiliser la fonction de main.js)
        if (typeof addToCart === 'function') {
            addToCart(
                DETAIL_PRODUCT_DATA.id,
                DETAIL_PRODUCT_DATA.nom,
                DETAIL_PRODUCT_DATA.prix,
                DETAIL_PRODUCT_DATA.image,
                qty,
                selectedOptions
            );
            // Toast succès au lieu d'un alert()
            if (typeof showToast === 'function') {
                showToast(`"${DETAIL_PRODUCT_DATA.nom}" ajouté au panier !`, 'success', 3500, 'Ajouté au panier');
            }
            setTimeout(() => {
                window.location.href = DYNAMIC_URLROOT + '/menu';
            }, 1500);
        } else {
            // Fallback si main.js pas encore chargé
            let cart = JSON.parse(localStorage.getItem('chawarma_cart')) || [];
            cart.push({
                id: DETAIL_PRODUCT_DATA.id,
                nom: DETAIL_PRODUCT_DATA.nom,
                prixBase: DETAIL_PRODUCT_DATA.prix,
                prixUnitaire: DETAIL_PRODUCT_DATA.prix,
                image: DETAIL_PRODUCT_DATA.image,
                qty: qty,
                options: selectedOptions,
                optionsKey: selectedOptions.map(o => o.nom).sort().join('|')
            });
            localStorage.setItem('chawarma_cart', JSON.stringify(cart));
            window.location.href = DYNAMIC_URLROOT + '/menu';
        }
    }
</script>

<?php require APPROOT . '/views/layout/footer.php'; ?>
