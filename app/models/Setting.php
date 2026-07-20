<?php
/**
 * Modèle Setting - Gestion des paramètres généraux du site
 */
class Setting extends Model {
    
    /**
     * Récupère tous les paramètres sous forme de tableau associatif clé => valeur
     */
    public function getAll() {
        $stmt = $this->db->query("SELECT cle, valeur FROM parametres_site");
        $stmt->execute();
        $results = $stmt->fetchAll();
        
        $settings = [];
        foreach ($results as $row) {
            $settings[$row['cle']] = $row['valeur'];
        }
        return $settings;
    }

    /**
     * Met à jour ou insère une liste de paramètres
     * 
     * @param array $data Tableau associatif clé => valeur
     */
    public function saveAll($data) {
        $stmt = $this->db->query("INSERT INTO parametres_site (cle, valeur) 
                                 VALUES (:cle, :valeur) 
                                 ON DUPLICATE KEY UPDATE valeur = :valeur_update");
        
        foreach ($data as $key => $value) {
            $stmt->execute([
                ':cle' => $key,
                ':valeur' => $value,
                ':valeur_update' => $value
            ]);
        }
        return true;
    }
}
