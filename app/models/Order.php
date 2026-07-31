<?php
/**
 * Modèle Order - Gestion des commandes et statistiques de ventes
 */
class Order extends Model {

    /**
     * Génère un numéro de commande unique
     */
    public function generateOrderNum() {
        return 'CMD-' . date('Ymd') . '-' . strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, 5));
    }

    /**
     * Crée une commande et ses produits associés
     */
    public function create($nom_client, $telephone, $mode_reception, $table_numero, $adresse_livraison, $notes, $prix_total, $items) {
        try {
            $dbh = $this->db->getDbh();
            $dbh->beginTransaction();

            $numero_commande = $this->generateOrderNum();

            // Insérer la commande
            $stmtOrder = $dbh->prepare("
                INSERT INTO commandes (numero_commande, nom_client, telephone, mode_reception, table_numero, adresse_livraison, notes, prix_total, statut)
                VALUES (:numero_commande, :nom_client, :telephone, :mode_reception, :table_numero, :adresse_livraison, :notes, :prix_total, 'Nouvelle commande')
            ");

            $stmtOrder->execute([
                ':numero_commande' => $numero_commande,
                ':nom_client' => $nom_client,
                ':telephone' => $telephone,
                ':mode_reception' => $mode_reception,
                ':table_numero' => !empty($table_numero) ? $table_numero : null,
                ':adresse_livraison' => !empty($adresse_livraison) ? $adresse_livraison : null,
                ':notes' => !empty($notes) ? $notes : null,
                ':prix_total' => (float)$prix_total
            ]);

            $commande_id = $dbh->lastInsertId();

            // Insérer les produits associés
            $stmtItem = $dbh->prepare("
                INSERT INTO commande_produits (commande_id, produit_id, nom_produit, prix_unitaire, quantite, options_texte)
                VALUES (:commande_id, :produit_id, :nom_produit, :prix_unitaire, :quantite, :options_texte)
            ");

            foreach ($items as $item) {
                // Formater les options choisies
                $options_array = [];
                if (!empty($item['options'])) {
                    foreach ($item['options'] as $opt) {
                        $priceAdd = parseFloatVal($opt['prix']);
                        $options_array[] = $opt['nom'] . ($priceAdd > 0 ? " (+{$priceAdd} F)" : " (Gratuit)");
                    }
                }
                $options_texte = !empty($options_array) ? implode(', ', $options_array) : null;

                $stmtItem->execute([
                    ':commande_id' => $commande_id,
                    ':produit_id' => (int)$item['id'],
                    ':nom_produit' => $item['nom'],
                    ':prix_unitaire' => (float)$item['prixUnitaire'],
                    ':quantite' => (int)$item['qty'],
                    ':options_texte' => $options_texte
                ]);
            }

            $dbh->commit();
            return [
                'success' => true,
                'commande_id' => $commande_id,
                'numero_commande' => $numero_commande
            ];
        } catch (Exception $e) {
            if ($this->db->getDbh()->inTransaction()) {
                $this->db->getDbh()->rollBack();
            }
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Récupère une commande par son ID
     */
    public function getById($id) {
        $stmt = $this->db->query("SELECT * FROM commandes WHERE id = :id");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    /**
     * Récupère les articles d'une commande
     */
    public function getItemsByOrderId($orderId) {
        $stmt = $this->db->query("SELECT * FROM commande_produits WHERE commande_id = :commande_id");
        $stmt->execute([':commande_id' => $orderId]);
        return $stmt->fetchAll();
    }

    /**
     * Met à jour le statut d'une commande
     */
    public function updateStatus($id, $status) {
        $stmt = $this->db->query("UPDATE commandes SET statut = :statut WHERE id = :id");
        return $stmt->execute([
            ':id' => $id,
            ':statut' => $status
        ]);
    }

    /**
     * Récupère toutes les commandes avec des filtres optionnels
     */
    public function getAll($filters = []) {
        $sql = "SELECT * FROM commandes WHERE 1=1";
        $params = [];

        if (!empty($filters['statut'])) {
            $sql .= " AND statut = :statut";
            $params[':statut'] = $filters['statut'];
        }

        if (!empty($filters['mode_reception'])) {
            $sql .= " AND mode_reception = :mode_reception";
            $params[':mode_reception'] = $filters['mode_reception'];
        }

        if (!empty($filters['search'])) {
            $sql .= " AND (nom_client LIKE :search OR telephone LIKE :search OR numero_commande LIKE :search)";
            $params[':search'] = '%' . $filters['search'] . '%';
        }

        if (!empty($filters['date_debut'])) {
            $sql .= " AND date_creation >= :date_debut";
            $params[':date_debut'] = $filters['date_debut'] . ' 00:00:00';
        }

        if (!empty($filters['date_fin'])) {
            $sql .= " AND date_creation <= :date_fin";
            $params[':date_fin'] = $filters['date_fin'] . ' 23:59:59';
        }

        $sql .= " ORDER BY date_creation DESC";

        $stmt = $this->db->query($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /**
     * Statistiques globales filtrées par période
     */
    public function getStats($period = 'today', $customStart = null, $customEnd = null) {
        $dateCondition = "1=1";
        $params = [];

        switch ($period) {
            case 'today':
                $dateCondition = "date_creation >= CURDATE()";
                break;
            case 'week':
                $dateCondition = "YEARWEEK(date_creation, 1) = YEARWEEK(CURDATE(), 1)";
                break;
            case 'month':
                $dateCondition = "MONTH(date_creation) = MONTH(CURDATE()) AND YEAR(date_creation) = YEAR(CURDATE())";
                break;
            case 'custom':
                if (!empty($customStart)) {
                    $dateCondition = "date_creation >= :start";
                    $params[':start'] = $customStart . ' 00:00:00';
                }
                if (!empty($customEnd)) {
                    $dateCondition .= (!empty($customStart) ? " AND " : "") . "date_creation <= :end";
                    $params[':end'] = $customEnd . ' 23:59:59';
                }
                break;
        }

        $stats = [];

        // 1. Chiffre d'affaires et nombre de commandes sur la période
        $stmt = $this->db->query("
            SELECT COUNT(*) as count, SUM(prix_total) as revenue 
            FROM commandes 
            WHERE {$dateCondition} AND statut != 'Annulée'
        ");
        $stmt->execute($params);
        $row = $stmt->fetch();
        $stats['total_orders'] = $row['count'] ?? 0;
        $stats['total_revenue'] = $row['revenue'] ?? 0;

        // 2. Commandes du jour et de la semaine (KPI fixes requis par l'énoncé)
        $stmtToday = $this->db->query("SELECT COUNT(*) as count, SUM(prix_total) as revenue FROM commandes WHERE date_creation >= CURDATE() AND statut != 'Annulée'");
        $stmtToday->execute();
        $rowToday = $stmtToday->fetch();
        $stats['kpi_today_orders'] = $rowToday['count'] ?? 0;
        $stats['kpi_today_revenue'] = $rowToday['revenue'] ?? 0;

        $stmtWeek = $this->db->query("SELECT COUNT(*) as count, SUM(prix_total) as revenue FROM commandes WHERE YEARWEEK(date_creation, 1) = YEARWEEK(CURDATE(), 1) AND statut != 'Annulée'");
        $stmtWeek->execute();
        $rowWeek = $stmtWeek->fetch();
        $stats['kpi_week_orders'] = $rowWeek['count'] ?? 0;
        $stats['kpi_week_revenue'] = $rowWeek['revenue'] ?? 0;

        // 3. Commandes par statut sur la période
        $stmtStatus = $this->db->query("
            SELECT statut, COUNT(*) as count 
            FROM commandes 
            WHERE {$dateCondition}
            GROUP BY statut
        ");
        $stmtStatus->execute($params);
        $statusRows = $stmtStatus->fetchAll();
        $stats['by_status'] = [
            'Nouvelle commande' => 0,
            'Confirmée' => 0,
            'En préparation' => 0,
            'Prête' => 0,
            'En livraison' => 0,
            'Terminée' => 0,
            'Annulée' => 0
        ];
        foreach ($statusRows as $r) {
            $stats['by_status'][$r['statut']] = (int)$r['count'];
        }

        // 4. Commandes par mode de réception sur la période
        $stmtMode = $this->db->query("
            SELECT mode_reception, COUNT(*) as count 
            FROM commandes 
            WHERE {$dateCondition} AND statut != 'Annulée'
            GROUP BY mode_reception
        ");
        $stmtMode->execute($params);
        $modeRows = $stmtMode->fetchAll();
        $stats['by_mode'] = [
            'Sur place' => 0,
            'À emporter' => 0,
            'Livraison' => 0
        ];
        foreach ($modeRows as $r) {
            // Normaliser le nom de clé
            $key = $r['mode_reception'];
            if ($key === 'A emporter') $key = 'À emporter';
            $stats['by_mode'][$key] = (int)$r['count'];
        }

        // 5. Plats les plus commandés sur la période
        $stmtTop = $this->db->query("
            SELECT nom_produit, SUM(quantite) as total_qty 
            FROM commande_produits cp
            INNER JOIN commandes c ON cp.commande_id = c.id
            WHERE c.{$dateCondition} AND c.statut != 'Annulée'
            GROUP BY nom_produit
            ORDER BY total_qty DESC
            LIMIT 5
        ");
        $stmtTop->execute($params);
        $stats['top_items'] = $stmtTop->fetchAll();

        // 6. Évolution des ventes quotidiennes (pour les graphiques)
        // Si période = aujourd'hui, on fait une évolution par heure, sinon par jour
        $groupBy = "DATE(date_creation)";
        $selectDate = "DATE_FORMAT(date_creation, '%d/%m') as label";
        if ($period === 'today') {
            $groupBy = "HOUR(date_creation)";
            $selectDate = "DATE_FORMAT(date_creation, '%Hh') as label";
        }
        
        $stmtEvol = $this->db->query("
            SELECT {$selectDate}, SUM(prix_total) as revenue, COUNT(*) as count
            FROM commandes
            WHERE {$dateCondition} AND statut != 'Annulée'
            GROUP BY {$groupBy}
            ORDER BY date_creation ASC
        ");
        $stmtEvol->execute($params);
        $stats['evolution'] = $stmtEvol->fetchAll();

        return $stats;
    }
}

/**
 * Helper de conversion de float pour éviter les avertissements d'analyse
 */
function parseFloatVal($val) {
    return floatval($val);
}
