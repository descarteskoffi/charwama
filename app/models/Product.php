<?php
/**
 * Modèle Product - Gestion des produits et de leurs options
 */
class Product extends Model {

    /**
     * Récupère tous les produits avec le nom de leur catégorie (pour l'admin)
     */
    public function getAll() {
        $stmt = $this->db->query("
            SELECT p.*, c.nom as categorie_nom 
            FROM produits p 
            INNER JOIN categories c ON p.categorie_id = c.id 
            ORDER BY c.ordre ASC, p.id DESC
        ");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Récupère tous les produits actifs, éventuellement filtrés par catégorie (pour le front)
     */
    public function getAllActive($categoryId = null) {
        $sql = "SELECT p.*, c.nom as categorie_nom 
                FROM produits p 
                INNER JOIN categories c ON p.categorie_id = c.id 
                WHERE p.statut = 1 AND c.statut = 1";
        
        $params = [];
        if ($categoryId !== null) {
            $sql .= " AND p.categorie_id = :categorie_id";
            $params[':categorie_id'] = $categoryId;
        }
        
        $sql .= " ORDER BY c.ordre ASC, p.id DESC";
        
        $stmt = $this->db->query($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /**
     * Récupère un produit par son ID
     */
    public function getById($id) {
        $stmt = $this->db->query("SELECT * FROM produits WHERE id = :id");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    /**
     * Récupère les options associées à un produit
     */
    public function getOptions($productId) {
        $stmt = $this->db->query("SELECT * FROM options_produits WHERE produit_id = :produit_id ORDER BY id ASC");
        $stmt->execute([':produit_id' => $productId]);
        return $stmt->fetchAll();
    }

    /**
     * Ajoute un produit et retourne son ID
     */
    public function add($nom, $description, $prix, $categorie_id, $image, $statut) {
        $stmt = $this->db->query("
            INSERT INTO produits (nom, description, prix, categorie_id, image, statut) 
            VALUES (:nom, :description, :prix, :categorie_id, :image, :statut)
        ");
        $stmt->execute([
            ':nom' => $nom,
            ':description' => $description,
            ':prix' => (float)$prix,
            ':categorie_id' => (int)$categorie_id,
            ':image' => $image,
            ':statut' => (int)$statut
        ]);
        return $this->db->getDbh()->lastInsertId();
    }

    /**
     * Modifie un produit existant
     */
    public function update($id, $nom, $description, $prix, $categorie_id, $image, $statut) {
        $sql = "UPDATE produits 
                SET nom = :nom, description = :description, prix = :prix, 
                    categorie_id = :categorie_id, statut = :statut";
        
        $params = [
            ':id' => $id,
            ':nom' => $nom,
            ':description' => $description,
            ':prix' => (float)$prix,
            ':categorie_id' => (int)$categorie_id,
            ':statut' => (int)$statut
        ];

        // Mettre à jour l'image uniquement si elle est spécifiée
        if ($image !== null) {
            $sql .= ", image = :image";
            $params[':image'] = $image;
        }

        $sql .= " WHERE id = :id";
        
        $stmt = $this->db->query($sql);
        return $stmt->execute($params);
    }

    /**
     * Supprime un produit
     */
    public function delete($id) {
        $stmt = $this->db->query("DELETE FROM produits WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }

    /**
     * Met à jour les options d'un produit (supprime et recrée)
     * 
     * @param int $productId
     * @param array $options Tableau de tableaux (chaque élément contenant 'nom_option' et 'prix_supplement')
     */
    public function saveOptions($productId, $options) {
        // Supprimer les anciennes options
        $stmtDel = $this->db->query("DELETE FROM options_produits WHERE produit_id = :produit_id");
        $stmtDel->execute([':produit_id' => $productId]);

        // Insérer les nouvelles options
        if (!empty($options)) {
            $stmtIns = $this->db->query("
                INSERT INTO options_produits (produit_id, nom_option, prix_supplement) 
                VALUES (:produit_id, :nom_option, :prix_supplement)
            ");
            
            foreach ($options as $option) {
                if (!empty($option['nom_option'])) {
                    $stmtIns->execute([
                        ':produit_id' => $productId,
                        ':nom_option' => $option['nom_option'],
                        ':prix_supplement' => (float)($option['prix_supplement'] ?? 0)
                    ]);
                }
            }
        }
        return true;
    }

    /**
     * Compte le nombre total de produits
     */
    public function count() {
        $stmt = $this->db->query("SELECT COUNT(*) as total FROM produits");
        $stmt->execute();
        $res = $stmt->fetch();
        return $res['total'] ?? 0;
    }
}
