<?php
/**
 * Vue - Administration - Gestion des horaires d'ouverture
 */
require APPROOT . '/views/layout/admin_header.php';
?>

<div style="max-width: 900px; margin: 0 auto;">

    <!-- Messages d'information -->
    <?php if (!empty($success)): ?>
        <div class="alert alert-success" style="padding: 16px; background-color: rgba(16, 185, 129, 0.15); border-left: 4px solid #10b981; color: #10b981; border-radius: 4px; margin-bottom: 24px;">
            <i class="fa-solid fa-circle-check" style="margin-right: 8px;"></i> <?php echo htmlspecialchars($success); ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="<?php echo DYNAMIC_URLROOT; ?>/admin/horaires">
        <input type="hidden" name="csrf_token" value="<?php echo $csrfToken; ?>">

        <!-- 1. BOUTON D'OUVERTURE / FERMETURE MANUELLE ET FORCÉE -->
        <h3 style="margin-bottom: 16px; font-family: var(--font-titles);"><i class="fa-solid fa-power-off text-primary" style="margin-right: 8px;"></i> Commutateur d'urgence</h3>
        <div class="form-card" style="background-color: var(--bg-card); padding: 32px; margin-bottom: 32px; border-left: 4px solid var(--primary);">
            <p style="margin-bottom: 20px; font-size: 0.95rem; color: #a0a0a5; line-height: 1.5;">
                Vous pouvez forcer la fermeture immédiate des commandes en ligne à tout moment, par exemple en cas de rush en cuisine ou d'événement imprévu. Cela désactivera instantanément la validation du panier pour les clients.
            </p>
            
            <?php
            $etatManuel = $siteSettings['etat_ouverture_manuel'] ?? 'ouvert';
            ?>
            <div style="display: flex; gap: 16px; align-items: center;">
                <label class="option-item" for="switch_ouvert" style="flex: 1; justify-content: center; padding: 20px; border: 2px solid <?php echo $etatManuel === 'ouvert' ? 'var(--primary)' : 'rgba(255,255,255,0.05)'; ?>; background-color: <?php echo $etatManuel === 'ouvert' ? 'rgba(255,107,8,0.08)' : 'transparent'; ?>; cursor: pointer; border-radius: 8px;">
                    <input type="radio" id="switch_ouvert" name="etat_ouverture_manuel" value="ouvert" <?php echo $etatManuel === 'ouvert' ? 'checked' : ''; ?> style="margin-right: 8px;">
                    <strong style="color: #10b981; font-size: 1.1rem;"><i class="fa-solid fa-circle-play" style="margin-right: 4px;"></i> OUVERT NORMALEMENT</strong>
                </label>

                <label class="option-item" for="switch_ferme" style="flex: 1; justify-content: center; padding: 20px; border: 2px solid <?php echo $etatManuel === 'ferme' ? '#ef4444' : 'rgba(255,255,255,0.05)'; ?>; background-color: <?php echo $etatManuel === 'ferme' ? 'rgba(239,68,68,0.08)' : 'transparent'; ?>; cursor: pointer; border-radius: 8px;">
                    <input type="radio" id="switch_ferme" name="etat_ouverture_manuel" value="ferme" <?php echo $etatManuel === 'ferme' ? 'checked' : ''; ?> style="margin-right: 8px;">
                    <strong style="color: #ef4444; font-size: 1.1rem;"><i class="fa-solid fa-circle-stop" style="margin-right: 4px;"></i> FERMER IMMÉDIATEMENT</strong>
                </label>
            </div>
        </div>

        <!-- 2. GESTION DES FERMETURES TEMPORAIRES / EXCEPTIONNELLES -->
        <h3 style="margin-bottom: 16px; font-family: var(--font-titles);"><i class="fa-solid fa-calendar-times" style="color: var(--secondary); margin-right: 8px;"></i> Planification des fermetures</h3>
        <div class="form-card" style="background-color: var(--bg-card); padding: 32px; margin-bottom: 32px; display: grid; grid-template-columns: 1fr 1fr; gap: 24px;">
            <!-- Fermeture exceptionnelle -->
            <div>
                <label class="form-label" for="fermeture_exceptionnelle_date">Fermeture exceptionnelle (Date précise)</label>
                <p style="font-size: 0.8rem; color: #a0a0a5; margin-bottom: 12px; height: 32px;">Le restaurant sera fermé toute cette journée (ex: jour férié).</p>
                <input type="date" id="fermeture_exceptionnelle_date" name="fermeture_exceptionnelle_date" class="form-control" value="<?php echo htmlspecialchars($siteSettings['fermeture_exceptionnelle_date'] ?? ''); ?>">
            </div>

            <!-- Période de fermeture temporaire -->
            <div>
                <label class="form-label">Période de fermeture temporaire (Congés / Travaux)</label>
                <p style="font-size: 0.8rem; color: #a0a0a5; margin-bottom: 12px; height: 32px;">Période comprise entre deux dates/heures de début et fin.</p>
                
                <div style="display: flex; gap: 12px; align-items: center;">
                    <div style="flex: 1;">
                        <span style="font-size: 0.75rem; color: #a0a0a5;">Début</span>
                        <input type="datetime-local" name="fermeture_temporaire_debut" class="form-control" value="<?php echo htmlspecialchars($siteSettings['fermeture_temporaire_debut'] ?? ''); ?>">
                    </div>
                    <div style="flex: 1;">
                        <span style="font-size: 0.75rem; color: #a0a0a5;">Fin</span>
                        <input type="datetime-local" name="fermeture_temporaire_fin" class="form-control" value="<?php echo htmlspecialchars($siteSettings['fermeture_temporaire_fin'] ?? ''); ?>">
                    </div>
                </div>
            </div>
        </div>

        <!-- 3. HORAIRES DE LA SEMAINE -->
        <h3 style="margin-bottom: 16px; font-family: var(--font-titles);"><i class="fa-solid fa-calendar-days text-primary" style="margin-right: 8px;"></i> Horaires hebdomadaires réguliers</h3>
        <div class="table-card" style="margin-bottom: 32px;">
            <div class="table-responsive">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th style="width: 180px;">Jour</th>
                            <th style="width: 150px; text-align: center;">Statut d'ouverture</th>
                            <th>Heure d'ouverture</th>
                            <th>Heure de fermeture</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $joursNoms = [
                            0 => 'Dimanche',
                            1 => 'Lundi',
                            2 => 'Mardi',
                            3 => 'Mercredi',
                            4 => 'Jeudi',
                            5 => 'Vendredi',
                            6 => 'Samedi'
                        ];

                        foreach ($weeklySchedules as $sched):
                            $j = (int)$sched['jour_semaine'];
                            $jourNom = $joursNoms[$j] ?? 'Inconnu';
                        ?>
                            <tr>
                                <td><strong><?php echo $jourNom; ?></strong></td>
                                <td style="text-align: center;">
                                    <label class="option-checkbox-wrapper" style="display: inline-flex; cursor: pointer; align-items: center; justify-content: center;">
                                        <input type="checkbox" name="ouvert_<?php echo $j; ?>" value="1" <?php echo $sched['ouvert'] == 1 ? 'checked' : ''; ?> style="display: none;" id="chk_<?php echo $j; ?>" class="sched-chk">
                                        <div class="option-checkbox" style="margin-right: 8px; width: 24px; height: 24px; border: 2px solid rgba(255,255,255,0.1); border-radius: 6px; display: flex; align-items: center; justify-content: center;">
                                            <i class="fa-solid fa-check" style="font-size: 0.8rem; display: <?php echo $sched['ouvert'] == 1 ? 'block' : 'none'; ?>; color: var(--primary);"></i>
                                        </div>
                                        <span class="lbl-status-<?php echo $j; ?>" style="font-weight: bold; font-size: 0.85rem; color: <?php echo $sched['ouvert'] == 1 ? '#10b981' : '#ef4444'; ?>;">
                                            <?php echo $sched['ouvert'] == 1 ? 'OUVERT' : 'FERMÉ'; ?>
                                        </span>
                                    </label>
                                </td>
                                <td>
                                    <input type="time" name="ouverture_<?php echo $j; ?>" class="form-control time-input-<?php echo $j; ?>" value="<?php echo htmlspecialchars($sched['heure_ouverture']); ?>" <?php echo $sched['ouvert'] == 0 ? 'disabled' : ''; ?> style="width: 140px;">
                                </td>
                                <td>
                                    <input type="time" name="fermeture_<?php echo $j; ?>" class="form-control time-input-<?php echo $j; ?>" value="<?php echo htmlspecialchars($sched['heure_fermeture']); ?>" <?php echo $sched['ouvert'] == 0 ? 'disabled' : ''; ?> style="width: 140px;">
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- 4. BOUTON DE SOUMISSION -->
        <div style="text-align: right; margin-bottom: 40px;">
            <button type="submit" class="btn btn-primary" style="padding: 16px 32px; font-size: 1rem; border-radius: 8px;">
                <i class="fa-solid fa-save"></i> Enregistrer tous les horaires
            </button>
        </div>
    </form>
</div>

<!-- Scripts interactifs pour les checkboxes d'horaires -->
<script>
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.sched-chk').forEach(chk => {
        chk.addEventListener('change', (e) => {
            const checked = e.target.checked;
            const row = e.target.closest('tr');
            const dayId = e.target.id.split('_')[1];
            
            // Modifier le libellé textuel
            const lbl = row.querySelector('.lbl-status-' + dayId);
            if (lbl) {
                lbl.innerText = checked ? 'OUVERT' : 'FERMÉ';
                lbl.style.color = checked ? '#10b981' : '#ef4444';
            }
            
            // Modifier l'icône à l'intérieur de la case
            const icon = row.querySelector('.option-checkbox i');
            if (icon) {
                icon.style.display = checked ? 'block' : 'none';
            }

            // Activer/Désactiver les sélecteurs de temps
            row.querySelectorAll('.time-input-' + dayId).forEach(input => {
                if (checked) {
                    input.removeAttribute('disabled');
                } else {
                    input.setAttribute('disabled', 'disabled');
                }
            });
        });
    });
});
</script>

<?php require APPROOT . '/views/layout/admin_footer.php'; ?>
