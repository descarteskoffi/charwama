<?php
/**
 * Modèle Admin - Gestion de l'authentification et de la sécurité des connexions
 */
class Admin extends Model {

    /**
     * Récupère un administrateur par son email
     */
    public function getByEmail($email) {
        $stmt = $this->db->query("SELECT * FROM admins WHERE email = :email");
        $stmt->execute([':email' => $email]);
        return $stmt->fetch();
    }

    /**
     * Enregistre une tentative de connexion (sécurité & audit)
     * 
     * @param int|null $adminId ID de l'admin (si trouvé) ou null
     * @param string $ip Adresse IP du client
     * @param string $status Résultat de la tentative ("succes", "echec_mdp", "bloque")
     */
    public function logAttempt($adminId, $ip, $status) {
        $stmt = $this->db->query("
            INSERT INTO logs_connexion (admin_id, ip, statut) 
            VALUES (:admin_id, :ip, :statut)
        ");
        return $stmt->execute([
            ':admin_id' => $adminId,
            ':ip' => $ip,
            ':statut' => $status
        ]);
    }

    /**
     * Compte le nombre de tentatives de connexion échouées d'une IP sur une période
     * 
     * @param string $ip
     * @param int $minutes Durée de la fenêtre de vérification en minutes (par défaut 15)
     */
    public function countFailedAttempts($ip, $minutes = 15) {
        $stmt = $this->db->query("
            SELECT COUNT(*) as total 
            FROM logs_connexion 
            WHERE ip = :ip 
              AND statut = 'echec_mdp' 
              AND date_heure > DATE_SUB(NOW(), INTERVAL :minutes MINUTE)
        ");
        $stmt->execute([
            ':ip' => $ip,
            ':minutes' => $minutes
        ]);
        $res = $stmt->fetch();
        return $res['total'] ?? 0;
    }

    /**
     * Récupère l'historique récent des tentatives de connexion pour le tableau de bord
     */
    public function getRecentLogs($limit = 10) {
        $stmt = $this->db->query("
            SELECT l.*, a.nom as admin_nom 
            FROM logs_connexion l 
            LEFT JOIN admins a ON l.admin_id = a.id 
            ORDER BY l.date_heure DESC 
            LIMIT :limit
        ");
        // Les limites avec PDO doivent être liées avec bindValue car les entiers sont requis
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
