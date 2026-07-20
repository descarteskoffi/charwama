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

<?php if ($successMsg): ?>
    <div class="alert alert-success">
        <i class="fa-solid fa-circle-check"></i> <?php echo htmlspecialchars($successMsg); ?>
    </div>
<?php endif; ?>

<?php if ($errorMsg): ?>
    <div class="alert alert-danger">
        <i class="fa-solid fa-triangle-exclamation"></i> <?php echo htmlspecialchars($errorMsg); ?>
    </div>
<?php endif; ?>

<!-- Barre d'action supérieure -->
<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; gap: 16px; flex-wrap: wrap;">
    <div class="search-bar" style="max-width: 320px; margin-bottom: 0;">
        <i class="fa-solid fa-magnifying-glass"></i>
        <input type="text" id="adminProductSearch" placeholder="Filtrer par nom..." onkeyup="filterAdminProducts()">
    </div>
    
    <a href="<?php echo DYNAMIC_URLROOT; ?>/admin/produits/ajouter" class="btn btn-primary">
        <i class="fa-solid fa-circle-plus"></i> Ajouter un Produit
    </a>
</div>

<!-- Tableau des produits -->
<div class="table-card">
    <div class="table-responsive">
        <table class="admin-table" id="adminProductsTable">
            <thead>
                <tr>
                    <th style="width: 80px;">Visuel</th>
                    <th>Nom du Produit</th>
                    <th>Catégorie</th>
                    <th>Prix</th>
                    <th>Statut</th>
                    <th style="width: 120px;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($products)): ?>
                    <tr>
                        <td colspan="6" style="text-align: center; color: #a0a0a5; padding: 60px 0;">
                            <i class="fa-solid fa-hamburger" style="font-size: 3rem; margin-bottom: 12px; color: #3f3f46;"></i>
                            <p>Aucun produit dans le catalogue.</p>
                            <a href="<?php echo DYNAMIC_URLROOT; ?>/admin/produits/ajouter" class="btn btn-primary btn-sm" style="margin-top: 16px;">Créer le premier produit</a>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($products as $prod): ?>
                        <tr class="admin-product-row" data-name="<?php echo htmlspecialchars(strtolower($prod['nom'])); ?>">
                            <td>
                                <?php if ($prod['image']): ?>
                                    <img src="<?php echo DYNAMIC_URLROOT; ?>/uploads/<?php echo htmlspecialchars($prod['image']); ?>" alt="Visuel" class="admin-table-img" onerror="this.src='https://images.unsplash.com/photo-1561651823-34fed022540d?w=100&auto=format&fit=crop&q=60';">
                                <?php else: ?>
                                    <img src="https://images.unsplash.com/photo-1561651823-34fed022540d?w=100&auto=format&fit=crop&q=60" alt="Défaut" class="admin-table-img">
                                <?php endif; ?>
                            </td>
                            <td>
                                <div style="font-weight: 600; color: #ffffff;"><?php echo htmlspecialchars($prod['nom']); ?></div>
                                <span style="font-size: 0.75rem; color: #a0a0a5; display: -webkit-box; -webkit-line-clamp: 1; -webkit-box-orient: vertical; overflow: hidden; max-width: 350px;">
                                    <?php echo htmlspecialchars($prod['description']); ?>
                                </span>
                            </td>
                            <td>
                                <span class="badge badge-inactive"><?php echo htmlspecialchars($prod['categorie_nom']); ?></span>
                            </td>
                            <td style="font-family: var(--font-titles); font-weight: 700; color: #ffffff;">
                                <?php echo number_format($prod['prix'], 0, ',', ' '); ?> F
                            </td>
                            <td>
                                <?php if ($prod['statut'] == 1): ?>
                                    <span class="badge badge-new">Disponible</span>
                                <?php else: ?>
                                    <span class="badge badge-popular" style="background-color: var(--danger);">Rupture</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="admin-table-actions">
                                    <a href="<?php echo DYNAMIC_URLROOT; ?>/admin/produits/modifier/<?php echo $prod['id']; ?>" class="action-btn edit" title="Modifier">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </a>
                                    <a href="<?php echo DYNAMIC_URLROOT; ?>/admin/produits/supprimer/<?php echo $prod['id']; ?>" 
                                       class="action-btn delete" 
                                       onclick="return confirmDeletion(event, 'le produit « <?php echo htmlspecialchars(addslashes($prod['nom'])); ?> »')" 
                                       title="Supprimer">
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
    // Filtrage des produits côté client dans le tableau d'administration
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
