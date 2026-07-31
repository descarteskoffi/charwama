<?php
/**
 * Vue - Administration - Tableau de Bord enrichi avec Chart.js
 */
require APPROOT . '/views/layout/admin_header.php';

// Extraction des données statistiques calculées par le modèle
$stats = $data['stats'] ?? [];
$byStatus = $stats['by_status'] ?? [];
$byMode = $stats['by_mode'] ?? [];
$topItems = $stats['top_items'] ?? [];
$evolution = $stats['evolution'] ?? [];

// Libellé de la période sélectionnée
$periodLabels = [
    'today' => "Aujourd'hui",
    'week' => "Cette semaine",
    'month' => "Ce mois-ci",
    'custom' => "Période personnalisée"
];
$currentPeriodLabel = $periodLabels[$filter] ?? "Aujourd'hui";
?>

<!-- Inclusion de Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<!-- 1. BARRE DE FILTRES TEMPORELS -->
<div class="form-card" style="background-color: var(--bg-card); padding: 20px; margin-bottom: 32px;">
    <form method="GET" action="<?php echo DYNAMIC_URLROOT; ?>/admin/dashboard" id="dashboardFilterForm" style="display: flex; flex-wrap: wrap; gap: 16px; align-items: flex-end;">
        
        <div class="form-group" style="margin-bottom: 0; flex: 1; min-width: 200px;">
            <label class="form-label" for="filter" style="margin-bottom: 6px;">Période d'analyse</label>
            <select id="filter" name="filter" class="form-control" onchange="toggleCustomDates()">
                <option value="today" <?php echo $filter === 'today' ? 'selected' : ''; ?>>Aujourd'hui</option>
                <option value="week" <?php echo $filter === 'week' ? 'selected' : ''; ?>>Cette semaine</option>
                <option value="month" <?php echo $filter === 'month' ? 'selected' : ''; ?>>Ce mois</option>
                <option value="custom" <?php echo $filter === 'custom' ? 'selected' : ''; ?>>Période personnalisée</option>
            </select>
        </div>

        <div class="form-group custom-date-group" style="margin-bottom: 0; width: 160px; display: <?php echo $filter === 'custom' ? 'block' : 'none'; ?>;">
            <label class="form-label" for="start">Date de début</label>
            <input type="date" id="start" name="start" class="form-control" value="<?php echo htmlspecialchars($start); ?>">
        </div>

        <div class="form-group custom-date-group" style="margin-bottom: 0; width: 160px; display: <?php echo $filter === 'custom' ? 'block' : 'none'; ?>;">
            <label class="form-label" for="end">Date de fin</label>
            <input type="date" id="end" name="end" class="form-control" value="<?php echo htmlspecialchars($end); ?>">
        </div>

        <button type="submit" class="btn btn-primary" style="padding: 14px 28px;">
            <i class="fa-solid fa-sync"></i> Actualiser
        </button>
    </form>
</div>

<!-- 2. CARTES DE STATISTIQUES (KPIs fixes) -->
<h3 style="margin-bottom: 16px; font-family: var(--font-titles);"><i class="fa-solid fa-chart-bar text-primary" style="margin-right: 8px;"></i> Indicateurs de Performance Globale</h3>
<div class="stats-grid" style="margin-bottom: 32px; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));">
    
    <!-- Ventes du jour -->
    <div class="stat-card" style="border-left: 4px solid #10b981;">
        <div class="stat-card-details">
            <h4>Chiffre d'Affaires du Jour</h4>
            <div class="stat-card-val" style="color: #10b981;">
                <?php echo number_format($stats['kpi_today_revenue'], 0, ',', ' '); ?> F
            </div>
            <span style="font-size:0.75rem; color:#a0a0a5;"><?php echo (int)$stats['kpi_today_orders']; ?> commande(s) aujourd'hui</span>
        </div>
        <div class="stat-card-icon" style="background-color: rgba(16, 185, 129, 0.1); color: #10b981;">
            <i class="fa-solid fa-coins"></i>
        </div>
    </div>
    
    <!-- Ventes de la semaine -->
    <div class="stat-card" style="border-left: 4px solid var(--secondary);">
        <div class="stat-card-details">
            <h4>Ventes de la Semaine</h4>
            <div class="stat-card-val" style="color: var(--secondary);">
                <?php echo number_format($stats['kpi_week_revenue'], 0, ',', ' '); ?> F
            </div>
            <span style="font-size:0.75rem; color:#a0a0a5;"><?php echo (int)$stats['kpi_week_orders']; ?> commande(s) cette semaine</span>
        </div>
        <div class="stat-card-icon" style="background-color: rgba(46, 196, 182, 0.1); color: var(--secondary);">
            <i class="fa-solid fa-calendar-check"></i>
        </div>
    </div>

    <!-- Commandes en cours sur la période -->
    <div class="stat-card" style="border-left: 4px solid #ff6b08;">
        <div class="stat-card-details">
            <h4>Commandes actives</h4>
            <div class="stat-card-val" style="color: #ff6b08;">
                <?php echo ($byStatus['Nouvelle commande'] + $byStatus['Confirmée'] + $byStatus['En préparation']); ?>
            </div>
            <span style="font-size:0.75rem; color:#a0a0a5;">
                <?php echo $byStatus['Nouvelle commande']; ?> nouvelle(s) / <?php echo $byStatus['En préparation']; ?> en cuisine
            </span>
        </div>
        <div class="stat-card-icon" style="background-color: rgba(255, 107, 8, 0.1); color: #ff6b08;">
            <i class="fa-solid fa-bell"></i>
        </div>
    </div>

    <!-- Commandes terminées sur la période -->
    <div class="stat-card" style="border-left: 4px solid #3b82f6;">
        <div class="stat-card-details">
            <h4>Commandes terminées</h4>
            <div class="stat-card-val" style="color: #3b82f6;">
                <?php echo $byStatus['Terminée'] ?? 0; ?>
            </div>
            <span style="font-size:0.75rem; color:#a0a0a5;">Sur la période sélectionnée</span>
        </div>
        <div class="stat-card-icon" style="background-color: rgba(59, 130, 246, 0.1); color: #3b82f6;">
            <i class="fa-solid fa-circle-check"></i>
        </div>
    </div>
</div>

<!-- 3. DEUXIÈME BLOC DE STATS RAPIDES PAR PÉRIODE -->
<div class="stats-grid" style="margin-bottom: 32px; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));">
    <div class="stat-card">
        <div class="stat-card-details">
            <h4>Commandes (<?php echo $currentPeriodLabel; ?>)</h4>
            <div class="stat-card-val"><?php echo $stats['total_orders']; ?></div>
        </div>
        <div class="stat-card-icon"><i class="fa-solid fa-receipt"></i></div>
    </div>
    
    <div class="stat-card">
        <div class="stat-card-details">
            <h4>CA Estimé (<?php echo $currentPeriodLabel; ?>)</h4>
            <div class="stat-card-val" style="color: var(--primary);"><?php echo number_format($stats['total_revenue'], 0, ',', ' '); ?> F</div>
        </div>
        <div class="stat-card-icon"><i class="fa-solid fa-wallet"></i></div>
    </div>

    <div class="stat-card">
        <div class="stat-card-details">
            <h4>Produits au menu</h4>
            <div class="stat-card-val"><?php echo $prodCount; ?></div>
        </div>
        <div class="stat-card-icon"><i class="fa-solid fa-hamburger"></i></div>
    </div>

    <div class="stat-card">
        <div class="stat-card-details">
            <h4>Tables configurées</h4>
            <div class="stat-card-val"><?php echo $tableCount; ?></div>
        </div>
        <div class="stat-card-icon"><i class="fa-solid fa-chair"></i></div>
    </div>
</div>

<!-- 4. GRAPHIQUES ANALYTIQUES CHART.JS -->
<h3 style="margin-bottom: 16px; font-family: var(--font-titles);"><i class="fa-solid fa-chart-line text-secondary" style="margin-right: 8px;"></i> Graphiques d'activité (<?php echo $currentPeriodLabel; ?>)</h3>
<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 32px; margin-bottom: 32px;">
    
    <!-- Graphique 1 : Évolution du CA -->
    <div class="form-card" style="background-color: var(--bg-card); padding: 24px;">
        <h4 style="margin-bottom: 16px; font-family: var(--font-titles); font-size: 1rem;"><i class="fa-solid fa-trend-up text-primary"></i> Évolution du Chiffre d'Affaires</h4>
        <div style="height: 280px; position: relative;">
            <canvas id="salesEvolutionChart"></canvas>
        </div>
    </div>

    <!-- Graphique 2 : Volume de commandes -->
    <div class="form-card" style="background-color: var(--bg-card); padding: 24px;">
        <h4 style="margin-bottom: 16px; font-family: var(--font-titles); font-size: 1rem;"><i class="fa-solid fa-shopping-basket text-secondary"></i> Nombre de commandes</h4>
        <div style="height: 280px; position: relative;">
            <canvas id="ordersCountChart"></canvas>
        </div>
    </div>

    <!-- Graphique 3 : Mode de réception (Doughnut) -->
    <div class="form-card" style="background-color: var(--bg-card); padding: 24px;">
        <h4 style="margin-bottom: 16px; font-family: var(--font-titles); font-size: 1rem;"><i class="fa-solid fa-truck text-secondary"></i> Répartition par mode de réception</h4>
        <div style="height: 280px; position: relative; display: flex; justify-content: center;">
            <canvas id="receptionModeChart"></canvas>
        </div>
    </div>

    <!-- Graphique 4 : Plats les plus vendus -->
    <div class="form-card" style="background-color: var(--bg-card); padding: 24px;">
        <h4 style="margin-bottom: 16px; font-family: var(--font-titles); font-size: 1rem;"><i class="fa-solid fa-star text-primary"></i> Top 5 des plats les plus vendus</h4>
        <div style="height: 280px; position: relative;">
            <canvas id="topProductsChart"></canvas>
        </div>
    </div>
</div>

<!-- 5. JOURNAL DE SÉCURITÉ ET ACTIONS RAPIDES -->
<div class="cart-layout" style="grid-template-columns: 1fr 1fr; gap: 32px;">
    <!-- Raccourcis d'administration -->
    <div>
        <h3 style="margin-bottom: 20px; font-family: var(--font-titles);"><i class="fa-solid fa-bolt text-primary" style="margin-right: 8px;"></i> Raccourcis back-office</h3>
        <div class="form-card" style="background-color: var(--bg-card); display: flex; flex-direction: column; gap: 16px; padding: 32px;">
            <a href="<?php echo DYNAMIC_URLROOT; ?>/admin/commandes" class="btn btn-primary" style="justify-content: flex-start; padding: 16px;">
                <i class="fa-solid fa-receipt"></i> Gérer les Commandes
            </a>
            <a href="<?php echo DYNAMIC_URLROOT; ?>/admin/horaires" class="btn btn-secondary" style="justify-content: flex-start; padding: 16px;">
                <i class="fa-solid fa-clock"></i> Modifier les Horaires d'ouverture
            </a>
            <a href="<?php echo DYNAMIC_URLROOT; ?>/admin/tables" class="btn btn-secondary" style="justify-content: flex-start; padding: 16px; border-color: rgba(255,255,255,0.05);">
                <i class="fa-solid fa-qrcode"></i> Gérer les Tables &amp; QR Codes
            </a>
        </div>
    </div>

    <!-- Sécurité : Tentatives de connexion -->
    <div>
        <h3 style="margin-bottom: 20px; font-family: var(--font-titles);"><i class="fa-solid fa-shield-halved" style="color: var(--secondary); margin-right: 8px;"></i> Journal des accès (Sécurité)</h3>
        <div class="table-card">
            <div class="table-responsive">
                <table class="admin-table" style="font-size: 0.85rem;">
                    <thead>
                        <tr>
                            <th>Date / Heure</th>
                            <th>Adresse IP</th>
                            <th>Résultat</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($recentLogs)): ?>
                            <tr>
                                <td colspan="3" style="text-align: center; color:#a0a0a5; padding: 20px;">Aucun historique disponible.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($recentLogs as $log): ?>
                                <tr>
                                    <td><?php echo date('d/m/Y H:i:s', strtotime($log['date_heure'])); ?></td>
                                    <td><code><?php echo htmlspecialchars($log['ip']); ?></code></td>
                                    <td>
                                        <?php if ($log['statut'] === 'succes'): ?>
                                            <span class="badge badge-new" style="font-size: 0.65rem; padding: 2px 6px;">Succès</span>
                                        <?php elseif ($log['statut'] === 'echec_mdp'): ?>
                                            <span class="badge badge-popular" style="font-size: 0.65rem; padding: 2px 6px;">Échec</span>
                                        <?php else: ?>
                                            <span class="badge badge-inactive" style="font-size: 0.65rem; padding: 2px 6px;"><?php echo htmlspecialchars($log['statut']); ?></span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- =========================================================================
     CONFIGURATION DES GRAPHIQUES VIA JAVASCRIPT
     ========================================================================= -->
<?php
// Préparer les données pour l'évolution
$evolLabels = [];
$evolRevenue = [];
$evolCount = [];
foreach ($evolution as $e) {
    $evolLabels[] = $e['label'];
    $evolRevenue[] = (float)$e['revenue'];
    $evolCount[] = (int)$e['count'];
}

// Préparer les données pour les modes
$modeLabels = array_keys($byMode);
$modeData = array_values($byMode);

// Préparer les données pour les tops produits
$topLabels = [];
$topData = [];
foreach ($topItems as $item) {
    $topLabels[] = $item['nom_produit'];
    $topData[] = (int)$item['total_qty'];
}
?>

<script>
function toggleCustomDates() {
    const filter = document.getElementById('filter').value;
    const dateGroups = document.querySelectorAll('.custom-date-group');
    dateGroups.forEach(el => {
        el.style.display = (filter === 'custom') ? 'block' : 'none';
    });
}

document.addEventListener('DOMContentLoaded', () => {
    // Config Chart.js Global
    Chart.defaults.color = '#a0a0a5';
    Chart.defaults.font.family = "'Outfit', sans-serif";

    // 1. Graphique Évolution du CA (Line Chart)
    const ctxSales = document.getElementById('salesEvolutionChart').getContext('2d');
    new Chart(ctxSales, {
        type: 'line',
        data: {
            labels: <?php echo json_encode($evolLabels); ?>,
            datasets: [{
                label: 'Chiffre d\'Affaires (FCFA)',
                data: <?php echo json_encode($evolRevenue); ?>,
                borderColor: '#ff6b08',
                backgroundColor: 'rgba(255, 107, 8, 0.1)',
                borderWidth: 3,
                fill: true,
                tension: 0.3
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                y: { grid: { color: 'rgba(255,255,255,0.05)' } },
                x: { grid: { display: false } }
            }
        }
    });

    // 2. Graphique Nombre de Commandes (Bar Chart)
    const ctxOrders = document.getElementById('ordersCountChart').getContext('2d');
    new Chart(ctxOrders, {
        type: 'bar',
        data: {
            labels: <?php echo json_encode($evolLabels); ?>,
            datasets: [{
                label: 'Nombre de commandes',
                data: <?php echo json_encode($evolCount); ?>,
                backgroundColor: '#2ec4b6',
                borderRadius: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                y: { grid: { color: 'rgba(255,255,255,0.05)' }, ticks: { stepSize: 1 } },
                x: { grid: { display: false } }
            }
        }
    });

    // 3. Graphique Mode de réception (Doughnut Chart)
    const ctxMode = document.getElementById('receptionModeChart').getContext('2d');
    new Chart(ctxMode, {
        type: 'doughnut',
        data: {
            labels: <?php echo json_encode($modeLabels); ?>,
            datasets: [{
                data: <?php echo json_encode($modeData); ?>,
                backgroundColor: ['#2ec4b6', '#ff6b08', '#3b82f6'],
                borderColor: '#18181b',
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'bottom', labels: { padding: 12 } }
            }
        }
    });

    // 4. Graphique Top Plats (Horizontal Bar Chart)
    const ctxTop = document.getElementById('topProductsChart').getContext('2d');
    new Chart(ctxTop, {
        type: 'bar',
        data: {
            labels: <?php echo json_encode($topLabels); ?>,
            datasets: [{
                label: 'Quantité vendue',
                data: <?php echo json_encode($topData); ?>,
                backgroundColor: '#ff6b08',
                borderRadius: 4
            }]
        },
        options: {
            indexAxis: 'y',
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                x: { grid: { color: 'rgba(255,255,255,0.05)' }, ticks: { stepSize: 1 } },
                y: { grid: { display: false } }
            }
        }
    });
});
</script>

<?php require APPROOT . '/views/layout/admin_footer.php'; ?>
