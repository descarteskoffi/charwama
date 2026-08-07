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
    <div>
        <h3 style="font-family: var(--font-titles); margin: 0; font-size: 1.5rem; display: flex; align-items: center; gap: 10px;">
            <i class="fa-solid fa-tags text-primary"></i> Gestion des Catégories
        </h3>
        <p style="color: var(--text-muted); font-size: 0.88rem; margin-top: 4px;">
            Organisez les rubriques du menu présentées à vos clients (<?php echo count($categories); ?> catégorie<?php echo count($categories) > 1 ? 's' : ''; ?>)
        </p>
    </div>
    
    <button type="button" class="btn btn-primary" id="openModalBtn" style="padding: 12px 24px; font-weight: 700; cursor: pointer;">
        <i class="fa-solid fa-circle-plus"></i> Ajouter une Catégorie
    </button>
</div>

<!-- TABLEAU DES CATÉGORIES (PLEINE LARGEUR) -->
<div class="table-card" style="margin-bottom: 40px; border-radius: var(--radius-lg); border: 1px solid var(--border-color); overflow: hidden; background: var(--bg-card);">
    <div class="table-responsive">
        <table class="admin-table">
            <thead>
                <tr>
                    <th style="width: 100px; text-align: center;">Ordre</th>
                    <th>Nom de la Catégorie</th>
                    <th style="width: 160px; text-align: center;">Statut</th>
                    <th style="width: 140px; text-align: center;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($categories)): ?>
                    <tr>
                        <td colspan="4" style="text-align: center; color: var(--text-muted); padding: 50px 20px;">
                            <i class="fa-solid fa-folder-open" style="font-size: 3rem; margin-bottom: 16px; color: var(--text-faint); display: block;"></i>
                            <p style="font-size: 1rem; margin: 0;">Aucune catégorie enregistrée.</p>
                            <span style="font-size: 0.85rem; color: var(--text-faint);">Cliquez sur le bouton ci-dessus pour ajouter votre première catégorie.</span>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($categories as $cat): ?>
                        <tr>
                            <td style="text-align: center;">
                                <span class="badge badge-inactive" style="font-weight: 700; font-size: 0.85rem; padding: 6px 12px; background: rgba(255,255,255,0.06); border-radius: var(--radius-sm);">
                                    #<?php echo $cat['ordre']; ?>
                                </span>
                            </td>
                            <td style="font-weight: 600; color: var(--text-primary); font-size: 1rem;">
                                <?php echo htmlspecialchars($cat['nom']); ?>
                            </td>
                            <td style="text-align: center;">
                                <?php if ($cat['statut'] == 1): ?>
                                    <span class="badge badge-new" style="display: inline-flex; align-items: center; gap: 6px; padding: 6px 14px; font-weight: 600;">
                                        <i class="fa-solid fa-circle" style="font-size: 0.45rem;"></i> Actif
                                    </span>
                                <?php else: ?>
                                    <span class="badge badge-inactive" style="background-color: rgba(239, 68, 68, 0.2); color: #ef4444; border: 1px solid rgba(239, 68, 68, 0.3); display: inline-flex; align-items: center; gap: 6px; padding: 6px 14px; font-weight: 600;">
                                        <i class="fa-solid fa-circle" style="font-size: 0.45rem;"></i> Inactif
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="admin-table-actions" style="justify-content: center; gap: 10px;">
                                    <a href="<?php echo DYNAMIC_URLROOT; ?>/admin/categories/modifier/<?php echo $cat['id']; ?>" 
                                       class="action-btn edit" 
                                       title="Modifier la catégorie"
                                       style="width: 36px; height: 36px;">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </a>
                                    <a href="<?php echo DYNAMIC_URLROOT; ?>/admin/categories/supprimer/<?php echo $cat['id']; ?>" 
                                       class="action-btn delete" 
                                       onclick="return confirmDeletion(event, 'la catégorie « <?php echo htmlspecialchars(addslashes($cat['nom'])); ?> »')" 
                                       title="Supprimer la catégorie"
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

<!-- MODALE DE CRÉATION DE CATÉGORIE -->
<div id="categoryModal" 
     class="modal" 
     role="dialog" 
     aria-labelledby="modalTitle" 
     aria-hidden="true"
     style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.85); backdrop-filter: blur(6px); -webkit-backdrop-filter: blur(6px); z-index: 10000; align-items: center; justify-content: center; padding: 20px;">
    
    <div class="modal-container" style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: var(--radius-lg); padding: 36px; width: 100%; max-width: 480px; position: relative; box-shadow: var(--shadow-lg); animation: modalFadeIn 0.25s ease-out;">
        
        <!-- Bouton de fermeture (X) -->
        <button type="button" 
                id="closeModalBtn" 
                class="modal-close"
                aria-label="Fermer"
                style="position: absolute; top: 20px; right: 20px; background: rgba(255,255,255,0.06); border: none; border-radius: 50%; width: 38px; height: 38px; display: flex; align-items: center; justify-content: center; font-size: 1.1rem; color: var(--text-muted); cursor: pointer; transition: var(--transition-fast);">
            <i class="fa-solid fa-xmark"></i>
        </button>
        
        <!-- Titre Modale -->
        <h3 id="modalTitle" style="margin-bottom: 24px; font-family: var(--font-titles); color: var(--text-primary); font-size: 1.4rem; display: flex; align-items: center; gap: 10px;">
            <i class="fa-solid fa-folder-plus text-primary"></i>
            Nouvelle Catégorie
        </h3>
        
        <!-- Formulaire de création -->
        <form id="categoryForm" action="<?php echo DYNAMIC_URLROOT; ?>/admin/categories/ajouter" method="POST">
            
            <input type="hidden" name="csrf_token" value="<?php echo $csrfToken; ?>">
            
            <!-- Nom de la catégorie -->
            <div class="form-group" style="margin-bottom: 20px;">
                <label class="form-label" for="categoryName" style="font-weight: 600; margin-bottom: 8px; display: block;">
                    Nom de la catégorie *
                </label>
                <input type="text" 
                       id="categoryName" 
                       name="nom" 
                       class="form-control" 
                       placeholder="Ex: Chawarmas, Paninis, Boissons..." 
                       required 
                       autocomplete="off"
                       style="padding: 12px 16px;">
                <small style="color: var(--text-faint); font-size: 0.75rem; display: block; margin-top: 6px;">
                    Nom lisible affiché dans le menu du site.
                </small>
            </div>
            
            <!-- Ordre d'affichage -->
            <div class="form-group" style="margin-bottom: 20px;">
                <label class="form-label" for="categoryOrder" style="font-weight: 600; margin-bottom: 8px; display: block;">
                    Ordre d'affichage *
                </label>
                <input type="number" 
                       id="categoryOrder" 
                       name="ordre" 
                       class="form-control" 
                       value="0" 
                       min="0" 
                       max="999" 
                       required
                       style="padding: 12px 16px;">
                <small style="color: var(--text-faint); font-size: 0.75rem; display: block; margin-top: 6px;">
                    Plus la valeur est petite, plus la catégorie apparaît haut (0-999).
                </small>
            </div>

            <!-- Statut -->
            <div class="form-group" style="margin-bottom: 28px;">
                <label style="display: flex; align-items: center; gap: 12px; cursor: pointer; user-select: none;">
                    <input type="checkbox" 
                           id="categoryStatut"
                           name="statut" 
                           value="1" 
                           checked 
                           style="width: 20px; height: 20px; cursor: pointer; accent-color: var(--primary);">
                    <span style="color: var(--text-body); font-weight: 500; font-size: 0.95rem;">Catégorie active (visible sur le site)</span>
                </label>
            </div>
            
            <!-- Boutons d'action -->
            <div style="display: flex; gap: 12px;">
                <button type="button" 
                        id="cancelModalBtn" 
                        class="btn btn-secondary" 
                        style="flex: 1; padding: 12px; font-weight: 600;">
                    Annuler
                </button>
                <button type="submit" 
                        id="submitCategoryBtn" 
                        class="btn btn-primary" 
                        style="flex: 1; padding: 12px; font-weight: 700;">
                    <i class="fa-solid fa-check"></i> Créer
                </button>
            </div>
        </form>
    </div>
</div>

<style>
/* Surcharges CSS pour garantir l'affichage de la modale d'administration */
#categoryModal.active,
#categoryModal[style*="display: flex"],
#categoryModal[style*="display:flex"] {
    opacity: 1 !important;
    pointer-events: auto !important;
}

@keyframes modalFadeIn {
    from { opacity: 0; transform: translateY(-15px); }
    to { opacity: 1; transform: translateY(0); }
}
#closeModalBtn:hover {
    background: rgba(255,255,255,0.12) !important;
    color: var(--text-primary) !important;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('categoryModal');
    const openBtn = document.getElementById('openModalBtn');
    const closeBtn = document.getElementById('closeModalBtn');
    const cancelBtn = document.getElementById('cancelModalBtn');
    const nameInput = document.getElementById('categoryName');
    const form = document.getElementById('categoryForm');

    function openModal(e) {
        if (e) e.preventDefault();
        if (!modal) return;
        modal.classList.add('active');
        modal.style.display = 'flex';
        modal.style.opacity = '1';
        modal.style.pointerEvents = 'auto';
        modal.setAttribute('aria-hidden', 'false');
        document.body.style.overflow = 'hidden';
        setTimeout(function() {
            if (nameInput) nameInput.focus();
        }, 100);
    }

    function closeModal(e) {
        if (e) e.preventDefault();
        if (!modal) return;
        modal.classList.remove('active');
        modal.style.display = 'none';
        modal.style.opacity = '0';
        modal.style.pointerEvents = 'none';
        modal.setAttribute('aria-hidden', 'true');
        document.body.style.overflow = '';
        if (form) form.reset();
    }

    if (openBtn) {
        openBtn.addEventListener('click', openModal);
    }
    if (closeBtn) {
        closeBtn.addEventListener('click', closeModal);
    }
    if (cancelBtn) {
        cancelBtn.addEventListener('click', closeModal);
    }
    
    if (modal) {
        modal.addEventListener('click', function(e) {
            if (e.target === modal) closeModal(e);
        });
    }

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && modal && (modal.classList.contains('active') || modal.style.display === 'flex')) {
            closeModal(e);
        }
    });
});
</script>

<?php require APPROOT . '/views/layout/admin_footer.php'; ?>
