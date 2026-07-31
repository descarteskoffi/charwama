<?php
/**
 * Script de migration de la base de données
 * Ajoute les tables pour la gestion des commandes, des horaires et des tables QR codes.
 */

require_once __DIR__ . '/app/config/config.php';
require_once __DIR__ . '/app/core/Database.php';

try {
    $db = new Database();
    $dbh = $db->getDbh();
    
    echo "Connexion réussie à la base de données.\n";
    
    // 1. Table des tables du restaurant
    echo "Création de la table 'tables_restaurant'...\n";
    $dbh->exec("
        CREATE TABLE IF NOT EXISTS tables_restaurant (
            id INT AUTO_INCREMENT PRIMARY KEY,
            numero_table VARCHAR(50) NOT NULL UNIQUE,
            nom_table VARCHAR(100) NULL,
            statut TINYINT(1) NOT NULL DEFAULT 1
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");

    // 2. Table des horaires
    echo "Création de la table 'horaires_restaurant'...\n";
    $dbh->exec("
        CREATE TABLE IF NOT EXISTS horaires_restaurant (
            id INT AUTO_INCREMENT PRIMARY KEY,
            jour_semaine INT NOT NULL UNIQUE, -- 0 = Dimanche, 1 = Lundi, ..., 6 = Samedi
            ouvert TINYINT(1) NOT NULL DEFAULT 1,
            heure_ouverture TIME DEFAULT '11:30:00',
            heure_fermeture TIME DEFAULT '23:30:00'
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");

    // 3. Table des commandes
    echo "Création de la table 'commandes'...\n";
    $dbh->exec("
        CREATE TABLE IF NOT EXISTS commandes (
            id INT AUTO_INCREMENT PRIMARY KEY,
            numero_commande VARCHAR(50) NOT NULL UNIQUE,
            nom_client VARCHAR(150) NOT NULL,
            telephone VARCHAR(50) NOT NULL,
            mode_reception VARCHAR(50) NOT NULL,
            table_numero VARCHAR(50) NULL,
            adresse_livraison TEXT NULL,
            notes TEXT NULL,
            prix_total DECIMAL(10, 2) NOT NULL,
            statut VARCHAR(50) NOT NULL DEFAULT 'Nouvelle commande',
            date_creation DATETIME DEFAULT CURRENT_TIMESTAMP,
            date_modification DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");

    // 4. Table de liaison commande -> produits
    echo "Création de la table 'commande_produits'...\n";
    $dbh->exec("
        CREATE TABLE IF NOT EXISTS commande_produits (
            id INT AUTO_INCREMENT PRIMARY KEY,
            commande_id INT NOT NULL,
            produit_id INT NOT NULL,
            nom_produit VARCHAR(150) NOT NULL,
            prix_unitaire DECIMAL(10, 2) NOT NULL,
            quantite INT NOT NULL,
            options_texte TEXT NULL,
            FOREIGN KEY (commande_id) REFERENCES commandes(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");

    // 5. Insertion des horaires par défaut (si vide)
    $stmt = $dbh->query("SELECT COUNT(*) FROM horaires_restaurant");
    if ($stmt->fetchColumn() == 0) {
        echo "Insertion des horaires par défaut...\n";
        $stmtInsert = $dbh->prepare("
            INSERT INTO horaires_restaurant (jour_semaine, ouvert, heure_ouverture, heure_fermeture)
            VALUES (:jour, :ouvert, :ouverture, :fermeture)
        ");
        
        // Par défaut, ouvert tous les jours de 11h30 à 23h30
        for ($i = 0; $i <= 6; $i++) {
            $stmtInsert->execute([
                ':jour' => $i,
                ':ouvert' => 1,
                ':ouverture' => '11:30:00',
                ':fermeture' => '23:30:00'
            ]);
        }
    }

    // 6. Insertion des tables par défaut (si vide)
    $stmt = $dbh->query("SELECT COUNT(*) FROM tables_restaurant");
    if ($stmt->fetchColumn() == 0) {
        echo "Insertion de tables de restaurant par défaut (Tables 1 à 5)...\n";
        $stmtInsertTable = $dbh->prepare("
            INSERT INTO tables_restaurant (numero_table, nom_table, statut)
            VALUES (:num, :nom, 1)
        ");
        for ($i = 1; $i <= 5; $i++) {
            $stmtInsertTable->execute([
                ':num' => (string)$i,
                ':nom' => "Table " . $i
            ]);
        }
    }

    // 7. Paramètres d'ouverture manuelle dans 'parametres_site'
    $dbh->exec("
        INSERT IGNORE INTO parametres_site (cle, valeur) VALUES
        ('etat_ouverture_manuel', 'ouvert'),
        ('fermeture_exceptionnelle_date', ''),
        ('fermeture_temporaire_debut', ''),
        ('fermeture_temporaire_fin', '')
    ");

    echo "\n>>> MIGRATION RÉUSSIE ET APPLIQUÉE AVEC SUCCÈS <<<\n";

} catch (Exception $e) {
    echo "\nERREUR DE MIGRATION : " . $e->getMessage() . "\n";
    exit(1);
}
