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

<div class="cart-layout" style="grid-template-columns: 1.2fr 0.8fr; gap: 40px;">
    <!-- 1. TABLEAU DE LISTE -->
    <div class="table-card">
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

    <!-- 2. FORMULAIRE DE CRÉATION -->
    <div class="form-card">
        <h3>Créer une Catégorie</h3>
        
        <form action="<?php echo DYNAMIC_URLROOT; ?>/admin/categories/ajouter" method="POST">
            <!-- CSRF protection -->
            <input type="hidden" name="csrf_token" value="<?php echo $csrfToken; ?>">
            
            <div class="form-group">
                <label class="form-label" for="nom">Nom de la catégorie *</label>
                <input type="text" id="nom" name="nom" class="form-control" placeholder="Ex: Chawarmas, Boissons..." required>
            </div>
            
            <div class="form-group">
                <label class="form-label" for="ordre">Ordre de tri (affichage) *</label>
                <input type="number" id="ordre" name="ordre" class="form-control" value="0" min="0" required>
                <span style="font-size: 0.75rem; color: #71717a; margin-top: 4px; display: block;">Détermine l'ordre d'affichage sur le site (le plus petit apparaît en premier).</span>
            </div>

            <div class="form-group" style="display: flex; align-items: center; gap: 12px; margin-top: 24px;">
                <label class="option-item" for="statut" style="width: 100%; cursor: pointer;">
                    <div class="option-checkbox-wrapper">
                        <input type="checkbox" id="statut" name="statut" value="1" checked style="display: none;" onchange="this.nextElementSibling.classList.toggle('active')">
                        <!-- Custom styled checkbox -->
                        <div class="option-checkbox" style="background-color: var(--primary); border-color: var(--primary);">
                            <i class="fa-solid fa-check" style="opacity: 1;"></i>
                        </div>
                        <span>Catégorie active (visible sur le site)</span>
                    </div>
                </label>
            </div>
            
            <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 20px;">
                <i class="fa-solid fa-circle-plus"></i> Créer la catégorie
            </button>
        </form>
    </div>
</div>

<script>
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
