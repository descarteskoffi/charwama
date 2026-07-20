<?php
/**
 * Vue - Administration - Modifier une Catégorie
 */
require APPROOT . '/views/layout/admin_header.php';
?>

<div style="margin-bottom: 24px;">
    <a href="<?php echo DYNAMIC_URLROOT; ?>/admin/categories" style="color: var(--primary); font-family: var(--font-titles); font-weight: 600;">
        <i class="fa-solid fa-arrow-left"></i> Retour à la liste des catégories
    </a>
</div>

<div class="container" style="max-width: 600px; margin: 0 auto;">
    <div class="form-card">
        <h3>Modifier la Catégorie</h3>
        
        <?php if (!empty($error)): ?>
            <div class="alert alert-danger">
                <i class="fa-solid fa-triangle-exclamation"></i> <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <form action="<?php echo DYNAMIC_URLROOT; ?>/admin/categories/modifier/<?php echo $category['id']; ?>" method="POST">
            <!-- CSRF protection -->
            <input type="hidden" name="csrf_token" value="<?php echo $csrfToken; ?>">
            
            <div class="form-group">
                <label class="form-label" for="nom">Nom de la catégorie *</label>
                <input type="text" id="nom" name="nom" class="form-control" value="<?php echo htmlspecialchars($category['nom']); ?>" required>
            </div>
            
            <div class="form-group">
                <label class="form-label" for="ordre">Ordre de tri (affichage) *</label>
                <input type="number" id="ordre" name="ordre" class="form-control" value="<?php echo $category['ordre']; ?>" min="0" required>
            </div>

            <div class="form-group" style="display: flex; align-items: center; gap: 12px; margin-top: 24px;">
                <label class="option-item" for="statut" style="width: 100%; cursor: pointer;">
                    <div class="option-checkbox-wrapper">
                        <input type="checkbox" id="statut" name="statut" value="1" <?php echo $category['statut'] == 1 ? 'checked' : ''; ?> style="display: none;">
                        <!-- Custom styled checkbox -->
                        <div class="option-checkbox" style="<?php echo $category['statut'] == 1 ? 'background-color: var(--primary); border-color: var(--primary);' : 'background-color: transparent; border-color: var(--border-color);'; ?>">
                            <i class="fa-solid fa-check" style="<?php echo $category['statut'] == 1 ? 'opacity: 1;' : 'opacity: 0;'; ?>"></i>
                        </div>
                        <span>Catégorie active (visible sur le site)</span>
                    </div>
                </label>
            </div>
            
            <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 20px;">
                <i class="fa-solid fa-floppy-disk"></i> Enregistrer les modifications
            </button>
        </form>
    </div>
</div>

<script>
    // Toggle checkmark icon in custom checkbox
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
