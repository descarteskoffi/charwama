-- Script d'initialisation de la base de données Chawarma

CREATE DATABASE IF NOT EXISTS charwama_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE charwama_db;

-- 1. Table des Administrateurs
CREATE TABLE IF NOT EXISTS admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    mot_de_passe_hash VARCHAR(255) NOT NULL,
    date_creation DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- 2. Table des Catégories de produits
CREATE TABLE IF NOT EXISTS categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    ordre INT NOT NULL DEFAULT 0,
    statut TINYINT(1) NOT NULL DEFAULT 1 -- 1 = Actif, 0 = Inactif
) ENGINE=InnoDB;

-- 3. Table de Produits
CREATE TABLE IF NOT EXISTS produits (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(150) NOT NULL,
    description TEXT,
    prix DECIMAL(10, 2) NOT NULL,
    categorie_id INT NOT NULL,
    image VARCHAR(255) DEFAULT NULL,
    statut TINYINT(1) NOT NULL DEFAULT 1, -- 1 = Actif, 0 = Inactif (Rupture)
    date_creation DATETIME DEFAULT CURRENT_TIMESTAMP,
    date_modification DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (categorie_id) REFERENCES categories(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- 4. Table des Options / Suppléments de produits
CREATE TABLE IF NOT EXISTS options_produits (
    id INT AUTO_INCREMENT PRIMARY KEY,
    produit_id INT NOT NULL,
    nom_option VARCHAR(100) NOT NULL, -- ex: "Grand (+500 F)", "Supplément Fromage (+200 F)"
    prix_supplement DECIMAL(10, 2) NOT NULL DEFAULT 0.00,
    FOREIGN KEY (produit_id) REFERENCES produits(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- 5. Table des Paramètres Généraux du site
CREATE TABLE IF NOT EXISTS parametres_site (
    cle VARCHAR(100) PRIMARY KEY,
    valeur TEXT NOT NULL
) ENGINE=InnoDB;

-- 6. Table de Log de Connexions Administrateur (Sécurité)
CREATE TABLE IF NOT EXISTS logs_connexion (
    id INT AUTO_INCREMENT PRIMARY KEY,
    admin_id INT DEFAULT NULL,
    date_heure DATETIME DEFAULT CURRENT_TIMESTAMP,
    ip VARCHAR(45) NOT NULL,
    statut VARCHAR(50) NOT NULL -- "succes", "echec_mdp", "bloque"
) ENGINE=InnoDB;

-- 7. Table des Tables du Restaurant & QR Codes
CREATE TABLE IF NOT EXISTS tables_restaurant (
    id INT AUTO_INCREMENT PRIMARY KEY,
    numero_table VARCHAR(50) NOT NULL UNIQUE,
    nom_table VARCHAR(100) DEFAULT NULL,
    statut TINYINT(1) NOT NULL DEFAULT 1 -- 1 = Actif, 0 = Inactif
) ENGINE=InnoDB;

-- 8. Table des Commandes Clients
CREATE TABLE IF NOT EXISTS commandes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    numero_commande VARCHAR(50) NOT NULL UNIQUE,
    nom_client VARCHAR(150) NOT NULL,
    telephone VARCHAR(50) DEFAULT NULL,
    mode_reception VARCHAR(50) NOT NULL, -- "Sur place", "A emporter", "Livraison"
    table_numero VARCHAR(50) DEFAULT NULL,
    adresse_livraison TEXT DEFAULT NULL,
    notes TEXT DEFAULT NULL,
    prix_total DECIMAL(10, 2) NOT NULL DEFAULT 0.00,
    statut VARCHAR(50) NOT NULL DEFAULT 'Nouvelle commande', -- "Nouvelle commande", "Confirmée", "En préparation", "Prête", "En livraison", "Terminée", "Annulée"
    date_creation DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- 9. Table des Produits d'une Commande
CREATE TABLE IF NOT EXISTS commande_produits (
    id INT AUTO_INCREMENT PRIMARY KEY,
    commande_id INT NOT NULL,
    produit_id INT DEFAULT NULL,
    nom_produit VARCHAR(150) NOT NULL,
    prix_unitaire DECIMAL(10, 2) NOT NULL,
    quantite INT NOT NULL DEFAULT 1,
    options_texte TEXT DEFAULT NULL,
    FOREIGN KEY (commande_id) REFERENCES commandes(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- 10. Table des Horaires Hebdomadaires du Restaurant
CREATE TABLE IF NOT EXISTS horaires_restaurant (
    id INT AUTO_INCREMENT PRIMARY KEY,
    jour_semaine INT NOT NULL UNIQUE, -- 0 (Dimanche) à 6 (Samedi)
    ouvert TINYINT(1) NOT NULL DEFAULT 1,
    heure_ouverture TIME NOT NULL DEFAULT '11:30:00',
    heure_fermeture TIME NOT NULL DEFAULT '23:30:00'
) ENGINE=InnoDB;

-- =========================================================================
-- SEEDING : Insertion des données initiales par défaut
-- =========================================================================

-- Admin par défaut (admin@chawarma.com / admin123)
INSERT INTO admins (nom, email, mot_de_passe_hash) VALUES 
('Administratrice', 'admin@chawarma.com', '$2y$10$H5zoA2MfaqF.L3fsK0hjj.sNmX3ZM/0rIcbGXvsoUG18L2Rp1Lxzu');

-- Catégories par défaut
INSERT INTO categories (nom, ordre, statut) VALUES 
('Chawarmas', 1, 1),
('Paninis', 2, 1),
('Sandwichs', 3, 1),
('Boissons', 4, 1);

-- Produits par défaut
-- Catégorie Chawarmas (id: 1)
INSERT INTO produits (nom, description, prix, categorie_id, image, statut) VALUES 
('Chawarma Poulet Classic', 'Pain libanais croustillant garni de poulet mariné juteux, de crème d\'ail faite maison, de frites croustillantes et de cornichons.', 2500.00, 1, 'chawarma_poulet.webp', 1),
('Chawarma Viande Grillée', 'Émincé de bœuf mariné et grillé, sauce tahina (sésame), oignons au sumac, persil frais et tomates.', 3000.00, 1, 'chawarma_boeuf.webp', 1),
('Chawarma Mixte Special', 'Un mélange généreux de poulet et de bœuf mariné, sauce secrète de la maison, salade, tomates et frites.', 3500.00, 1, 'chawarma_mixte.webp', 1);

-- Catégorie Paninis (id: 2)
INSERT INTO produits (nom, description, prix, categorie_id, image, statut) VALUES 
('Panini Poulet Mozzarella', 'Pain panini grillé, filet de poulet rôti, mozzarella fondante, pesto de basilic maison et tomates séchées.', 2000.00, 2, 'panini_poulet.webp', 1),
('Panini Thon Fondant', 'Mélange de thon émietté, mayonnaise légère, cheddar fondant, rondelles d\'oignons rouges et olives noires.', 1800.00, 2, 'panini_thon.webp', 1),
('Panini 3 Fromages', 'Pour les amateurs de fromage : chèvre, mozzarella, cheddar fondu avec un filet de miel parfumé.', 1800.00, 2, 'panini_3fromages.webp', 1);

-- Catégorie Sandwichs (id: 3)
INSERT INTO produits (nom, description, prix, categorie_id, image, statut) VALUES 
('Sandwich Escalope Crème', 'Pain baguette artisanal, escalope de poulet tendre poêlée, champignons frais, crème fraîche onctueuse et fromage râpé.', 2500.00, 3, 'sandwich_escalope.webp', 1),
('Sandwich Américain Steak', 'Pain baguette garni d\'un double steak haché grillé, sauce burger maison, oignons caramélisés, salade, tomate et frites.', 2800.00, 3, 'sandwich_americain.webp', 1),
('Sandwich Kebab Traditionnel', 'Pain maison garni de viande kebab grillée à la broche, sauce blanche à la menthe, salade croquante, tomates et oignons.', 2500.00, 3, 'sandwich_kebab.webp', 1);

-- Catégorie Boissons (id: 4)
INSERT INTO produits (nom, description, prix, categorie_id, image, statut) VALUES 
('Coca-Cola Original', 'Canette de 33cl bien fraîche.', 500.00, 4, 'cocacola.webp', 1),
('Fanta Orange', 'Canette de 33cl bien fraîche.', 500.00, 4, 'fanta.webp', 1),
('Eau Minérale Naturelle', 'Bouteille de 50cl.', 300.00, 4, 'eau_minerale.webp', 1);

-- Options de produits
INSERT INTO options_produits (produit_id, nom_option, prix_supplement) VALUES
(1, 'Format XXL', 1000.00),
(1, 'Supplément Fromage fondu', 300.00),
(1, 'Sauce Algérienne extra', 0.00),
(2, 'Format XXL', 1200.00),
(2, 'Supplément double fromage', 400.00),
(4, 'Supplément frites à l\'intérieur', 200.00),
(4, 'Double mozzarella', 300.00),
(7, 'Supplément bacon', 400.00),
(7, 'Fromage cheddar', 200.00);

-- Tables du restaurant par défaut
INSERT INTO tables_restaurant (numero_table, nom_table, statut) VALUES
('1', 'Table 1 (Salle)', 1),
('2', 'Table 2 (Salle)', 1),
('3', 'Table 3 (Terrasse)', 1),
('4', 'Table 4 (Terrasse)', 1),
('5', 'Table VIP', 1);

-- Horaires par défaut de la semaine (0 = Dimanche à 6 = Samedi)
INSERT INTO horaires_restaurant (jour_semaine, ouvert, heure_ouverture, heure_fermeture) VALUES
(0, 1, '11:30:00', '23:30:00'),
(1, 1, '11:30:00', '23:30:00'),
(2, 1, '11:30:00', '23:30:00'),
(3, 1, '11:30:00', '23:30:00'),
(4, 1, '11:30:00', '23:30:00'),
(5, 1, '11:30:00', '00:30:00'),
(6, 1, '11:30:00', '00:30:00');

-- Paramètres Généraux
INSERT INTO parametres_site (cle, valeur) VALUES 
('whatsapp_phone', '2290165090839'),
('contact_email', 'contact@francofastfood.com'),
('contact_adresse', 'Cocotomey, Bénin'),
('site_horaires', 'Lundi au Dimanche de 11h30 à 23h30 non-stop'),
('lien_facebook', 'https://facebook.com/francofastfood'),
('lien_instagram', 'https://instagram.com/francofastfood'),
('accueil_hero_titre', 'Franco Fast-Food : L\'Expérience Gourmande Ultime !'),
('accueil_hero_soustitre', 'Succombez à nos recettes artisanales préparées à la minute : chawarmas juteux, paninis fondants et sandwichs croustillants. Commandez en 1 clic !'),
('accueil_a_propos_titre', 'La passion du goût chez Franco Fast-Food'),
('accueil_a_propos_description', 'Chez Franco fast-food, nous réinventons la restauration rapide avec des ingrédients frais, des sauces faites maison et un savoir-faire authentique.'),
('etat_ouverture_manuel', 'ouvert');
