/**
 * main.js — Script Principal Chawarma Premium
 * Phase 4 : Version enrichie avec Toast, Scroll Reveal, Lazy Loading, Page Loader
 * 
 * Modules :
 *   1. Page Loader
 *   2. Menu mobile
 *   3. Scroll Progress Bar + Navbar scroll effect
 *   4. Scroll Reveal (IntersectionObserver)
 *   5. Lazy Loading des images
 *   6. Toast Notifications
 *   7. Gestion du Panier (localStorage)
 *   8. Filtrage et Recherche (page catalogue)
 *   9. Modal Produit
 *  10. Page Panier & Construction de la commande
 *  11. Redirection WhatsApp
 *  12. URL Product Pre-selection
 */

document.addEventListener('DOMContentLoaded', () => {
    // --- Initialisation ---
    initPageLoader();
    initMobileNav();
    initTheme();
    initScrollProgress();
    initScrollReveal();
    initLazyImages();
    updateCartBadge();

    if (document.getElementById('cartItemsList')) {
        renderCartPage();
    }

    checkUrlForProduct();
});

/* =========================================================================
   1. PAGE LOADER
   ========================================================================= */
function initPageLoader() {
    const loader = document.getElementById('pageLoader');
    if (!loader) return;

    // Cacher le loader après le chargement complet
    window.addEventListener('load', () => {
        setTimeout(() => {
            loader.classList.add('hidden');
        }, 300);
    });

    // Fallback : cacher après 2 secondes quoi qu'il arrive
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

    // Fermer le menu si on clique en dehors
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

    // Lire le thème actuel (déjà appliqué par le script inline du <head>)
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

    // Initialiser l'état du bouton selon le thème déjà actif
    const currentTheme = document.documentElement.getAttribute('data-theme') || 'dark';
    applyTheme(currentTheme);

    // Écouter le clic
    btn.addEventListener('click', () => {
        const current = document.documentElement.getAttribute('data-theme');
        const next = current === 'light' ? 'dark' : 'light';
        applyTheme(next);

        // Petit toast de confirmation
        const label = next === 'light' ? '☀️ Mode clair activé' : '🌙 Mode sombre activé';
        showToast(label, 'info', 2000, 'Thème modifié');
    });
}

/* =========================================================================
   3. SCROLL PROGRESS BAR + NAVBAR EFFECT
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
    onScroll(); // Initialiser à l'état actuel
}

/* =========================================================================
   4. SCROLL REVEAL (IntersectionObserver)
   ========================================================================= */
function initScrollReveal() {
    const elements = document.querySelectorAll('[data-reveal]');
    if (elements.length === 0) return;

    const observer = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
            if (entry.isIntersecting) {
                entry.target.classList.add('is-revealed');
                observer.unobserve(entry.target); // Jouer une seule fois
            }
        });
    }, {
        threshold: 0.12,
        rootMargin: '0px 0px -60px 0px'
    });

    elements.forEach((el) => observer.observe(el));
}

/* =========================================================================
   5. LAZY LOADING DES IMAGES
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
        // Fallback pour anciens navigateurs
        lazyImgs.forEach((img) => {
            img.src = img.getAttribute('data-src');
            img.removeAttribute('data-src');
        });
    }
}

/* =========================================================================
   6. TOAST NOTIFICATIONS (Remplace les alert() natifs)
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

/**
 * Affiche un toast de notification.
 * @param {string} message - Le message principal
 * @param {string} type    - 'success' | 'error' | 'warning' | 'info'
 * @param {number} duration - Durée en ms (défaut: 4000)
 * @param {string} title    - Titre optionnel (override le titre par défaut)
 */
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

    // Disparaître automatiquement
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
   7. GESTION DU PANIER (LOCAL STORAGE)
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
        // Force reflow pour relancer l'animation
        void badge.offsetWidth;
        badge.classList.add('bounce');
    }
}

function addToCart(id, name, basePrice, image, qty, options = []) {
    const cart = getCart();

    // Clé unique pour différencier les articles avec options différentes
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
   8. FILTRAGE ET RECHERCHE (PAGE CATALOGUE)
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
   9. MODAL PRODUIT
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

    // Construire les options
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
    document.body.style.overflow = 'hidden'; // Empêcher le scroll derrière la modal
}

function closeProductModal() {
    document.getElementById('productModal').classList.remove('active');
    document.body.style.overflow = '';
}

// Fermer la modal sur Escape
document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') closeProductModal();
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

    // Récupérer le prix de base en nettoyant le texte
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

    // Toast succès plutôt qu'un alert natif
    showToast(`"${name}" a été ajouté à votre commande !`, 'success', 4000, 'Ajouté au panier');
}

/* =========================================================================
   10. PAGE PANIER ET RENDU
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

    // Mettre à jour le résumé
    const totalQty = cart.reduce((sum, item) => sum + item.qty, 0);
    document.getElementById('summaryCount').innerText = totalQty;
    document.getElementById('summaryTotal').innerText = `${grandTotal.toLocaleString('fr-FR')} FCFA`;

    // Réappliquer scroll reveal sur les nouveaux éléments
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

function toggleAddressField() {
    const orderMode = document.getElementById('orderMode').value;
    const addressGroup = document.getElementById('deliveryAddressGroup');
    const addressInput = document.getElementById('clientAddress');

    if (orderMode === 'Livraison') {
        addressGroup.style.display = 'block';
        addressInput.setAttribute('required', 'required');
    } else {
        addressGroup.style.display = 'none';
        addressInput.removeAttribute('required');
        addressInput.value = '';
    }
}

/* =========================================================================
   11. REDIRECTION ET ENVOI SUR WHATSAPP
   ========================================================================= */
function sendWhatsAppOrder(event) {
    event.preventDefault();

    const clientName = document.getElementById('clientName').value.trim();
    const orderMode = document.getElementById('orderMode').value;
    const clientAddress = document.getElementById('clientAddress')
        ? document.getElementById('clientAddress').value.trim()
        : '';
    const orderNotes = document.getElementById('orderNotes').value.trim();
    const cart = getCart();

    // Validations avec Toast
    if (cart.length === 0) {
        showToast('Votre panier est vide. Ajoutez des articles avant de commander.', 'error', 4000, 'Panier vide');
        return;
    }
    if (clientName === '') {
        showToast('Veuillez saisir votre nom pour la commande.', 'warning', 4000, 'Champ requis');
        document.getElementById('clientName').focus();
        return;
    }
    if (orderMode === 'Livraison' && clientAddress === '') {
        showToast('Veuillez saisir votre adresse de livraison.', 'warning', 4000, 'Champ requis');
        document.getElementById('clientAddress').focus();
        return;
    }

    // Construire le message WhatsApp
    let msg = `*Bonjour Chawarma Premium, je souhaite passer une commande :*\n\n`;
    msg += `*🧾 RÉCAPITULATIF DE LA COMMANDE :*\n`;
    msg += `--------------------------------------------------\n`;

    let grandTotal = 0;
    cart.forEach(item => {
        const itemTotal = item.prixUnitaire * item.qty;
        grandTotal += itemTotal;
        msg += `• *${item.qty}x ${item.nom}*\n`;
        if (item.options && item.options.length > 0) {
            item.options.forEach(o => {
                msg += `  └ _${o.nom} (${parseFloat(o.prix) > 0 ? `+${o.prix} F` : 'Gratuit'})_\n`;
            });
        }
        msg += `  _Prix : ${item.prixUnitaire.toLocaleString('fr-FR')} F | Sous-total : ${itemTotal.toLocaleString('fr-FR')} F_\n\n`;
    });

    msg += `--------------------------------------------------\n`;
    msg += `*💰 TOTAL COMMANDE : ${grandTotal.toLocaleString('fr-FR')} FCFA*\n\n`;
    msg += `*👤 INFORMATIONS CLIENT :*\n`;
    msg += `--------------------------------------------------\n`;
    msg += `• *Nom du Client :* ${clientName}\n`;
    msg += `• *Mode de retrait :* ${orderMode}\n`;

    if (orderMode === 'Livraison') {
        msg += `• *Adresse de livraison :* ${clientAddress}\n`;
    }
    if (orderNotes !== '') {
        msg += `• *Notes spéciales :* ${orderNotes}\n`;
    }

    msg += `--------------------------------------------------\n`;
    msg += `_Merci et à tout de suite en cuisine !_ 🍳`;

    // Numéro WhatsApp (déclaré dans le footer PHP)
    const phone = (typeof RECEIVING_WHATSAPP_PHONE !== 'undefined' && RECEIVING_WHATSAPP_PHONE)
        ? RECEIVING_WHATSAPP_PHONE
        : (typeof DEFAULT_PHONE !== 'undefined' ? DEFAULT_PHONE : '');

    const waUrl = `https://wa.me/${phone}?text=${encodeURIComponent(msg)}`;

    // Vider le panier
    localStorage.removeItem('chawarma_cart');
    updateCartBadge();

    // Ouvrir WhatsApp
    window.open(waUrl, '_blank');

    // Toast + redirection
    showToast('Votre commande a été compilée ! Vous allez être redirigé vers WhatsApp.', 'success', 3500, 'Commande envoyée 🎉');
    setTimeout(() => {
        window.location.href = (typeof DYNAMIC_URLROOT !== 'undefined' ? DYNAMIC_URLROOT : '') + '/';
    }, 3600);
}

/* =========================================================================
   12. PRÉ-OUVERTURE PRODUIT PAR URL (?product=[id])
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
