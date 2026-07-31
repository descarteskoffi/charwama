<?php
/**
 * Vue - Administration - Gestion des tables & QR Codes
 */
require APPROOT . '/views/layout/admin_header.php';

// Récupérer et vider les messages en session
$successMsg = $_SESSION['table_success'] ?? '';
$errorMsg = $_SESSION['table_error'] ?? '';
unset($_SESSION['table_success'], $_SESSION['table_error']);
?>

<!-- 1. ALERTES D'ACTIONS -->
<?php if (!empty($successMsg)): ?>
    <div class="alert alert-success" style="padding: 16px; background-color: rgba(16, 185, 129, 0.15); border-left: 4px solid #10b981; color: #10b981; border-radius: 4px; margin-bottom: 24px;">
        <i class="fa-solid fa-circle-check" style="margin-right: 8px;"></i> <?php echo htmlspecialchars($successMsg); ?>
    </div>
<?php endif; ?>

<?php if (!empty($errorMsg)): ?>
    <div class="alert alert-danger" style="padding: 16px; background-color: rgba(239, 68, 68, 0.15); border-left: 4px solid #ef4444; color: #ef4444; border-radius: 4px; margin-bottom: 24px;">
        <i class="fa-solid fa-triangle-exclamation" style="margin-right: 8px;"></i> <?php echo htmlspecialchars($errorMsg); ?>
    </div>
<?php endif; ?>

<div class="cart-layout" style="grid-template-columns: 0.8fr 1.2fr; gap: 32px; align-items: flex-start;">
    
    <!-- COLONNE GAUCHE : AJOUTER UNE TABLE -->
    <div>
        <h3 style="margin-bottom: 20px; font-family: var(--font-titles);"><i class="fa-solid fa-square-plus text-primary" style="margin-right: 8px;"></i> Ajouter une table</h3>
        <div class="form-card" style="background-color: var(--bg-card); padding: 32px;">
            <form method="POST" action="<?php echo DYNAMIC_URLROOT; ?>/admin/tables/ajouter">
                <input type="hidden" name="csrf_token" value="<?php echo $csrfToken; ?>">

                <div class="form-group">
                    <label class="form-label" for="numero_table">Numéro / Identifiant de la table *</label>
                    <input type="text" id="numero_table" name="numero_table" class="form-control" placeholder="Ex: 4, 12, VIP-1..." required>
                </div>

                <div class="form-group">
                    <label class="form-label" for="nom_table">Nom descriptif (optionnel)</label>
                    <input type="text" id="nom_table" name="nom_table" class="form-control" placeholder="Ex: Table Terrasse, Table Coin...">
                </div>

                <div class="form-group" style="margin-bottom: 24px;">
                    <label class="option-checkbox-wrapper" style="display: inline-flex; cursor: pointer; align-items: center;">
                        <input type="checkbox" name="statut" value="1" checked style="display: none;" id="chk_table_add">
                        <div class="option-checkbox" style="margin-right: 8px; width: 24px; height: 24px; border: 2px solid rgba(255,255,255,0.1); border-radius: 6px; display: flex; align-items: center; justify-content: center;">
                            <i class="fa-solid fa-check" style="font-size: 0.8rem; display: block; color: var(--primary);"></i>
                        </div>
                        <span style="font-weight: bold; font-size: 0.9rem;">Table active</span>
                    </label>
                </div>

                <button type="submit" class="btn btn-primary" style="width: 100%; padding: 14px;">
                    <i class="fa-solid fa-plus"></i> Créer la Table
                </button>
            </form>
        </div>
    </div>

    <!-- COLONNE DROITE : LISTE DES TABLES ET GENERATEUR DE QR CODES -->
    <div>
        <h3 style="margin-bottom: 20px; font-family: var(--font-titles);"><i class="fa-solid fa-qrcode text-secondary" style="margin-right: 8px;"></i> Liste des Tables &amp; QR Codes</h3>
        
        <?php if (empty($tables)): ?>
            <div class="form-card" style="background-color: var(--bg-card); text-align: center; padding: 48px; color: #a0a0a5;">
                <i class="fa-solid fa-circle-info" style="font-size: 2.5rem; margin-bottom: 12px; display: block; color: var(--secondary);"></i>
                Aucune table n'a encore été configurée. Utilisez le formulaire de gauche pour en ajouter une.
            </div>
        <?php else: ?>
            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 24px;">
                <?php foreach ($tables as $table): 
                    // Construire l'URL qui sera encodée dans le QR code
                    $menuUrl = DYNAMIC_URLROOT . '/menu?table=' . urlencode($table['numero_table']);
                    $qrCodeUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=' . urlencode($menuUrl);
                ?>
                    <div class="form-card" style="background-color: var(--bg-card); padding: 24px; display: flex; flex-direction: column; align-items: center; position: relative;">
                        
                        <!-- Badge Statut -->
                        <span class="badge <?php echo $table['statut'] == 1 ? 'badge-new' : 'badge-inactive'; ?>" style="position: absolute; top: 16px; right: 16px; font-size: 0.7rem; padding: 4px 8px;">
                            <?php echo $table['statut'] == 1 ? 'Active' : 'Inactive'; ?>
                        </span>

                        <h4 style="font-family: var(--font-titles); font-size: 1.15rem; margin-bottom: 4px; text-align: center;">
                            <?php echo htmlspecialchars($table['nom_table']); ?>
                        </h4>
                        <span style="font-size: 0.8rem; color: #a0a0a5; margin-bottom: 16px;">Identifiant : <code><?php echo htmlspecialchars($table['numero_table']); ?></code></span>

                        <!-- QR Code Image -->
                        <div style="background-color: #fff; padding: 12px; border-radius: 8px; box-shadow: 0 4px 10px rgba(0,0,0,0.3); margin-bottom: 16px;">
                            <img src="<?php echo $qrCodeUrl; ?>" alt="QR Code Table <?php echo htmlspecialchars($table['numero_table']); ?>" style="width: 140px; height: 140px; display: block;">
                        </div>

                        <!-- Actions QR Code -->
                        <div style="display: flex; gap: 8px; width: 100%; margin-bottom: 16px;">
                            <button onclick="printQrCode('<?php echo htmlspecialchars($table['numero_table']); ?>', '<?php echo htmlspecialchars($table['nom_table']); ?>', '<?php echo $qrCodeUrl; ?>')" class="btn btn-secondary" style="flex: 1; padding: 8px; font-size: 0.8rem;">
                                <i class="fa-solid fa-print"></i> Imprimer
                            </button>
                            <a href="<?php echo $qrCodeUrl; ?>" target="_blank" class="btn btn-secondary" style="flex: 1; padding: 8px; font-size: 0.8rem; text-decoration: none; display: inline-flex; align-items: center; justify-content: center;">
                                <i class="fa-solid fa-download"></i> Ouvrir QR
                            </a>
                        </div>

                        <hr style="border: 0; border-top: 1px solid rgba(255,255,255,0.05); width: 100%; margin-bottom: 16px;">

                        <!-- Actions d'édition et suppression -->
                        <div style="display: flex; gap: 12px; width: 100%;">
                            <!-- Bouton modifier pour ouvrir la modale ou modifier l'état directement -->
                            <button onclick="openEditTableModal(<?php echo $table['id']; ?>, '<?php echo htmlspecialchars($table['numero_table']); ?>', '<?php echo htmlspecialchars($table['nom_table']); ?>', <?php echo $table['statut']; ?>)" class="btn btn-secondary" style="flex: 1; font-size: 0.8rem; padding: 8px; border-color: rgba(255,255,255,0.05);">
                                <i class="fa-solid fa-pencil"></i> Modifier
                            </button>
                            <!-- Bouton supprimer -->
                            <a href="<?php echo DYNAMIC_URLROOT; ?>/admin/tables/supprimer/<?php echo $table['id']; ?>" onclick="return confirmDeletion(event, 'cette table')" class="btn btn-secondary" style="flex: 0 0 40px; padding: 8px; color: #ef4444; border-color: rgba(239, 68, 68, 0.15); display: flex; align-items: center; justify-content: center;">
                                <i class="fa-solid fa-trash"></i>
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- MODALE D'ÉDITION DE TABLE -->
<div id="editTableModal" class="modal" role="dialog" aria-hidden="true" style="display: none; align-items: center; justify-content: center; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.8); z-index: 1000;">
    <div class="form-card" style="background-color: var(--bg-card); width: 100%; max-width: 450px; padding: 32px; position: relative;">
        <h3 style="margin-bottom: 24px; font-family: var(--font-titles);"><i class="fa-solid fa-pencil text-primary" style="margin-right: 8px;"></i> Modifier la table</h3>
        
        <form method="POST" action="<?php echo DYNAMIC_URLROOT; ?>/admin/tables/edit">
            <input type="hidden" name="csrf_token" value="<?php echo $csrfToken; ?>">
            <input type="hidden" id="edit_id" name="id" value="">

            <div class="form-group">
                <label class="form-label" for="edit_numero_table">Numéro / Identifiant de la table *</label>
                <input type="text" id="edit_numero_table" name="numero_table" class="form-control" required>
            </div>

            <div class="form-group">
                <label class="form-label" for="edit_nom_table">Nom descriptif</label>
                <input type="text" id="edit_nom_table" name="nom_table" class="form-control">
            </div>

            <div class="form-group" style="margin-bottom: 24px;">
                <label class="option-checkbox-wrapper" style="display: inline-flex; cursor: pointer; align-items: center;">
                    <input type="checkbox" name="statut" value="1" style="display: none;" id="edit_statut">
                    <div class="option-checkbox" style="margin-right: 8px; width: 24px; height: 24px; border: 2px solid rgba(255,255,255,0.1); border-radius: 6px; display: flex; align-items: center; justify-content: center;">
                        <i class="fa-solid fa-check" style="font-size: 0.8rem; color: var(--primary);"></i>
                    </div>
                    <span style="font-weight: bold; font-size: 0.9rem;">Table active</span>
                </label>
            </div>

            <div style="display: flex; gap: 12px;">
                <button type="button" onclick="closeEditTableModal()" class="btn btn-secondary" style="flex: 1; padding: 12px;">Annuler</button>
                <button type="submit" class="btn btn-primary" style="flex: 1; padding: 12px;">Enregistrer</button>
            </div>
        </form>
    </div>
</div>

<script>
// Impression du QR Code en mode étiquette propre
function printQrCode(number, name, qrUrl) {
    const printWindow = window.open('', '_blank', 'width=600,height=600');
    printWindow.document.write(`
        <!DOCTYPE html>
        <html>
        <head>
            <title>Imprimer QR Code - Table ${number}</title>
            <style>
                body {
                    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
                    text-align: center;
                    padding: 40px;
                    background-color: #fff;
                    color: #000;
                }
                .card {
                    border: 4px double #000;
                    padding: 30px;
                    display: inline-block;
                    border-radius: 15px;
                    box-shadow: 0 0 10px rgba(0,0,0,0.1);
                    max-width: 350px;
                }
                h1 {
                    margin: 0 0 5px 0;
                    font-size: 28px;
                    text-transform: uppercase;
                    letter-spacing: 1px;
                }
                h2 {
                    margin: 0 0 20px 0;
                    font-size: 22px;
                    color: #444;
                }
                img {
                    width: 250px;
                    height: 250px;
                    display: block;
                    margin: 0 auto 20px auto;
                }
                p {
                    font-size: 16px;
                    margin: 0;
                    font-weight: bold;
                    color: #222;
                }
                @media print {
                    body { padding: 0; }
                    .card { box-shadow: none; border-color: #000; }
                }
            </style>
        </head>
        <body onload="window.print(); window.close();">
            <div class="card">
                <h1>Chawarma Elite</h1>
                <h2>${name ? name : 'Table ' + number}</h2>
                <img src="${qrUrl}" alt="QR Code">
                <p>📲 SCANNEZ POUR COMMANDER</p>
                <p style="font-size: 12px; font-weight: normal; margin-top: 10px; color: #666;">Service rapide sur place ou à emporter</p>
            </div>
        </body>
        </html>
    `);
    printWindow.document.close();
}

// Gestion interactive de la modale d'édition
function openEditTableModal(id, number, name, status) {
    document.getElementById('edit_id').value = id;
    document.getElementById('edit_numero_table').value = number;
    document.getElementById('edit_nom_table').value = name;
    
    const chk = document.getElementById('edit_statut');
    chk.checked = (status === 1);
    
    // Mettre à jour l'état visuel de la case à cocher custom
    const icon = chk.closest('.option-checkbox-wrapper').querySelector('.option-checkbox i');
    if (icon) {
        icon.style.display = (status === 1) ? 'block' : 'none';
    }

    document.getElementById('editTableModal').style.display = 'flex';
}

function closeEditTableModal() {
    document.getElementById('editTableModal').style.display = 'none';
}

// Custom checkbox add table
document.getElementById('chk_table_add').addEventListener('change', (e) => {
    const icon = e.target.closest('.option-checkbox-wrapper').querySelector('.option-checkbox i');
    if (icon) {
        icon.style.display = e.target.checked ? 'block' : 'none';
    }
});

// Custom checkbox edit table
document.getElementById('edit_statut').addEventListener('change', (e) => {
    const icon = e.target.closest('.option-checkbox-wrapper').querySelector('.option-checkbox i');
    if (icon) {
        icon.style.display = e.target.checked ? 'block' : 'none';
    }
});
</script>

<?php require APPROOT . '/views/layout/admin_footer.php'; ?>
