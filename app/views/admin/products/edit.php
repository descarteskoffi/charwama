<?php
/**
 * Vue - Administration - Modifier un Produit
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
        <h3>Modifier le Produit : <?php echo htmlspecialchars($product['nom']); ?></h3>
        
        <?php if (!empty($error)): ?>
            <div class="alert alert-danger">
                <i class="fa-solid fa-triangle-exclamation"></i> <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <form action="<?php echo DYNAMIC_URLROOT; ?>/admin/produits/modifier/<?php echo $product['id']; ?>" method="POST" enctype="multipart/form-data">
            <!-- CSRF -->
            <input type="hidden" name="csrf_token" value="<?php echo $csrfToken; ?>">
            
            <div class="cart-layout" style="grid-template-columns: 1fr 1fr; gap: 32px; margin-bottom: 0;">
                <!-- Colonne Gauche : Données de base -->
                <div>
                    <div class="form-group">
                        <label class="form-label" for="nom">Nom du produit *</label>
                        <input type="text" id="nom" name="nom" class="form-control" value="<?php echo htmlspecialchars($product['nom']); ?>" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="prix">Prix de base (FCFA) *</label>
                        <input type="number" id="prix" name="prix" class="form-control" value="<?php echo (int)$product['prix']; ?>" min="0" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="categorie_id">Catégorie *</label>
                        <select id="categorie_id" name="categorie_id" class="form-control" required>
                            <option value="">-- Sélectionner une catégorie --</option>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?php echo $cat['id']; ?>" <?php echo $product['categorie_id'] == $cat['id'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($cat['nom']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <!-- Colonne Droite : Image & Statut -->
                <div>
                    <div class="form-group">
                        <label class="form-label" for="image">Image du produit (laisser vide pour conserver)</label>
                        <input type="file" id="image" name="image" class="form-control" accept="image/*" onchange="previewImage(this)">
                        
                        <!-- Prévisualisation -->
                        <div class="image-upload-preview" id="imagePreviewContainer">
                            <?php if ($product['image']): ?>
                                <img src="<?php echo DYNAMIC_URLROOT; ?>/uploads/<?php echo htmlspecialchars($product['image']); ?>" alt="Produit">
                            <?php else: ?>
                                <i class="fa-solid fa-image" style="font-size: 2.5rem; color: #3f3f46;"></i>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="form-group" style="margin-top: 24px;">
                        <label class="option-item" for="statut" style="cursor: pointer; width: 100%;">
                            <div class="option-checkbox-wrapper">
                                <input type="checkbox" id="statut" name="statut" value="1" <?php echo $product['statut'] == 1 ? 'checked' : ''; ?> style="display: none;">
                                <div class="option-checkbox" style="<?php echo $product['statut'] == 1 ? 'background-color: var(--primary); border-color: var(--primary);' : 'background-color: transparent; border-color: var(--border-color);'; ?>">
                                    <i class="fa-solid fa-check" style="<?php echo $product['statut'] == 1 ? 'opacity: 1;' : 'opacity: 0;'; ?>"></i>
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
                <textarea id="description" name="description" class="form-control" style="min-height: 80px;"><?php echo htmlspecialchars($product['description']); ?></textarea>
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
                    <!-- Pré-remplir avec les options existantes -->
                    <?php foreach ($options as $index => $opt): ?>
                        <div class="dynamic-option-row" id="option_row_<?php echo $index; ?>">
                            <input type="text" name="option_nom[]" class="form-control" value="<?php echo htmlspecialchars($opt['nom_option']); ?>" placeholder="Nom de l'option" required>
                            <input type="number" name="option_prix[]" class="form-control" value="<?php echo (int)$opt['prix_supplement']; ?>" placeholder="Supplément (F)" min="0" required>
                            <button type="button" class="action-btn delete" onclick="removeOptionRow(<?php echo $index; ?>)" title="Supprimer">
                                <i class="fa-solid fa-trash-can"></i>
                            </button>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 24px; padding: 16px;">
                <i class="fa-solid fa-floppy-disk"></i> Enregistrer les modifications
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
            // Remettre l'ancienne image si définie, sinon l'icône par défaut
            <?php if ($product['image']): ?>
                previewContainer.innerHTML = '<img src="<?php echo DYNAMIC_URLROOT; ?>/uploads/<?php echo htmlspecialchars($product['image']); ?>" alt="Produit">';
            <?php else: ?>
                previewContainer.innerHTML = '<i class="fa-solid fa-image" style="font-size: 2.5rem; color: #3f3f46;"></i>';
            <?php endif; ?>
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
    let optionIndex = <?php echo count($options); ?>;
    
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
