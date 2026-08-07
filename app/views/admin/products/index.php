<?php
/**
 * Vue - Administration - Liste des Produits
 */
require APPROOT . '/views/layout/admin_header.php';

// Flash messages
$successMsg = $_SESSION['success_msg'] ?? null;
$errorMsg = $_SESSION['error_msg'] ?? null;
unset($_SESSION['success_msg'], $_SESSION['error_msg']);
?>

<!-- BANDEAUX D'ALERTE & NOTIFICATIONS -->
<?php if ($successMsg): ?>
    <div class="alert alert-success" style="padding: 16px 20px; background-color: rgba(16, 185, 129, 0.15); border-left: 4px solid #10b981; color: #10b981; border-radius: 12px; margin-bottom: 24px; display: flex; align-items: center; gap: 12px; font-weight: 600; box-shadow: var(--shadow-sm);">
        <i class="fa-solid fa-circle-check" style="font-size: 1.25rem;"></i>
        <span><?php echo htmlspecialchars($successMsg); ?></span>
    </div>
<?php endif; ?>

<?php if ($errorMsg): ?>
    <div class="alert alert-danger" style="padding: 16px 20px; background-color: rgba(239, 68, 68, 0.15); border-left: 4px solid #ef4444; color: #ef4444; border-radius: 12px; margin-bottom: 24px; display: flex; align-items: center; gap: 12px; font-weight: 600; box-shadow: var(--shadow-sm);">
        <i class="fa-solid fa-triangle-exclamation" style="font-size: 1.25rem;"></i>
        <span><?php echo htmlspecialchars($errorMsg); ?></span>
    </div>
<?php endif; ?>

<!-- BARRE D'ACTION SUPÉRIEURE -->
<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 28px; gap: 16px; flex-wrap: wrap;">
    <div class="search-bar" style="max-width: 360px; margin-bottom: 0;">
        <i class="fa-solid fa-magnifying-glass"></i>
        <input type="text" id="adminProductSearch" placeholder="Rechercher un produit par nom..." onkeyup="filterAdminProducts()">
    </div>
    
    <a href="<?php echo DYNAMIC_URLROOT; ?>/admin/produits/ajouter" class="btn btn-primary" style="padding: 12px 24px; font-weight: 700;">
        <i class="fa-solid fa-circle-plus"></i> Ajouter un Produit
    </a>
</div>

<!-- TABLEAU DES PRODUITS (PLEINE LARGEUR) -->
<div class="table-card" style="margin-bottom: 40px; border-radius: var(--radius-lg); border: 1px solid var(--border-color); overflow: hidden; background: var(--bg-card);">
    <div class="table-responsive">
        <table class="admin-table" id="adminProductsTable">
            <thead>
                <tr>
                    <th style="width: 80px; text-align: center;">Visuel</th>
                    <th>Nom & Description du Produit</th>
                    <th style="width: 140px;">Catégorie</th>
                    <th style="width: 120px;">Prix</th>
                    <th style="width: 130px; text-align: center;">Statut</th>
                    <th style="width: 120px; text-align: center;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($products)): ?>
                    <tr>
                        <td colspan="6" style="text-align: center; color: var(--text-muted); padding: 60px 20px;">
                            <i class="fa-solid fa-hamburger" style="font-size: 3rem; margin-bottom: 16px; color: var(--text-faint); display: block;"></i>
                            <p style="font-size: 1rem; margin: 0;">Aucun produit dans le catalogue.</p>
                            <a href="<?php echo DYNAMIC_URLROOT; ?>/admin/produits/ajouter" class="btn btn-primary btn-sm" style="margin-top: 16px;">Créer le premier produit</a>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($products as $prod): ?>
                        <tr class="admin-product-row" data-name="<?php echo htmlspecialchars(strtolower($prod['nom'])); ?>">
                            <td style="text-align: center;">
                                <?php if ($prod['image']): ?>
                                    <img src="<?php echo DYNAMIC_URLROOT; ?>/uploads/<?php echo htmlspecialchars($prod['image']); ?>" alt="Visuel" class="admin-table-img" onerror="this.src='https://images.unsplash.com/photo-1561651823-34fed022540d?w=100&auto=format&fit=crop&q=60';">
                                <?php else: ?>
                                    <img src="https://images.unsplash.com/photo-1561651823-34fed022540d?w=100&auto=format&fit=crop&q=60" alt="Défaut" class="admin-table-img">
                                <?php endif; ?>
                            </td>
                            <td>
                                <div style="font-weight: 600; color: var(--text-primary); font-size: 0.88rem; margin-bottom: 3px;">
                                    <?php echo htmlspecialchars($prod['nom']); ?>
                                </div>
                                <span style="font-size: 0.75rem; color: var(--text-muted); display: -webkit-box; -webkit-line-clamp: 1; -webkit-box-orient: vertical; overflow: hidden; max-width: 450px; line-height: 1.4;">
                                    <?php echo !empty($prod['description']) ? htmlspecialchars($prod['description']) : '<i style="opacity: 0.5;">Aucune description</i>'; ?>
                                </span>
                            </td>
                            <td>
                                <span class="badge badge-inactive" style="font-weight: 600;">
                                    <?php echo htmlspecialchars($prod['categorie_nom']); ?>
                                </span>
                            </td>
                            <td style="font-family: var(--font-titles); font-weight: 700; color: var(--text-primary); font-size: 0.9rem;">
                                <?php echo number_format($prod['prix'], 0, ',', ' '); ?> F
                            </td>
                            <td style="text-align: center;">
                                <?php if ($prod['statut'] == 1): ?>
                                    <span class="badge badge-new" style="display: inline-flex; align-items: center; gap: 5px; padding: 4px 10px; font-weight: 600; font-size: 0.75rem;">
                                        <i class="fa-solid fa-circle" style="font-size: 0.4rem;"></i> Disponible
                                    </span>
                                <?php else: ?>
                                    <span class="badge badge-popular" style="background-color: rgba(239, 68, 68, 0.2); color: #ef4444; border: 1px solid rgba(239, 68, 68, 0.3); display: inline-flex; align-items: center; gap: 5px; padding: 4px 10px; font-weight: 600; font-size: 0.75rem;">
                                        <i class="fa-solid fa-circle" style="font-size: 0.4rem;"></i> Rupture
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="admin-table-actions" style="justify-content: center; gap: 10px;">
                                    <a href="<?php echo DYNAMIC_URLROOT; ?>/admin/produits/modifier/<?php echo $prod['id']; ?>" 
                                       class="action-btn edit" 
                                       title="Modifier"
                                       style="width: 36px; height: 36px;">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </a>
                                    <a href="<?php echo DYNAMIC_URLROOT; ?>/admin/produits/supprimer/<?php echo $prod['id']; ?>" 
                                       class="action-btn delete" 
                                       onclick="return confirmDeletion(event, 'le produit « <?php echo htmlspecialchars(addslashes($prod['nom'])); ?> »')" 
                                       title="Supprimer"
                                       style="width: 36px; height: 36px;">
                                        <i class="fa-solid fa-trash-can"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
    function filterAdminProducts() {
        const query = document.getElementById('adminProductSearch').value.toLowerCase().trim();
        const rows = document.querySelectorAll('.admin-product-row');
        
        rows.forEach(row => {
            const productName = row.getAttribute('data-name');
            if (productName.includes(query)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    }
</script>

<?php require APPROOT . '/views/layout/admin_footer.php'; ?>
