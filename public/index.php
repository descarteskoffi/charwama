<?php
/**
 * Front Controller - Point d'entrée de l'application
 */

// Affichage des erreurs en développement
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Charger la configuration
require_once __DIR__ . '/../app/config/config.php';

// Requérir le noyau MVC
require_once __DIR__ . '/../app/core/Database.php';
require_once __DIR__ . '/../app/core/Router.php';
require_once __DIR__ . '/../app/core/Controller.php';
require_once __DIR__ . '/../app/core/Model.php';

// Traitement de l'URI pour supporter l'hébergement dans un sous-dossier
$scriptName = $_SERVER['SCRIPT_NAME']; // ex: /chawarma/public/index.php
$basePath = dirname($scriptName); // ex: /chawarma/public ou /chawarma (si rewrite direct)

// Si la base est "/", on le gère comme une chaîne vide pour le remplacement
if ($basePath === '/' || $basePath === '\\') {
    $basePath = '';
}

$requestUri = $_SERVER['REQUEST_URI']; // ex: /chawarma/public/menu?cat=1

// Retirer les paramètres d'URL (?cat=1) pour faire le remplacement du basePath
$requestPath = parse_url($requestUri, PHP_URL_PATH);

if (!empty($basePath) && strpos($requestPath, $basePath) === 0) {
    // Extraire uniquement la partie après le basePath
    $routePath = substr($requestPath, strlen($basePath));
} else {
    $routePath = $requestPath;
}

// Si la route est vide, forcer la racine
if (empty($routePath)) {
    $routePath = '/';
}

// S'assurer qu'il y a un "/" au début
if (strpos($routePath, '/') !== 0) {
    $routePath = '/' . $routePath;
}

// Définir URLROOT dynamiquement pour les assets si non défini dans config.php
if (defined('URLROOT') && URLROOT === '') {
    // Reconstruire l'URL racine
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'];
    define('DYNAMIC_URLROOT', $protocol . '://' . $host . $basePath);
} else {
    define('DYNAMIC_URLROOT', URLROOT);
}

// Initialiser le routeur
$router = new Router();

// Routes publiques
$router->add('/', 'HomeController', 'index');
$router->add('/menu', 'MenuController', 'index');
$router->add('/produit/{id}', 'MenuController', 'show');
$router->add('/panier', 'CartController', 'index');
$router->add('/a-propos', 'PageController', 'about');
$router->add('/contact', 'PageController', 'contact');
$router->add('/mentions-legales', 'PageController', 'legal');

// Routes administratives
$router->add('/admin/login', 'AdminController', 'login');
$router->add('/admin/logout', 'AdminController', 'logout');
$router->add('/admin/dashboard', 'AdminController', 'dashboard');

// CRUD Produits
$router->add('/admin/produits', 'AdminController', 'products');
$router->add('/admin/produits/ajouter', 'AdminController', 'addProduct');
$router->add('/admin/produits/modifier/{id}', 'AdminController', 'editProduct');
$router->add('/admin/produits/supprimer/{id}', 'AdminController', 'deleteProduct');

// CRUD Catégories
$router->add('/admin/categories', 'AdminController', 'categories');
$router->add('/admin/categories/ajouter', 'AdminController', 'addCategory');
$router->add('/admin/categories/modifier/{id}', 'AdminController', 'editCategory');
$router->add('/admin/categories/supprimer/{id}', 'AdminController', 'deleteCategory');

// Paramètres Généraux
$router->add('/admin/parametres', 'AdminController', 'settings');

// Dispatch de la requête
$router->dispatch($routePath);
