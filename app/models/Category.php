<?php
/**
 * Modèle Category - Gestion des catégories de produits
 */
class Category extends Model {

    /**
     * Récupère toutes les catégories (pour le back-office)
     */
    public function getAll() {
        $stmt = $this->db->query("SELECT * FROM categories ORDER BY ordre ASC, id DESC");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Récupère les catégories actives uniquement (pour le front-office)
     */
    public function getAllActive() {
        $stmt = $this->db->query("SELECT * FROM categories WHERE statut = 1 ORDER BY ordre ASC, id DESC");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Récupère une catégorie par son ID
     */
    public function getById($id) {
        $stmt = $this->db->query("SELECT * FROM categories WHERE id = :id");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    /**
     * Ajoute une nouvelle catégorie
     */
    public function add($nom, $ordre, $statut) {
        $stmt = $this->db->query("INSERT INTO categories (nom, ordre, statut) VALUES (:nom, :ordre, :statut)");
        return $stmt->execute([
            ':nom' => $nom,
            ':ordre' => (int)$ordre,
            ':statut' => (int)$statut
        ]);
    }

    /**
     * Modifie une catégorie existante
     */
    public function update($id, $nom, $ordre, $statut) {
        $stmt = $this->db->query("UPDATE categories SET nom = :nom, ordre = :ordre, statut = :statut WHERE id = :id");
        return $stmt->execute([
            ':id' => $id,
            ':nom' => $nom,
            ':ordre' => (int)$ordre,
            ':statut' => (int)$statut
        ]);
    }

    /**
     * Supprime une catégorie
     */
    public function delete($id) {
        $stmt = $this->db->query("DELETE FROM categories WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }

    /**
     * Retourne le nombre total de catégories
     */
    public function count() {
        $stmt = $this->db->query("SELECT COUNT(*) as total FROM categories");
        $stmt->execute();
        $res = $stmt->fetch();
        return $res['total'] ?? 0;
    }
}
