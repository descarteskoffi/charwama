<?php
/**
 * Vue - Administration - Tableau de Bord
 */
require APPROOT . '/views/layout/admin_header.php';
?>

<!-- 1. CARTES DE STATISTIQUES -->
<div class="stats-grid">
    <!-- Stat 1 : Catégories -->
    <div class="stat-card">
        <div class="stat-card-details">
            <h4>Catégories</h4>
            <div class="stat-card-val"><?php echo $catCount; ?></div>
        </div>
        <div class="stat-card-icon secondary">
            <i class="fa-solid fa-tags"></i>
        </div>
    </div>
    
    <!-- Stat 2 : Produits -->
    <div class="stat-card">
        <div class="stat-card-details">
            <h4>Produits actifs</h4>
            <div class="stat-card-val"><?php echo $prodCount; ?></div>
        </div>
        <div class="stat-card-icon">
            <i class="fa-solid fa-hamburger"></i>
        </div>
    </div>

    <!-- Stat 3 : Statut Serveur -->
    <div class="stat-card">
        <div class="stat-card-details">
            <h4>Hébergement</h4>
            <div class="stat-card-val" style="font-size: 1.25rem; font-family: var(--font-body); margin-top: 12px; color: var(--secondary);">Alwaysdata</div>
        </div>
        <div class="stat-card-icon" style="background-color: rgba(46, 196, 182, 0.15); color: var(--secondary);">
            <i class="fa-solid fa-server"></i>
        </div>
    </div>
</div>

<!-- 2. SECTION ACTIONS RAPIDES & JOURNAL DE SÉCURITÉ -->
<div class="cart-layout" style="grid-template-columns: 1.1fr 0.9fr; gap: 32px;">
    <!-- Raccourcis d'administration -->
    <div>
        <h3 style="margin-bottom: 20px; font-family: var(--font-titles);"><i class="fa-solid fa-bolt text-primary" style="margin-right: 8px;"></i> Actions Rapides</h3>
        <div class="form-card" style="background-color: var(--bg-card); display: flex; flex-direction: column; gap: 16px; padding: 32px;">
            <a href="<?php echo DYNAMIC_URLROOT; ?>/admin/produits/ajouter" class="btn btn-primary" style="justify-content: flex-start; padding: 16px;">
                <i class="fa-solid fa-circle-plus"></i> Ajouter un produit au catalogue
            </a>
            <a href="<?php echo DYNAMIC_URLROOT; ?>/admin/categories" class="btn btn-secondary" style="justify-content: flex-start; padding: 16px;">
                <i class="fa-solid fa-plus-minus"></i> Organiser les catégories
            </a>
            <a href="<?php echo DYNAMIC_URLROOT; ?>/admin/parametres" class="btn btn-secondary" style="justify-content: flex-start; padding: 16px; border-color: rgba(255,255,255,0.05);">
                <i class="fa-solid fa-phone-volume"></i> Modifier le numéro WhatsApp récepteur
            </a>
        </div>
    </div>

    <!-- Sécurité : Tentatives de connexion -->
    <div>
        <h3 style="margin-bottom: 20px; font-family: var(--font-titles);"><i class="fa-solid fa-shield-halved" style="color: var(--secondary); margin-right: 8px;"></i> Journal des accès (Sécurité)</h3>
        <div class="table-card">
            <div class="table-responsive">
                <table class="admin-table" style="font-size: 0.85rem;">
                    <thead>
                        <tr>
                            <th>Date / Heure</th>
                            <th>Adresse IP</th>
                            <th>Résultat</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($recentLogs)): ?>
                            <tr>
                                <td colspan="3" style="text-align: center; color:#a0a0a5; padding: 20px;">Aucun historique disponible.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($recentLogs as $log): ?>
                                <tr>
                                    <td><?php echo date('d/m/Y H:i:s', strtotime($log['date_heure'])); ?></td>
                                    <td><code><?php echo htmlspecialchars($log['ip']); ?></code></td>
                                    <td>
                                        <?php if ($log['statut'] === 'succes'): ?>
                                            <span class="badge badge-new" style="font-size: 0.65rem; padding: 2px 6px;">Succès</span>
                                        <?php elseif ($log['statut'] === 'echec_mdp'): ?>
                                            <span class="badge badge-popular" style="font-size: 0.65rem; padding: 2px 6px;">Échec</span>
                                        <?php else: ?>
                                            <span class="badge badge-inactive" style="font-size: 0.65rem; padding: 2px 6px;"><?php echo htmlspecialchars($log['statut']); ?></span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php require APPROOT . '/views/layout/admin_footer.php'; ?>
