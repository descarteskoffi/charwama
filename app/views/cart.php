<?php
/**
 * Vue - Panier de Commande (Cart)
 */
require APPROOT . '/views/layout/header.php';
?>

<section class="container cart-page">
    <div class="section-header">
        <span class="tag">Votre Commande</span>
        <h2>Récapitulatif du Panier</h2>
    </div>
    
    <!-- État : Panier Vide -->
    <div id="emptyCartState" class="cart-empty" style="display: none;">
        <i class="fa-solid fa-cart-shopping"></i>
        <h2>Votre panier est vide</h2>
        <p>Parcourez notre carte pour y ajouter des spécialités de chawarmas, paninis ou sandwichs !</p>
        <a href="<?php echo DYNAMIC_URLROOT; ?>/menu" class="btn btn-primary" style="margin-top: 16px;">
            <i class="fa-solid fa-utensils"></i> Découvrir le Menu
        </a>
    </div>

    <!-- État : Panier Rempli -->
    <div id="activeCartLayout" class="cart-layout" style="display: none;">
        <!-- Colonne Gauche : Liste des articles -->
        <div class="cart-items-wrapper" id="cartItemsList">
            <!-- Injecté dynamiquement par JavaScript -->
        </div>

        <!-- Colonne Droite : Formulaire & Total -->
        <div class="cart-summary">
            <h3>Résumé de la Commande</h3>
            
            <div class="summary-row">
                <span>Nombre d'articles :</span>
                <span id="summaryCount">0</span>
            </div>
            
            <div class="summary-row total">
                <span>Total :</span>
                <span class="price" id="summaryTotal">0 FCFA</span>
            </div>

            <!-- Formulaire de commande WhatsApp -->
            <form id="whatsappOrderForm" class="cart-form" onsubmit="sendWhatsAppOrder(event)">
                <div class="form-group">
                    <label class="form-label" for="clientName">Votre Nom Complet *</label>
                    <input type="text" id="clientName" class="form-control" placeholder="Saisissez votre nom..." required>
                </div>

                <div class="form-group">
                    <label class="form-label" for="orderMode">Mode de Réception *</label>
                    <select id="orderMode" class="form-control" onchange="toggleAddressField()" required>
                        <option value="A emporter">À Emporter (Sur place - Pickup)</option>
                        <option value="Sur place">Consommation sur Place</option>
                        <option value="Livraison">Livraison à domicile</option>
                    </select>
                </div>

                <!-- Champ adresse affiché seulement en mode livraison -->
                <div class="form-group" id="deliveryAddressGroup" style="display: none;">
                    <label class="form-label" for="clientAddress">Adresse de Livraison complète *</label>
                    <textarea id="clientAddress" class="form-control" placeholder="Indiquez la rue, le quartier et éventuellement un repère visuel..."></textarea>
                </div>

                <div class="form-group">
                    <label class="form-label" for="orderNotes">Instructions Spéciales (Sauces, Allergies...)</label>
                    <textarea id="orderNotes" class="form-control" placeholder="Ex: Sauce blanche et algérienne, sans oignons dans le panini, coca bien frais."></textarea>
                </div>

                <button type="submit" class="btn btn-primary" style="width: 100%; display: flex; align-items: center; justify-content: center; background-color: #25d366; border-color: #25d366; box-shadow: 0 4px 14px rgba(37, 211, 102, 0.4);">
                    <i class="fa-brands fa-whatsapp" style="font-size: 1.25rem;"></i> Confirmer & Commander via WhatsApp
                </button>
            </form>
        </div>
    </div>
</section>

<!-- Passer le numéro WhatsApp admin configuré dans la DB au JS -->
<script>
    const RECEIVING_WHATSAPP_PHONE = "<?php echo preg_replace('/[^0-9]/', '', $siteSettings['whatsapp_phone'] ?? DEFAULT_PHONE); ?>";
</script>

<?php require APPROOT . '/views/layout/footer.php'; ?>
