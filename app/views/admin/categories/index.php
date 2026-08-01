<?php
/**
 * Vue - Administration - Liste des Catégories
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
    <h3 style="font-family: var(--font-titles); margin: 0;"><i class="fa-solid fa-tags text-primary" style="margin-right: 8px;"></i> Liste des Catégories</h3>
    
    <button type="button" class="btn btn-primary" onclick="openAddCategoryModal()">
        <i class="fa-solid fa-circle-plus"></i> Ajouter une Catégorie
    </button>
</div>

<!-- 1. TABLEAU DE LISTE -->
<div class="table-card" style="margin-bottom: 32px;">
    <div class="table-responsive">
        <table class="admin-table">
            <thead>
                <tr>
                    <th style="width: 80px;">Ordre</th>
                    <th>Nom de la Catégorie</th>
                    <th>Statut</th>
                    <th style="width: 120px;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($categories)): ?>
                    <tr>
                        <td colspan="4" style="text-align: center; color: #a0a0a5; padding: 40px 0;">
                            <i class="fa-solid fa-folder-open" style="font-size: 2.5rem; margin-bottom: 12px; color: #3f3f46;"></i>
                            <p>Aucune catégorie enregistrée.</p>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($categories as $cat): ?>
                        <tr>
                            <td>
                                <span class="badge badge-inactive" style="font-weight: bold;"><?php echo $cat['ordre']; ?></span>
                            </td>
                            <td style="font-weight: 600; color: #ffffff;">
                                <?php echo htmlspecialchars($cat['nom']); ?>
                            </td>
                            <td>
                                <?php if ($cat['statut'] == 1): ?>
                                    <span class="badge badge-new">Actif</span>
                                <?php else: ?>
                                    <span class="badge badge-inactive" style="background-color: var(--danger);">Inactif</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="admin-table-actions">
                                    <a href="<?php echo DYNAMIC_URLROOT; ?>/admin/categories/modifier/<?php echo $cat['id']; ?>" class="action-btn edit" title="Modifier">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </a>
                                    <a href="<?php echo DYNAMIC_URLROOT; ?>/admin/categories/supprimer/<?php echo $cat['id']; ?>" 
                                       class="action-btn delete" 
                                       onclick="return confirmDeletion(event, 'la catégorie « <?php echo htmlspecialchars(addslashes($cat['nom'])); ?> »')" 
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

<!-- 2. MODALE POPUP DE CRÉATION DE CATÉGORIE -->
<div id="addCategoryModal" class="modal" role="dialog" aria-hidden="true" style="display: none; align-items: center; justify-content: center; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.8); z-index: 9999;">
    <div class="form-card" style="background-color: var(--bg-card); width: 90%; max-width: 480px; padding: 32px; border-radius: 12px; position: relative;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
            <h3 style="font-family: var(--font-titles); margin: 0;"><i class="fa-solid fa-folder-plus text-primary" style="margin-right: 8px;"></i> Nouvelle Catégorie</h3>
            <button type="button" onclick="closeAddCategoryModal()" style="font-size: 1.2rem; color: #a0a0a5; cursor: pointer;">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
        
        <form action="<?php echo DYNAMIC_URLROOT; ?>/admin/categories/ajouter" method="POST">
            <!-- CSRF protection -->
            <input type="hidden" name="csrf_token" value="<?php echo $csrfToken; ?>">
            
            <div class="form-group">
                <label class="form-label" for="nom">Nom de la catégorie *</label>
                <input type="text" id="nom" name="nom" class="form-control" placeholder="Ex: Chawarmas, Paninis, Boissons..." required>
            </div>
            
            <div class="form-group">
                <label class="form-label" for="ordre">Ordre de tri (affichage) *</label>
                <input type="number" id="ordre" name="ordre" class="form-control" value="0" min="0" required>
                <span style="font-size: 0.75rem; color: #71717a; margin-top: 4px; display: block;">Détermine l'ordre d'affichage sur le site (le plus petit apparaît en premier).</span>
            </div>

            <div class="form-group" style="margin-top: 20px;">
                <label class="option-item" for="statut" style="width: 100%; cursor: pointer;">
                    <div class="option-checkbox-wrapper">
                        <input type="checkbox" id="statut" name="statut" value="1" checked style="display: none;" onchange="this.nextElementSibling.classList.toggle('active')">
                        <div class="option-checkbox" style="background-color: var(--primary); border-color: var(--primary);">
                            <i class="fa-solid fa-check" style="opacity: 1;"></i>
                        </div>
                        <span>Catégorie active (visible sur le site)</span>
                    </div>
                </label>
            </div>
            
            <div style="display: flex; gap: 12px; margin-top: 28px;">
                <button type="button" onclick="closeAddCategoryModal()" class="btn btn-secondary" style="flex: 1;">Annuler</button>
                <button type="submit" class="btn btn-primary" style="flex: 1;">
                    <i class="fa-solid fa-check"></i> Créer
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function openAddCategoryModal() {
        document.getElementById('addCategoryModal').style.display = 'flex';
    }

    function closeAddCategoryModal() {
        document.getElementById('addCategoryModal').style.display = 'none';
    }

    // Script d'assistance pour le checkbox personnalisé de création
    document.getElementById('statut').addEventListener('change', function() {
        const checkboxIcon = this.nextElementSibling.querySelector('i');
        const checkboxDiv = this.nextElementSibling;
        
        if(this.checked) {
            checkboxDiv.style.backgroundColor = 'var(--primary)';
            checkboxDiv.style.borderColor = 'var(--primary)';
            checkboxIcon.style.opacity = '1';
        } else {
            checkboxDiv.style.backgroundColor = 'transparent';
            checkboxDiv.style.borderColor = 'var(--border-color)';
            checkboxIcon.style.opacity = '0';
        }
    });
</script>

<?php require APPROOT . '/views/layout/admin_footer.php'; ?>
