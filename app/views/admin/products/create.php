<?php
/**
 * Vue - Administration - Ajouter un Produit
 */
require APPROOT . '/views/layout/admin_header.php';
?>

<div style="margin-bottom: 24px;">
    <a href="<?php echo DYNAMIC_URLROOT; ?>/admin/produits" style="color: var(--primary); font-family: var(--font-titles); font-weight: 600;">
        <i class="fa-solid fa-arrow-left"></i> Retour à la liste des produits
    </a>
</div>

<div class="container" style="max-width: 800px; margin: 0 auto; margin-bottom: 60px;">
    <div class="form-card">
        <h3>Ajouter un Nouveau Produit</h3>
        
        <?php if (!empty($error)): ?>
            <div class="alert alert-danger">
                <i class="fa-solid fa-triangle-exclamation"></i> <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <!-- Formulaire multipart pour l'upload d'images -->
        <form action="<?php echo DYNAMIC_URLROOT; ?>/admin/produits/ajouter" method="POST" enctype="multipart/form-data">
            <!-- CSRF -->
            <input type="hidden" name="csrf_token" value="<?php echo $csrfToken; ?>">
            
            <div class="cart-layout" style="grid-template-columns: 1fr 1fr; gap: 32px; margin-bottom: 0;">
                <!-- Colonne Gauche : Données de base -->
                <div>
                    <div class="form-group">
                        <label class="form-label" for="nom">Nom du produit *</label>
                        <input type="text" id="nom" name="nom" class="form-control" placeholder="Ex: Chawarma Poulet Grand..." required>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="prix">Prix de base (FCFA) *</label>
                        <input type="number" id="prix" name="prix" class="form-control" placeholder="Ex: 2500" min="0" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="categorie_id">Catégorie *</label>
                        <select id="categorie_id" name="categorie_id" class="form-control" required>
                            <option value="">-- Sélectionner une catégorie --</option>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?php echo $cat['id']; ?>"><?php echo htmlspecialchars($cat['nom']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <!-- Colonne Droite : Image & Statut -->
                <div>
                    <div class="form-group">
                        <label class="form-label" for="image">Image du produit</label>
                        <input type="file" id="image" name="image" class="form-control" accept="image/*" onchange="previewImage(this)">
                        
                        <!-- Prévisualisation -->
                        <div class="image-upload-preview" id="imagePreviewContainer">
                            <i class="fa-solid fa-image" style="font-size: 2.5rem; color: #3f3f46;"></i>
                        </div>
                    </div>

                    <div class="form-group" style="margin-top: 24px;">
                        <label class="option-item" for="statut" style="cursor: pointer; width: 100%;">
                            <div class="option-checkbox-wrapper">
                                <input type="checkbox" id="statut" name="statut" value="1" checked style="display: none;">
                                <div class="option-checkbox" style="background-color: var(--primary); border-color: var(--primary);">
                                    <i class="fa-solid fa-check" style="opacity: 1;"></i>
                                </div>
                                <span>Produit actif (visible à la commande)</span>
                            </div>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Description -->
            <div class="form-group">
                <label class="form-label" for="description">Description / Ingrédients</label>
                <textarea id="description" name="description" class="form-control" placeholder="Garniture, accompagnements, sauces comprises par défaut..." style="min-height: 80px;"></textarea>
            </div>

            <!-- Zone de configuration des Suppléments / Options (Dynamique) -->
            <div class="dynamic-options-wrapper">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
                    <h4 style="font-family: var(--font-titles); font-size: 1rem;"><i class="fa-solid fa-sliders text-primary"></i> Options & Suppléments payants</h4>
                    <button type="button" class="btn btn-secondary btn-sm" onclick="addOptionRow()">
                        <i class="fa-solid fa-circle-plus"></i> Ajouter une option
                    </button>
                </div>
                
                <div id="optionsInputsContainer">
                    <!-- Les lignes d'options seront injectées ici en JS -->
                </div>
                
                <span style="font-size: 0.75rem; color: #71717a; display: block; margin-top: 8px;">
                    Exemple : Option "Taille XXL" avec prix "+1000", ou option "Supplément Fromage" avec prix "+200". Indiquez 0 pour une option gratuite (ex: "Sauce Algérienne").
                </span>
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 24px; padding: 16px;">
                <i class="fa-solid fa-floppy-disk"></i> Enregistrer le Produit
            </button>
        </form>
    </div>
</div>

<script>
    // Prévisualisation de l'image sélectionnée
    function previewImage(input) {
        const previewContainer = document.getElementById('imagePreviewContainer');
        previewContainer.innerHTML = '';
        
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            
            reader.onload = function(e) {
                const img = document.createElement('img');
                img.src = e.target.result;
                previewContainer.appendChild(img);
            }
            
            reader.readAsDataURL(input.files[0]);
        } else {
            previewContainer.innerHTML = '<i class="fa-solid fa-image" style="font-size: 2.5rem; color: #3f3f46;"></i>';
        }
    }

    // Gestion du statut checkbox personnalisé
    document.getElementById('statut').addEventListener('change', function() {
        const icon = this.nextElementSibling.querySelector('i');
        const div = this.nextElementSibling;
        if(this.checked) {
            div.style.backgroundColor = 'var(--primary)';
            div.style.borderColor = 'var(--primary)';
            icon.style.opacity = '1';
        } else {
            div.style.backgroundColor = 'transparent';
            div.style.borderColor = 'var(--border-color)';
            icon.style.opacity = '0';
        }
    });

    // Ajout dynamique de lignes d'options
    let optionIndex = 0;
    
    function addOptionRow() {
        const container = document.getElementById('optionsInputsContainer');
        const row = document.createElement('div');
        row.className = 'dynamic-option-row';
        row.id = `option_row_${optionIndex}`;
        
        row.innerHTML = `
            <input type="text" name="option_nom[]" class="form-control" placeholder="Nom de l'option (ex: Double fromage)" required>
            <input type="number" name="option_prix[]" class="form-control" placeholder="Supplément (F)" min="0" value="0" required>
            <button type="button" class="action-btn delete" onclick="removeOptionRow(${optionIndex})" title="Supprimer">
                <i class="fa-solid fa-trash-can"></i>
            </button>
        `;
        
        container.appendChild(row);
        optionIndex++;
    }

    function removeOptionRow(index) {
        const row = document.getElementById(`option_row_${index}`);
        if (row) {
            row.remove();
        }
    }
</script>
<?php require APPROOT . '/views/layout/admin_footer.php'; ?>
