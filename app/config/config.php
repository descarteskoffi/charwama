<?php
/**
 * Configuration globale de l'application
 */

// Configuration de la base de données
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'charwama_db');

// Chemins de l'application
define('APPROOT', dirname(dirname(__FILE__)));
define('URLROOT', ''); // Laisser vide pour la détection dynamique, ou spécifier (ex: http://localhost/chawarma)

// Informations générales par défaut
define('SITENAME', 'Franco fast-food');
define('DEFAULT_PHONE', '2290165090839'); // WhatsApp Bénin +229 0165090839

// Démarrage de la session de manière sécurisée
if (session_status() === PHP_SESSION_NONE) {
    // Empêcher l'accès aux cookies de session via JavaScript
    ini_set('session.cookie_httponly', 1);
    // Forcer l'utilisation de cookies de session uniquement (pas d'URL)
    ini_set('session.use_only_cookies', 1);
    
    // Si HTTPS est activé, forcer le cookie sécurisé
    if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') {
        ini_set('session.cookie_secure', 1);
    }
    
    session_start();
}
