<?php
/**
 * Modèle RestaurantTable - Gestion des tables et des QR codes
 */
class RestaurantTable extends Model {

    /**
     * Récupère toutes les tables du restaurant
     */
    public function getAll() {
        $stmt = $this->db->query("SELECT * FROM tables_restaurant ORDER BY LENGTH(numero_table), numero_table ASC");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Récupère uniquement les tables actives
     */
    public function getAllActive() {
        $stmt = $this->db->query("SELECT * FROM tables_restaurant WHERE statut = 1 ORDER BY LENGTH(numero_table), numero_table ASC");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Récupère une table par son ID
     */
    public function getById($id) {
        $stmt = $this->db->query("SELECT * FROM tables_restaurant WHERE id = :id");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    /**
     * Récupère et vérifie une table par son numéro
     */
    public function getByNumber($number) {
        $stmt = $this->db->query("SELECT * FROM tables_restaurant WHERE numero_table = :numero_table");
        $stmt->execute([':numero_table' => $number]);
        return $stmt->fetch();
    }

    /**
     * Ajoute une nouvelle table
     */
    public function add($numero_table, $nom_table, $statut) {
        $stmt = $this->db->query("
            INSERT INTO tables_restaurant (numero_table, nom_table, statut) 
            VALUES (:numero_table, :nom_table, :statut)
        ");
        return $stmt->execute([
            ':numero_table' => $numero_table,
            ':nom_table' => !empty($nom_table) ? $nom_table : "Table " . $numero_table,
            ':statut' => (int)$statut
        ]);
    }

    /**
     * Met à jour une table existante
     */
    public function update($id, $numero_table, $nom_table, $statut) {
        $stmt = $this->db->query("
            UPDATE tables_restaurant 
            SET numero_table = :numero_table, nom_table = :nom_table, statut = :statut 
            WHERE id = :id
        ");
        return $stmt->execute([
            ':id' => $id,
            ':numero_table' => $numero_table,
            ':nom_table' => !empty($nom_table) ? $nom_table : "Table " . $numero_table,
            ':statut' => (int)$statut
        ]);
    }

    /**
     * Supprime une table
     */
    public function delete($id) {
        $stmt = $this->db->query("DELETE FROM tables_restaurant WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }

    /**
     * Compte le nombre de tables
     */
    public function count() {
        $stmt = $this->db->query("SELECT COUNT(*) as total FROM tables_restaurant");
        $stmt->execute();
        $res = $stmt->fetch();
        return $res['total'] ?? 0;
    }
}
