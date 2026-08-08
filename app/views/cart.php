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
            <form id="whatsappOrderForm" class="cart-form" onsubmit="handleOrderFormSubmit(event)">
                <div class="form-group">
                    <label class="form-label" for="clientName">Votre Nom Complet *</label>
                    <input type="text" id="clientName" class="form-control" placeholder="Saisissez votre nom..." required>
                </div>

                <div class="form-group">
                    <label class="form-label" for="clientPhone">Votre Numéro de Téléphone *</label>
                    <input type="tel" id="clientPhone" class="form-control" placeholder="Ex: 97000000" required>
                </div>

                <div class="form-group">
                    <label class="form-label" for="orderMode">Mode de Réception *</label>
                    <select id="orderMode" class="form-control" onchange="toggleOrderModeFields()" required>
                        <option value="A emporter">À Emporter (Retrait au comptoir)</option>
                        <option value="Sur place">Manger sur Place</option>
                        <option value="Livraison">Livraison à domicile</option>
                    </select>
                </div>

                <!-- Sélection de la table pour Consommation sur Place -->
                <div class="form-group" id="tableSelectionGroup" style="display: none;">
                    <label class="form-label" for="tableNumero">Numéro de table *</label>
                    
                    <!-- Cas table détectée automatiquement -->
                    <div id="detectedTableInfo" style="display: none; padding: 12px; background-color: rgba(46, 196, 182, 0.15); border-left: 4px solid var(--secondary); color: var(--secondary); font-weight: bold; border-radius: 4px; margin-bottom: 12px;">
                        <i class="fa-solid fa-chair"></i> Votre commande sera servie à la table <span id="detectedTableNumSpan"></span>.
                    </div>
                    
                    <!-- Cas de sélection manuelle (accès direct sans QR code) -->
                    <select id="tableNumero" class="form-control">
                        <option value="">-- Sélectionnez votre table --</option>
                        <?php if (!empty($activeTables)): ?>
                            <?php foreach ($activeTables as $tab): ?>
                                <option value="<?php echo htmlspecialchars($tab['numero_table']); ?>">
                                    <?php echo htmlspecialchars($tab['nom_table']); ?>
                                </option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>

                <!-- Champ adresse affiché seulement en mode livraison -->
                <div class="form-group" id="deliveryAddressGroup" style="display: none;">
                    <label class="form-label" for="clientAddress">Adresse de Livraison complète *</label>
                    <textarea id="clientAddress" class="form-control" placeholder="Indiquez la rue, le quartier et éventuellement un repère visuel (ex: près de la pharmacie)..."></textarea>
                </div>

                <div class="form-group">
                    <label class="form-label" for="orderNotes">Instructions Spéciales (Sauces, Allergies...)</label>
                    <textarea id="orderNotes" class="form-control" placeholder="Ex: Sauce blanche et algérienne, sans oignons dans le panini, coca bien frais."></textarea>
                </div>

                <!-- Bouton soumis vers la modale de recap -->
                <button type="submit" class="btn btn-primary" id="cartSubmitBtn" style="width: 100%; display: flex; align-items: center; justify-content: center; background-color: #25d366; border-color: #25d366; box-shadow: 0 4px 14px rgba(37, 211, 102, 0.4);">
                    <i class="fa-brands fa-whatsapp" style="font-size: 1.25rem;"></i> Confirmer & Commander via WhatsApp
                </button>
            </form>
        </div>
    </div>
</section>

<!-- Modal Récapitulatif Final -->
<div id="orderRecapModal" class="order-recap-modal" role="dialog" aria-hidden="true" style="display: none; align-items: center; justify-content: center; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.85); z-index: 9999; opacity: 0; pointer-events: none; transition: opacity 0.25s ease;">
    <div class="form-card" style="background-color: var(--bg-card); width: 90%; max-width: 500px; padding: 32px; border-radius: 12px; border: 2px solid var(--primary); box-shadow: 0 10px 30px rgba(0,0,0,0.5);">
        <h3 style="margin-bottom: 20px; font-family: var(--font-titles); text-align: center; color: var(--primary);">
            <i class="fa-solid fa-file-invoice"></i> Récapitulatif de votre commande
        </h3>
        
        <div id="recapModalContent" style="font-size: 0.95rem; line-height: 1.6; margin-bottom: 24px; color: #e4e4e7;">
            <!-- Injecté dynamiquement par JS -->
        </div>

        <div style="display: flex; gap: 12px;">
            <button type="button" onclick="closeRecapModal()" class="btn btn-secondary" style="flex: 1; padding: 12px;">
                Modifier
            </button>
            <button type="button" id="recapConfirmBtn" onclick="confirmAndSubmitOrder()" class="btn btn-primary" style="flex: 1; padding: 12px; background-color: #25d366; border-color: #25d366; box-shadow: 0 4px 14px rgba(37, 211, 102, 0.4);">
                <i class="fa-solid fa-circle-check"></i> Valider &amp; Commander
            </button>
        </div>
    </div>
</div>

<!-- Passer le numéro WhatsApp admin configuré dans la DB au JS -->
<script>
    var RECEIVING_WHATSAPP_PHONE = "<?php echo preg_replace('/[^0-9]/', '', $siteSettings['whatsapp_phone'] ?? DEFAULT_PHONE); ?>";
    window.RECEIVING_WHATSAPP_PHONE = RECEIVING_WHATSAPP_PHONE;
</script>

<?php require APPROOT . '/views/layout/footer.php'; ?>
