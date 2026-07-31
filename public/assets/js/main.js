/**
 * main.js — Script Principal Chawarma Premium
 * Version 2.0 : Persistance des commandes en BDD, Gestion des tables/QR codes,
 *                Vérification d'ouverture, Récapitulatif de commande amélioré
 *
 * Modules :
 *   1.  Page Loader
 *   2.  Menu mobile
 *   3.  Thème Dark / Light
 *   4.  Scroll Progress Bar + Navbar scroll effect
 *   5.  Scroll Reveal (IntersectionObserver)
 *   6.  Lazy Loading des images
 *   7.  Toast Notifications
 *   8.  Gestion du Panier (localStorage)
 *   9.  Filtrage et Recherche (page catalogue)
 *   10. Modal Produit
 *   11. Page Panier & Construction de la commande (refonte complète)
 *   12. Détection de table via QR code (URL param ?table=...)
 *   13. Vérification d'ouverture du restaurant
 *   14. Soumission de commande (DB + WhatsApp)
 *   15. URL Product Pre-selection
 */

// =========================================================================
// CONSTANTE GLOBALE : TABLE DÉTECTÉE VIA QR CODE
// =========================================================================
let detectedTableFromQR = null;

document.addEventListener('DOMContentLoaded', () => {
    // --- Initialisation ---
    initPageLoader();
    initMobileNav();
    initTheme();
    initScrollProgress();
    initScrollReveal();
    initLazyImages();
    updateCartBadge();

    // Détection de table depuis l'URL (QR code)
    detectTableFromUrl();

    // Vérification d'ouverture avant rendu du panier
    if (document.getElementById('cartItemsList')) {
        renderCartPage();
        checkRestaurantOpening();
        initCartPageTable();
    }

    checkUrlForProduct();
    checkRestaurantOpeningBanner();
});

/* =========================================================================
   1. PAGE LOADER
   ========================================================================= */
function initPageLoader() {
    const loader = document.getElementById('pageLoader');
    if (!loader) return;

    window.addEventListener('load', () => {
        setTimeout(() => {
            loader.classList.add('hidden');
        }, 300);
    });

    setTimeout(() => {
        loader.classList.add('hidden');
    }, 2000);
}

/* =========================================================================
   2. MENU DE NAVIGATION MOBILE
   ========================================================================= */
function initMobileNav() {
    const toggle = document.getElementById('mobileNavToggle');
    const menu = document.getElementById('navMenu');

    if (!toggle || !menu) return;

    toggle.addEventListener('click', () => {
        menu.classList.toggle('active');
        const icon = toggle.querySelector('i');
        if (menu.classList.contains('active')) {
            icon.classList.replace('fa-bars', 'fa-xmark');
        } else {
            icon.classList.replace('fa-xmark', 'fa-bars');
        }
    });

    document.addEventListener('click', (e) => {
        if (!toggle.contains(e.target) && !menu.contains(e.target)) {
            menu.classList.remove('active');
            const icon = toggle.querySelector('i');
            if (icon) icon.classList.replace('fa-xmark', 'fa-bars');
        }
    });
}

/* =========================================================================
   3. THÈME DARK / LIGHT MODE
   ========================================================================= */
function initTheme() {
    const btn = document.getElementById('themeToggleBtn');
    if (!btn) return;

    const applyTheme = (theme) => {
        if (theme === 'light') {
            document.documentElement.setAttribute('data-theme', 'light');
            btn.setAttribute('aria-label', 'Passer en mode sombre');
            btn.setAttribute('title', 'Passer en mode sombre');
        } else {
            document.documentElement.removeAttribute('data-theme');
            btn.setAttribute('aria-label', 'Passer en mode clair');
            btn.setAttribute('title', 'Passer en mode clair');
        }
        localStorage.setItem('chawarma_theme', theme);
    };

    const currentTheme = document.documentElement.getAttribute('data-theme') || 'dark';
    applyTheme(currentTheme);

    btn.addEventListener('click', () => {
        const current = document.documentElement.getAttribute('data-theme');
        const next = current === 'light' ? 'dark' : 'light';
        applyTheme(next);

        const label = next === 'light' ? '☀️ Mode clair activé' : '🌙 Mode sombre activé';
        showToast(label, 'info', 2000, 'Thème modifié');
    });
}

/* =========================================================================
   4. SCROLL PROGRESS BAR + NAVBAR EFFECT
   ========================================================================= */
function initScrollProgress() {
    const progressBar = document.getElementById('scrollProgressBar');
    const navbar = document.querySelector('.navbar');

    if (!progressBar && !navbar) return;

    const onScroll = () => {
        const scrollTop = window.scrollY || document.documentElement.scrollTop;
        const docHeight = document.documentElement.scrollHeight - document.documentElement.clientHeight;
        const scrollPercent = docHeight > 0 ? (scrollTop / docHeight) * 100 : 0;

        if (progressBar) {
            progressBar.style.width = Math.min(scrollPercent, 100) + '%';
        }

        if (navbar) {
            if (scrollTop > 60) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }
        }
    };

    window.addEventListener('scroll', onScroll, { passive: true });
    onScroll();
}

/* =========================================================================
   5. SCROLL REVEAL (IntersectionObserver)
   ========================================================================= */
function initScrollReveal() {
    const elements = document.querySelectorAll('[data-reveal]');
    if (elements.length === 0) return;

    const observer = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
            if (entry.isIntersecting) {
                entry.target.classList.add('is-revealed');
                observer.unobserve(entry.target);
            }
        });
    }, {
        threshold: 0.12,
        rootMargin: '0px 0px -60px 0px'
    });

    elements.forEach((el) => observer.observe(el));
}

/* =========================================================================
   6. LAZY LOADING DES IMAGES
   ========================================================================= */
function initLazyImages() {
    const lazyImgs = document.querySelectorAll('img[data-src]');
    if (lazyImgs.length === 0) return;

    if ('IntersectionObserver' in window) {
        const imgObserver = new IntersectionObserver((entries) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting) {
                    const img = entry.target;
                    img.src = img.getAttribute('data-src');
                    img.removeAttribute('data-src');
                    img.classList.add('lazy-loaded');
                    imgObserver.unobserve(img);
                }
            });
        }, { rootMargin: '200px 0px' });

        lazyImgs.forEach((img) => imgObserver.observe(img));
    } else {
        lazyImgs.forEach((img) => {
            img.src = img.getAttribute('data-src');
            img.removeAttribute('data-src');
        });
    }
}

/* =========================================================================
   7. TOAST NOTIFICATIONS
   ========================================================================= */
const TOAST_ICONS = {
    success: 'fa-circle-check',
    error:   'fa-circle-xmark',
    warning: 'fa-triangle-exclamation',
    info:    'fa-circle-info',
};

const TOAST_TITLES = {
    success: 'Succès',
    error:   'Erreur',
    warning: 'Attention',
    info:    'Information',
};

function showToast(message, type = 'info', duration = 4000, title = null) {
    const container = document.getElementById('toastContainer');
    if (!container) return;

    const toast = document.createElement('div');
    toast.className = `toast ${type}`;
    toast.setAttribute('role', 'alert');
    toast.setAttribute('aria-live', 'polite');

    const icon = TOAST_ICONS[type] || TOAST_ICONS.info;
    const toastTitle = title || TOAST_TITLES[type] || 'Info';

    toast.innerHTML = `
        <div class="toast-icon"><i class="fa-solid ${icon}"></i></div>
        <div class="toast-body">
            <div class="toast-title">${escapeHtml(toastTitle)}</div>
            <div class="toast-message">${escapeHtml(message)}</div>
        </div>
        <div class="toast-close" onclick="dismissToast(this.closest('.toast'))" title="Fermer">
            <i class="fa-solid fa-xmark"></i>
        </div>
        <div class="toast-progress" style="animation-duration: ${duration}ms;"></div>
    `;

    container.appendChild(toast);

    const timer = setTimeout(() => dismissToast(toast), duration);
    toast._timer = timer;
}

function dismissToast(toast) {
    if (!toast || toast._dismissed) return;
    toast._dismissed = true;
    clearTimeout(toast._timer);
    toast.classList.add('hiding');
    toast.addEventListener('animationend', () => toast.remove(), { once: true });
}

function escapeHtml(str) {
    const div = document.createElement('div');
    div.textContent = str;
    return div.innerHTML;
}

/* =========================================================================
   8. GESTION DU PANIER (LOCAL STORAGE)
   ========================================================================= */
function getCart() {
    return JSON.parse(localStorage.getItem('chawarma_cart')) || [];
}

function saveCart(cart) {
    localStorage.setItem('chawarma_cart', JSON.stringify(cart));
    updateCartBadge();
}

function updateCartBadge() {
    const badge = document.getElementById('cartBadge');
    if (!badge) return;

    const cart = getCart();
    const totalItems = cart.reduce((sum, item) => sum + item.qty, 0);
    badge.innerText = totalItems;

    if (totalItems > 0) {
        badge.classList.remove('bounce');
        void badge.offsetWidth;
        badge.classList.add('bounce');
    }
}

function addToCart(id, name, basePrice, image, qty, options = []) {
    const cart = getCart();

    const optionsKey = options.map(o => o.nom).sort().join('|');
    const existingIndex = cart.findIndex(item => item.id === id && item.optionsKey === optionsKey);

    if (existingIndex > -1) {
        cart[existingIndex].qty += qty;
    } else {
        const optionsPriceSum = options.reduce((sum, o) => sum + parseFloat(o.prix), 0);
        const itemPrice = basePrice + optionsPriceSum;

        cart.push({
            id,
            nom: name,
            prixBase: basePrice,
            prixUnitaire: itemPrice,
            image,
            qty,
            options,
            optionsKey
        });
    }

    saveCart(cart);
}

/* =========================================================================
   9. FILTRAGE ET RECHERCHE (PAGE CATALOGUE)
   ========================================================================= */
let activeCategoryId = null;

function selectCategory(catId, element) {
    activeCategoryId = catId;

    document.querySelectorAll('.filter-tab').forEach(tab => tab.classList.remove('active'));
    element.classList.add('active');

    filterProducts();
}

function filterProducts() {
    const searchInput = document.getElementById('productSearchInput');
    const searchQuery = searchInput ? searchInput.value.toLowerCase().trim() : '';

    const cards = document.querySelectorAll('#productsGrid .product-card');
    let visibleCount = 0;

    cards.forEach(card => {
        const cardCatId = parseInt(card.getAttribute('data-category'));
        const cardName = (card.getAttribute('data-name') || '').toLowerCase();
        const descEl = card.querySelector('.product-card-desc');
        const cardDesc = descEl ? descEl.innerText.toLowerCase() : '';

        const matchesCategory = (activeCategoryId === null || cardCatId === activeCategoryId);
        const matchesSearch = (searchQuery === '' || cardName.includes(searchQuery) || cardDesc.includes(searchQuery));

        if (matchesCategory && matchesSearch) {
            card.style.display = 'flex';
            visibleCount++;
        } else {
            card.style.display = 'none';
        }
    });

    const counter = document.getElementById('productCountLabel');
    if (counter) {
        counter.innerText = `${visibleCount} produit(s) affiché(s)`;
    }

    const placeholder = document.getElementById('jsEmptySearchPlaceholder');
    const staticPlaceholder = document.getElementById('emptySearchPlaceholder');
    if (placeholder) {
        if (visibleCount === 0) {
            placeholder.style.display = 'block';
            if (staticPlaceholder) staticPlaceholder.style.display = 'none';
        } else {
            placeholder.style.display = 'none';
        }
    }
}

/* =========================================================================
   10. MODAL PRODUIT
   ========================================================================= */
function openProductModal(button) {
    const card = button.closest('.product-card');

    const id = parseInt(card.getAttribute('data-id'));
    const name = card.getAttribute('data-name-real') || card.querySelector('.product-card-title').innerText;
    const desc = card.getAttribute('data-desc');
    const price = parseFloat(card.getAttribute('data-price'));
    const image = card.getAttribute('data-image');
    const options = JSON.parse(card.getAttribute('data-options') || '[]');

    document.getElementById('modalProductId').value = id;
    document.getElementById('modalProductTitle').innerText = name;
    document.getElementById('modalProductDesc').innerText = desc;
    document.getElementById('modalProductPrice').innerText = `${price.toLocaleString('fr-FR')} FCFA`;
    document.getElementById('modalQuantityVal').innerText = '1';

    const modalImg = document.getElementById('modalProductImage');
    if (image) {
        modalImg.src = image;
        modalImg.style.display = 'block';
    } else {
        modalImg.src = 'https://images.unsplash.com/photo-1561651823-34fed022540d?w=500&auto=format&fit=crop&q=60';
    }

    const optionsList = document.getElementById('modalOptionsList');
    const optionsWrapper = document.getElementById('modalOptionsWrapper');
    optionsList.innerHTML = '';

    if (options.length === 0) {
        optionsWrapper.style.display = 'none';
    } else {
        optionsWrapper.style.display = 'block';
        options.forEach((opt, index) => {
            const label = document.createElement('label');
            label.className = 'option-item';
            label.htmlFor = `modal_opt_${index}`;
            const priceText = opt.prix_supplement > 0
                ? `+${parseFloat(opt.prix_supplement).toLocaleString('fr-FR')} F`
                : 'Gratuit';
            label.innerHTML = `
                <div class="option-checkbox-wrapper">
                    <input type="checkbox" id="modal_opt_${index}"
                           class="modal-option-checkbox"
                           data-name="${escapeHtml(opt.nom_option)}"
                           data-price="${opt.prix_supplement}"
                           style="display: none;">
                    <div class="option-checkbox">
                        <i class="fa-solid fa-check"></i>
                    </div>
                    <span>${escapeHtml(opt.nom_option)}</span>
                </div>
                <span class="option-price">${priceText}</span>
            `;
            optionsList.appendChild(label);
        });
    }

    document.getElementById('productModal').classList.add('active');
    document.body.style.overflow = 'hidden';
}

function closeProductModal() {
    document.getElementById('productModal').classList.remove('active');
    document.body.style.overflow = '';
}

document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') {
        closeProductModal();
        closeRecapModal();
    }
});

function adjustModalQuantity(val) {
    const qtySpan = document.getElementById('modalQuantityVal');
    let qty = parseInt(qtySpan.innerText) + val;
    if (qty < 1) qty = 1;
    qtySpan.innerText = qty;
}

function addModalProductToCart() {
    const id = parseInt(document.getElementById('modalProductId').value);
    const name = document.getElementById('modalProductTitle').innerText;

    const rawPrice = document.getElementById('modalProductPrice').innerText.replace(/[^0-9]/g, '');
    const basePrice = parseFloat(rawPrice);

    const qty = parseInt(document.getElementById('modalQuantityVal').innerText);
    const image = document.getElementById('modalProductImage').src;

    const selectedOptions = [];
    document.querySelectorAll('.modal-option-checkbox:checked').forEach(chk => {
        selectedOptions.push({
            nom: chk.getAttribute('data-name'),
            prix: parseFloat(chk.getAttribute('data-price'))
        });
    });

    addToCart(id, name, basePrice, image, qty, selectedOptions);
    closeProductModal();

    showToast(`"${name}" a été ajouté à votre commande !`, 'success', 4000, 'Ajouté au panier');
}

/* =========================================================================
   11. PAGE PANIER - RENDU & ACTIONS
   ========================================================================= */
function renderCartPage() {
    const container = document.getElementById('cartItemsList');
    const emptyState = document.getElementById('emptyCartState');
    const activeLayout = document.getElementById('activeCartLayout');

    if (!container) return;

    const cart = getCart();

    if (cart.length === 0) {
        emptyState.style.display = 'block';
        activeLayout.style.display = 'none';
        return;
    }

    emptyState.style.display = 'none';
    activeLayout.style.display = 'grid';
    container.innerHTML = '';

    let grandTotal = 0;

    cart.forEach((item, index) => {
        const itemTotal = item.prixUnitaire * item.qty;
        grandTotal += itemTotal;

        let optionsHtml = '';
        if (item.options && item.options.length > 0) {
            const optList = item.options.map(o =>
                `${escapeHtml(o.nom)} (${parseFloat(o.prix) > 0 ? `+${o.prix}F` : 'Gratuit'})`
            ).join(', ');
            optionsHtml = `<div class="cart-item-options">Suppléments : ${optList}</div>`;
        }

        const imgSrc = item.image || 'https://images.unsplash.com/photo-1561651823-34fed022540d?w=500&auto=format&fit=crop&q=60';

        const cartItemDiv = document.createElement('div');
        cartItemDiv.className = 'cart-item';
        cartItemDiv.setAttribute('data-reveal', 'fade-up');
        cartItemDiv.setAttribute('data-reveal-delay', String(Math.min(index * 100, 500)));

        cartItemDiv.innerHTML = `
            <div class="cart-item-img">
                <img src="${escapeHtml(imgSrc)}" alt="${escapeHtml(item.nom)}" loading="lazy">
            </div>
            <div class="cart-item-details">
                <h4 class="cart-item-name">${escapeHtml(item.nom)}</h4>
                ${optionsHtml}
                <div class="cart-item-price">
                    ${item.prixUnitaire.toLocaleString('fr-FR')} FCFA
                    <span style="font-size:0.8rem;font-weight:normal;color:#a0a0a5;"> l'unité</span>
                </div>
            </div>
            <div class="cart-item-actions">
                <div class="cart-item-remove" onclick="removeCartItem(${index})" title="Retirer l'article" role="button" tabindex="0">
                    <i class="fa-solid fa-trash-can"></i>
                </div>
                <div class="quantity-selector">
                    <button class="quantity-btn" onclick="adjustCartItemQuantity(${index}, -1)" aria-label="Diminuer la quantité">
                        <i class="fa-solid fa-minus"></i>
                    </button>
                    <span class="quantity-val">${item.qty}</span>
                    <button class="quantity-btn" onclick="adjustCartItemQuantity(${index}, 1)" aria-label="Augmenter la quantité">
                        <i class="fa-solid fa-plus"></i>
                    </button>
                </div>
                <div style="font-family:var(--font-titles);font-weight:700;margin-top:4px;">
                    Sous-total : ${itemTotal.toLocaleString('fr-FR')} F
                </div>
            </div>
        `;

        container.appendChild(cartItemDiv);
    });

    const totalQty = cart.reduce((sum, item) => sum + item.qty, 0);
    document.getElementById('summaryCount').innerText = totalQty;
    document.getElementById('summaryTotal').innerText = `${grandTotal.toLocaleString('fr-FR')} FCFA`;

    initScrollReveal();
}

function adjustCartItemQuantity(index, delta) {
    const cart = getCart();
    cart[index].qty += delta;

    if (cart[index].qty < 1) {
        const name = cart[index].nom;
        cart.splice(index, 1);
        saveCart(cart);
        renderCartPage();
        showToast(`"${name}" a été retiré de votre commande.`, 'warning', 3000, 'Article retiré');
        return;
    }

    saveCart(cart);
    renderCartPage();
}

function removeCartItem(index) {
    const cart = getCart();
    const name = cart[index].nom;
    cart.splice(index, 1);
    saveCart(cart);
    renderCartPage();
    showToast(`"${name}" a été retiré de votre commande.`, 'warning', 3000, 'Article retiré');
}

/* =========================================================================
   12. DÉTECTION DE TABLE VIA QR CODE (?table=...)
   ========================================================================= */
function detectTableFromUrl() {
    const urlParams = new URLSearchParams(window.location.search);
    const tableNum = urlParams.get('table');

    if (tableNum) {
        // Stocker dans sessionStorage pour persister sur la page du panier
        sessionStorage.setItem('chawarma_detected_table', tableNum);
        detectedTableFromQR = tableNum;
    } else {
        // Ne pas effacer une table déjà détectée si on navigue vers une autre page
        const stored = sessionStorage.getItem('chawarma_detected_table');
        if (stored) {
            detectedTableFromQR = stored;
        }
    }
}

/* =========================================================================
   INIT DES CHAMPS SELON MODE DE RÉCEPTION (PANIER)
   ========================================================================= */
function initCartPageTable() {
    // Appliquer la logique initiale selon la table détectée
    toggleOrderModeFields();
}

function toggleOrderModeFields() {
    const mode = document.getElementById('orderMode') ? document.getElementById('orderMode').value : null;
    if (!mode) return;

    const tableGroup = document.getElementById('tableSelectionGroup');
    const deliveryGroup = document.getElementById('deliveryAddressGroup');
    const detectedInfo = document.getElementById('detectedTableInfo');
    const tableSelect = document.getElementById('tableNumero');
    const detectedSpan = document.getElementById('detectedTableNumSpan');
    const addressInput = document.getElementById('clientAddress');

    // Cacher tout par défaut
    if (tableGroup) tableGroup.style.display = 'none';
    if (deliveryGroup) deliveryGroup.style.display = 'none';
    if (addressInput) addressInput.removeAttribute('required');

    if (mode === 'Sur place') {
        // Afficher la section table
        if (tableGroup) tableGroup.style.display = 'block';

        if (detectedTableFromQR) {
            // Table détectée via QR code → afficher le message de confirmation
            if (detectedInfo) {
                detectedInfo.style.display = 'block';
                if (detectedSpan) detectedSpan.textContent = detectedTableFromQR;
            }
            // Présélectionner la table dans le select si possible
            if (tableSelect) {
                for (let opt of tableSelect.options) {
                    if (opt.value === detectedTableFromQR) {
                        opt.selected = true;
                        break;
                    }
                }
                tableSelect.style.display = 'none'; // La table est auto-détectée, pas besoin du select
            }
        } else {
            // Pas de table détectée → sélection manuelle obligatoire
            if (detectedInfo) detectedInfo.style.display = 'none';
            if (tableSelect) tableSelect.style.display = 'block';
        }

    } else if (mode === 'Livraison') {
        if (deliveryGroup) deliveryGroup.style.display = 'block';
        if (addressInput) addressInput.setAttribute('required', 'required');
        // Réinitialiser la sélection de table
        if (detectedInfo) detectedInfo.style.display = 'none';
    } else {
        // À emporter : aucune table, aucune adresse requise
        if (detectedInfo) detectedInfo.style.display = 'none';
    }
}

/* =========================================================================
   13. VÉRIFICATION D'OUVERTURE DU RESTAURANT
   ========================================================================= */
function checkRestaurantOpening() {
    const submitBtn = document.getElementById('cartSubmitBtn');
    if (!submitBtn) return;

    fetch(DYNAMIC_URLROOT + '/api/ouverture')
        .then(res => res.json())
        .then(data => {
            if (!data.isOpen) {
                // Désactiver le bouton de commande
                submitBtn.disabled = true;
                submitBtn.style.opacity = '0.4';
                submitBtn.style.cursor = 'not-allowed';
                submitBtn.style.backgroundColor = '#666';
                submitBtn.style.borderColor = '#666';
                submitBtn.style.boxShadow = 'none';
                submitBtn.innerHTML = '<i class="fa-solid fa-lock"></i> Restaurant fermé — Commandes indisponibles';

                // Afficher une bannière d'information
                const cartPage = document.querySelector('.cart-summary');
                if (cartPage) {
                    const banner = document.createElement('div');
                    banner.style.cssText = 'padding: 16px; background-color: rgba(239, 68, 68, 0.15); border-left: 4px solid #ef4444; color: #ef4444; font-weight: bold; border-radius: 4px; margin-bottom: 20px; line-height: 1.5;';
                    banner.innerHTML = `<i class="fa-solid fa-circle-xmark" style="margin-right: 8px;"></i> ${escapeHtml(data.message)}`;
                    cartPage.insertBefore(banner, cartPage.querySelector('.summary-row'));
                }
            }
        })
        .catch(() => {
            // En cas d'erreur réseau, ne pas bloquer (tolérance en faveur du client)
        });
}

function checkRestaurantOpeningBanner() {
    // Afficher une bannière globale discrète si le restaurant est fermé (sur toutes les pages)
    const existingBanner = document.getElementById('restaurantClosedBanner');
    if (existingBanner) return; // Déjà présent

    const urlRoot = (typeof DYNAMIC_URLROOT !== 'undefined') ? DYNAMIC_URLROOT : '';
    fetch(urlRoot + '/api/ouverture')
        .then(res => res.json())
        .then(data => {
            if (!data.isOpen) {
                const banner = document.createElement('div');
                banner.id = 'restaurantClosedBanner';
                banner.setAttribute('role', 'alert');
                banner.style.cssText = `
                    position: fixed;
                    bottom: 90px;
                    left: 50%;
                    transform: translateX(-50%);
                    background: linear-gradient(135deg, rgba(239,68,68,0.95), rgba(180,30,30,0.95));
                    color: #fff;
                    padding: 14px 24px;
                    border-radius: 50px;
                    font-size: 0.9rem;
                    font-weight: bold;
                    z-index: 999;
                    box-shadow: 0 8px 25px rgba(239,68,68,0.4);
                    max-width: 90vw;
                    text-align: center;
                    backdrop-filter: blur(8px);
                    animation: slideInUp 0.4s ease;
                `;
                banner.innerHTML = `<i class="fa-solid fa-moon" style="margin-right: 8px;"></i> ${escapeHtml(data.message)}`;
                document.body.appendChild(banner);
            }
        })
        .catch(() => {});
}

/* =========================================================================
   14. SOUMISSION DE COMMANDE : DB + WHATSAPP
   ========================================================================= */

// Objet pour stocker les données de la commande en attente de confirmation
let pendingOrderData = null;

/**
 * Étape 1 : Validation du formulaire et ouverture de la modale de récapitulatif
 */
function handleOrderFormSubmit(event) {
    event.preventDefault();

    const clientName = document.getElementById('clientName').value.trim();
    const orderMode = document.getElementById('orderMode').value;
    const cart = getCart();

    // Validations de base
    if (cart.length === 0) {
        showToast('Votre panier est vide. Ajoutez des articles avant de commander.', 'error', 4000, 'Panier vide');
        return;
    }
    if (!clientName) {
        showToast('Veuillez saisir votre nom pour la commande.', 'warning', 4000, 'Champ requis');
        document.getElementById('clientName').focus();
        return;
    }

    // Récupérer le numéro de téléphone (optionnel sur le formulaire visible, on l'ajoutera si besoin)
    const telephoneInput = document.getElementById('clientPhone');
    const telephone = telephoneInput ? telephoneInput.value.trim() : '';

    // Gestion de la table
    let tableNumero = '';
    if (orderMode === 'Sur place') {
        if (detectedTableFromQR) {
            tableNumero = detectedTableFromQR;
        } else {
            const tableSelect = document.getElementById('tableNumero');
            tableNumero = tableSelect ? tableSelect.value : '';
        }
        if (!tableNumero) {
            showToast('Veuillez sélectionner votre numéro de table.', 'warning', 4000, 'Table requise');
            return;
        }
    }

    // Gestion de l'adresse livraison
    let adresseLivraison = '';
    if (orderMode === 'Livraison') {
        adresseLivraison = document.getElementById('clientAddress') ? document.getElementById('clientAddress').value.trim() : '';
        if (!adresseLivraison) {
            showToast('Veuillez saisir votre adresse de livraison.', 'warning', 4000, 'Adresse requise');
            document.getElementById('clientAddress').focus();
            return;
        }
    }

    const orderNotes = document.getElementById('orderNotes') ? document.getElementById('orderNotes').value.trim() : '';

    // Calcul du total
    let grandTotal = 0;
    cart.forEach(item => {
        grandTotal += item.prixUnitaire * item.qty;
    });

    // Stocker les données pour la confirmation finale
    pendingOrderData = {
        clientName,
        telephone: telephone || 'N/A',
        orderMode,
        tableNumero,
        adresseLivraison,
        orderNotes,
        cart,
        grandTotal
    };

    // Construire et afficher le récapitulatif dans la modale
    buildRecapModal(pendingOrderData);
    openRecapModal();
}

/**
 * Construit le contenu HTML de la modale de récapitulatif
 */
function buildRecapModal(data) {
    const recapContent = document.getElementById('recapModalContent');
    if (!recapContent) return;

    // Destination selon le mode
    let destinationHtml = '';
    if (data.orderMode === 'Sur place') {
        destinationHtml = `<strong style="color: #2ec4b6;"><i class="fa-solid fa-chair"></i> Table ${escapeHtml(data.tableNumero)}</strong>`;
    } else if (data.orderMode === 'Livraison') {
        destinationHtml = `<strong style="color: #3b82f6;"><i class="fa-solid fa-truck"></i> ${escapeHtml(data.adresseLivraison)}</strong>`;
    } else {
        destinationHtml = `<strong style="color: #ff6b08;"><i class="fa-solid fa-shop"></i> Retrait au comptoir</strong>`;
    }

    // Liste des articles
    let itemsHtml = '<ul style="list-style: none; padding: 0; margin: 0 0 16px 0;">';
    data.cart.forEach(item => {
        itemsHtml += `<li style="display:flex; justify-content:space-between; padding: 8px 0; border-bottom: 1px solid rgba(255,255,255,0.05);">
            <span><strong style="color: var(--primary);">${item.qty}x</strong> ${escapeHtml(item.nom)}</span>
            <span>${(item.prixUnitaire * item.qty).toLocaleString('fr-FR')} F</span>
        </li>`;
        if (item.options && item.options.length > 0) {
            item.options.forEach(opt => {
                itemsHtml += `<li style="padding: 2px 0 2px 16px; font-size: 0.8rem; color: #a0a0a5; font-style: italic;">
                    + ${escapeHtml(opt.nom)} ${parseFloat(opt.prix) > 0 ? `(+${opt.prix}F)` : '(Gratuit)'}
                </li>`;
            });
        }
    });
    itemsHtml += '</ul>';

    recapContent.innerHTML = `
        ${itemsHtml}
        <div style="display: grid; gap: 12px; padding: 16px; background: rgba(255,255,255,0.03); border-radius: 8px; border: 1px solid rgba(255,255,255,0.06);">
            <div style="display:flex; justify-content:space-between; align-items:center;">
                <span style="color: #a0a0a5;">Mode de réception :</span>
                <span style="font-weight: bold;">${escapeHtml(data.orderMode)}</span>
            </div>
            <div style="display:flex; justify-content:space-between; align-items:center;">
                <span style="color: #a0a0a5;">Destination :</span>
                <span>${destinationHtml}</span>
            </div>
            ${data.orderNotes ? `<div style="display:flex; justify-content:space-between; align-items:flex-start;">
                <span style="color: #a0a0a5;">Notes :</span>
                <span style="text-align:right; max-width: 60%;">${escapeHtml(data.orderNotes)}</span>
            </div>` : ''}
            <hr style="border: 0; border-top: 1px solid rgba(255,255,255,0.08); margin: 4px 0;">
            <div style="display:flex; justify-content:space-between; align-items:center;">
                <span style="font-weight: bold; font-size: 1rem;">Total à payer :</span>
                <span style="font-weight: bold; font-size: 1.2rem; color: var(--primary); font-family: var(--font-titles);">${data.grandTotal.toLocaleString('fr-FR')} FCFA</span>
            </div>
        </div>
    `;
}

function openRecapModal() {
    const modal = document.getElementById('orderRecapModal');
    if (modal) {
        modal.style.display = 'flex';
        document.body.style.overflow = 'hidden';
    }
}

function closeRecapModal() {
    const modal = document.getElementById('orderRecapModal');
    if (modal) {
        modal.style.display = 'none';
        document.body.style.overflow = '';
    }
}

/**
 * Étape 2 : Confirmer, enregistrer en BDD, puis rediriger vers WhatsApp
 */
async function confirmAndSubmitOrder() {
    if (!pendingOrderData) return;

    const confirmBtn = document.getElementById('recapConfirmBtn');
    if (confirmBtn) {
        confirmBtn.disabled = true;
        confirmBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Envoi en cours...';
    }

    const data = pendingOrderData;

    // ---- 1. Enregistrement en base de données via l'API AJAX ----
    let numeroCommande = null;
    try {
        const payload = {
            clientName: data.clientName,
            telephone: data.telephone,
            orderMode: data.orderMode,
            tableNumero: data.tableNumero,
            clientAddress: data.adresseLivraison,
            orderNotes: data.orderNotes,
            prixTotal: data.grandTotal,
            items: data.cart
        };

        const response = await fetch(DYNAMIC_URLROOT + '/api/commande/creer', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(payload)
        });

        const result = await response.json();

        if (result.success) {
            numeroCommande = result.numero_commande;
        } else {
            // Avertir mais ne pas bloquer — la commande WhatsApp doit quand même partir
            console.warn('Erreur BDD:', result.error);
            showToast('La commande a été envoyée sur WhatsApp, mais n\'a pas pu être enregistrée en base de données.', 'warning', 6000, 'Avertissement');
        }
    } catch (err) {
        console.error('Erreur réseau lors de l\'enregistrement:', err);
    }

    // ---- 2. Construire le message WhatsApp ----
    let msg = `*🔥 NOUVELLE COMMANDE — Chawarma Elite*\n`;
    if (numeroCommande) {
        msg += `*Référence : ${numeroCommande}*\n`;
    }
    msg += `\n*🧾 DÉTAIL DE LA COMMANDE :*\n`;
    msg += `--------------------------------------------------\n`;

    data.cart.forEach(item => {
        const itemTotal = item.prixUnitaire * item.qty;
        msg += `• *${item.qty}x ${item.nom}*\n`;
        if (item.options && item.options.length > 0) {
            item.options.forEach(o => {
                msg += `  └ _${o.nom} (${parseFloat(o.prix) > 0 ? `+${o.prix} F` : 'Gratuit'})_\n`;
            });
        }
        msg += `  _Prix unit. : ${item.prixUnitaire.toLocaleString('fr-FR')} F | Sous-total : ${itemTotal.toLocaleString('fr-FR')} F_\n\n`;
    });

    msg += `--------------------------------------------------\n`;
    msg += `*💰 TOTAL : ${data.grandTotal.toLocaleString('fr-FR')} FCFA*\n\n`;
    msg += `*👤 INFORMATIONS CLIENT :*\n`;
    msg += `--------------------------------------------------\n`;
    msg += `• *Nom :* ${data.clientName}\n`;
    if (data.telephone && data.telephone !== 'N/A') {
        msg += `• *Téléphone :* ${data.telephone}\n`;
    }
    msg += `• *Mode de réception :* ${data.orderMode}\n`;

    if (data.orderMode === 'Sur place') {
        msg += `• *Table :* ${data.tableNumero}\n`;
    } else if (data.orderMode === 'Livraison') {
        msg += `• *Adresse de livraison :* ${data.adresseLivraison}\n`;
    } else {
        msg += `• *Retrait :* Au comptoir\n`;
    }

    if (data.orderNotes) {
        msg += `• *Notes :* ${data.orderNotes}\n`;
    }

    msg += `--------------------------------------------------\n`;
    msg += `_Merci et à tout de suite en cuisine ! 🍳_`;

    // ---- 3. Ouvrir WhatsApp ----
    const phone = (typeof RECEIVING_WHATSAPP_PHONE !== 'undefined' && RECEIVING_WHATSAPP_PHONE)
        ? RECEIVING_WHATSAPP_PHONE
        : (typeof DEFAULT_PHONE !== 'undefined' ? DEFAULT_PHONE : '');

    const waUrl = `https://wa.me/${phone}?text=${encodeURIComponent(msg)}`;

    // ---- 4. Vider le panier ----
    localStorage.removeItem('chawarma_cart');
    sessionStorage.removeItem('chawarma_detected_table');
    updateCartBadge();

    // ---- 5. Fermer modale et rediriger ----
    closeRecapModal();
    window.open(waUrl, '_blank');

    showToast('Votre commande a été envoyée ! Vous allez être redirigé vers WhatsApp.', 'success', 3500, 'Commande envoyée 🎉');
    setTimeout(() => {
        window.location.href = (typeof DYNAMIC_URLROOT !== 'undefined' ? DYNAMIC_URLROOT : '') + '/';
    }, 3600);
}

/* =========================================================================
   15. PRÉ-OUVERTURE PRODUIT PAR URL (?product=[id])
   ========================================================================= */
function checkUrlForProduct() {
    const urlParams = new URLSearchParams(window.location.search);
    const productId = urlParams.get('product');

    if (!productId) return;

    const productCard = document.querySelector(`.product-card[data-id="${productId}"]`);
    if (productCard) {
        const btn = productCard.querySelector('button[onclick]');
        if (btn) {
            setTimeout(() => btn.click(), 400);
        }
    }
}
