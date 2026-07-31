<?php
/**
 * Vue - Administration - Liste des commandes
 */
require APPROOT . '/views/layout/admin_header.php';
?>

<!-- 1. BARRE DE FILTRES -->
<div class="form-card" style="background-color: var(--bg-card); padding: 24px; margin-bottom: 32px;">
    <form method="GET" action="<?php echo DYNAMIC_URLROOT; ?>/admin/commandes" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)) 120px; gap: 16px; align-items: flex-end;">
        
        <div class="form-group" style="margin-bottom: 0;">
            <label class="form-label" for="search">Recherche</label>
            <input type="text" id="search" name="search" class="form-control" placeholder="Nom, Tel, CMD-..." value="<?php echo htmlspecialchars($filters['search']); ?>">
        </div>

        <div class="form-group" style="margin-bottom: 0;">
            <label class="form-label" for="statut">Statut</label>
            <select id="statut" name="statut" class="form-control">
                <option value="">Tous les statuts</option>
                <?php
                $statuts = ['Nouvelle commande', 'Confirmée', 'En préparation', 'Prête', 'En livraison', 'Terminée', 'Annulée'];
                foreach ($statuts as $st) {
                    $selected = ($filters['statut'] === $st) ? 'selected' : '';
                    echo "<option value='".htmlspecialchars($st)."' $selected>".htmlspecialchars($st)."</option>";
                }
                ?>
            </select>
        </div>

        <div class="form-group" style="margin-bottom: 0;">
            <label class="form-label" for="mode_reception">Mode de réception</label>
            <select id="mode_reception" name="mode_reception" class="form-control">
                <option value="">Tous les modes</option>
                <option value="Sur place" <?php echo $filters['mode_reception'] === 'Sur place' ? 'selected' : ''; ?>>Sur place</option>
                <option value="A emporter" <?php echo $filters['mode_reception'] === 'A emporter' ? 'selected' : ''; ?>>À emporter</option>
                <option value="Livraison" <?php echo $filters['mode_reception'] === 'Livraison' ? 'selected' : ''; ?>>Livraison</option>
            </select>
        </div>

        <div class="form-group" style="margin-bottom: 0;">
            <label class="form-label" for="date_debut">Date début</label>
            <input type="date" id="date_debut" name="date_debut" class="form-control" value="<?php echo htmlspecialchars($filters['date_debut']); ?>">
        </div>

        <div class="form-group" style="margin-bottom: 0;">
            <label class="form-label" for="date_fin">Date fin</label>
            <input type="date" id="date_fin" name="date_fin" class="form-control" value="<?php echo htmlspecialchars($filters['date_fin']); ?>">
        </div>

        <button type="submit" class="btn btn-primary" style="padding: 14px; width: 100%;">
            <i class="fa-solid fa-filter"></i> Filtrer
        </button>
    </form>
</div>

<!-- 2. TABLEAU DES COMMANDES -->
<div class="table-card">
    <div class="table-responsive">
        <table class="admin-table">
            <thead>
                <tr>
                    <th style="width: 140px;">Réf / Date</th>
                    <th>Client</th>
                    <th>Mode &amp; Destination</th>
                    <th>Détail commande</th>
                    <th>Total</th>
                    <th style="width: 200px;">Statut</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($orders)): ?>
                    <tr>
                        <td colspan="6" style="text-align: center; color: #a0a0a5; padding: 40px;">
                            <i class="fa-solid fa-inbox" style="font-size: 2.5rem; margin-bottom: 12px; display: block; color: var(--primary);"></i>
                            Aucune commande ne correspond à ces critères.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($orders as $order): ?>
                        <tr class="order-row-<?php echo strtolower(str_replace(' ', '-', $order['statut'])); ?>">
                            <!-- Réf & Date -->
                            <td>
                                <strong style="color: var(--primary); font-family: var(--font-titles);"><?php echo htmlspecialchars($order['numero_commande']); ?></strong>
                                <div style="font-size: 0.8rem; color: #a0a0a5; margin-top: 4px;">
                                    <?php echo date('d/m/Y H:i', strtotime($order['date_creation'])); ?>
                                </div>
                            </td>

                            <!-- Client -->
                            <td>
                                <strong><?php echo htmlspecialchars($order['nom_client']); ?></strong>
                                <div style="font-size: 0.85rem; margin-top: 4px;">
                                    <a href="tel:<?php echo htmlspecialchars($order['telephone']); ?>" style="color: var(--secondary); text-decoration: none;">
                                        <i class="fa-solid fa-phone" style="font-size: 0.75rem; margin-right: 4px;"></i> <?php echo htmlspecialchars($order['telephone']); ?>
                                    </a>
                                </div>
                            </td>

                            <!-- Mode de réception & Destination -->
                            <td>
                                <?php
                                $mode = $order['mode_reception'];
                                $badgeClass = 'badge-popular'; // fallback
                                if ($mode === 'Sur place') $badgeClass = 'badge-new';
                                if ($mode === 'Livraison') $badgeClass = 'badge-inactive';
                                ?>
                                <span class="badge <?php echo $badgeClass; ?>"><?php echo htmlspecialchars($mode); ?></span>
                                
                                <div style="font-size: 0.85rem; margin-top: 8px; color: #e4e4e7;">
                                    <?php if ($mode === 'Sur place'): ?>
                                        <strong style="color: #2ec4b6;"><i class="fa-solid fa-chair"></i> Table <?php echo htmlspecialchars($order['table_numero'] ?? 'N/A'); ?></strong>
                                    <?php elseif ($mode === 'Livraison'): ?>
                                        <div style="line-height: 1.3;"><i class="fa-solid fa-truck"></i> <?php echo nl2br(htmlspecialchars($order['adresse_livraison'])); ?></div>
                                    <?php else: ?>
                                        <span><i class="fa-solid fa-shop"></i> Retrait comptoir</span>
                                    <?php endif; ?>
                                </div>
                            </td>

                            <!-- Articles commandés -->
                            <td>
                                <ul style="list-style: none; padding: 0; margin: 0; font-size: 0.85rem; line-height: 1.4;">
                                    <?php foreach ($order['items'] as $item): ?>
                                        <li style="margin-bottom: 6px;">
                                            <span style="color: var(--primary); font-weight: bold;"><?php echo (int)$item['quantite']; ?>x</span> 
                                            <strong><?php echo htmlspecialchars($item['nom_produit']); ?></strong>
                                            <?php if (!empty($item['options_texte'])): ?>
                                                <div style="font-size: 0.75rem; color: #a0a0a5; padding-left: 20px; font-style: italic;">
                                                    + <?php echo htmlspecialchars($item['options_texte']); ?>
                                                </div>
                                            <?php endif; ?>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                                <?php if (!empty($order['notes'])): ?>
                                    <div style="margin-top: 8px; padding: 6px 10px; background-color: rgba(255, 107, 8, 0.08); border-left: 3px solid var(--primary); font-size: 0.8rem; border-radius: 0 4px 4px 0;">
                                        <strong>Notes :</strong> <?php echo htmlspecialchars($order['notes']); ?>
                                    </div>
                                <?php endif; ?>
                            </td>

                            <!-- Total -->
                            <td>
                                <strong style="font-family: var(--font-titles); color: #fff; font-size: 1rem;">
                                    <?php echo number_format($order['prix_total'], 0, ',', ' '); ?> F
                                </strong>
                            </td>

                            <!-- Statut avec formulaire inline de mise à jour -->
                            <td>
                                <form method="POST" action="<?php echo DYNAMIC_URLROOT; ?>/admin/commandes/statut">
                                    <input type="hidden" name="csrf_token" value="<?php echo $csrfToken; ?>">
                                    <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                                    
                                    <?php
                                    // Déterminer la couleur de fond du select selon le statut pour repère visuel rapide
                                    $statusColors = [
                                        'Nouvelle commande' => '#ff6b08',
                                        'Confirmée' => '#3b82f6',
                                        'En préparation' => '#eab308',
                                        'Prête' => '#10b981',
                                        'En livraison' => '#8b5cf6',
                                        'Terminée' => '#71717a',
                                        'Annulée' => '#ef4444'
                                    ];
                                    $bgColor = $statusColors[$order['statut']] ?? '#ff6b08';
                                    ?>
                                    
                                    <select name="statut" class="form-control" onchange="this.form.submit()" style="background-color: <?php echo $bgColor; ?>; color: #fff; border-color: transparent; font-weight: bold; cursor: pointer; font-size: 0.85rem; padding: 8px 12px; border-radius: 6px;">
                                        <?php
                                        foreach ($statuts as $st) {
                                            $selected = ($order['statut'] === $st) ? 'selected' : '';
                                            echo "<option value='".htmlspecialchars($st)."' $selected style='background-color: var(--bg-card); color: #fff;'>".htmlspecialchars($st)."</option>";
                                        }
                                        ?>
                                    </select>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require APPROOT . '/views/layout/admin_footer.php'; ?>
