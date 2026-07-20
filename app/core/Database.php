<?php
/**
 * Classe Database - Gestion de la connexion MySQL via PDO
 */
class Database {
    private $host = DB_HOST;
    private $user = DB_USER;
    private $pass = DB_PASS;
    private $dbname = DB_NAME;

    private $dbh;
    private $error;

    public function __construct() {
        // Définir le DSN (Data Source Name)
        $dsn = 'mysql:host=' . $this->host . ';dbname=' . $this->dbname . ';charset=utf8mb4';
        
        $options = [
            PDO::ATTR_PERSISTENT => true,
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8mb4'
        ];

        // Créer une instance de PDO
        try {
            $this->dbh = new PDO($dsn, $this->user, $this->pass, $options);
        } catch (PDOException $e) {
            $this->error = $e->getMessage();
            // En production, il faudrait masquer le message précis, mais ici on le loggue ou l'affiche pour le dev
            die("Erreur de connexion à la base de données : " . $this->error);
        }
    }

    /**
     * Retourne l'instance PDO brute si besoin de faire des requêtes avancées
     */
    public function getDbh() {
        return $this->dbh;
    }

    /**
     * Prépare une requête SQL
     */
    public function query($sql) {
        return $this->dbh->prepare($sql);
    }
}
