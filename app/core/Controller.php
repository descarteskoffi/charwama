<?php
/**
 * Classe Controller de base
 */
class Controller {
    
    /**
     * Charge une vue
     * 
     * @param string $view Nom de la vue (ex: 'home' ou 'admin/dashboard')
     * @param array $data Données à passer à la vue
     */
    public function render($view, $data = []) {
        // Extraire les données sous forme de variables utilisables dans la vue
        extract($data);
        
        $viewFile = APPROOT . '/views/' . $view . '.php';
        
        if (file_exists($viewFile)) {
            require_once $viewFile;
        } else {
            // La vue n'existe pas
            die("La vue '{$view}' n'existe pas.");
        }
    }

    /**
     * Redirige vers une URL donnée
     */
    public function redirect($url) {
        $baseUrl = defined('DYNAMIC_URLROOT') && !empty(DYNAMIC_URLROOT) ? DYNAMIC_URLROOT : (defined('URLROOT') ? URLROOT : '');
        header('Location: ' . $baseUrl . '/' . ltrim($url, '/'));
        exit();
    }

    /**
     * Vérifie si la requête est en POST
     */
    public function isPost() {
        return $_SERVER['REQUEST_METHOD'] === 'POST';
    }

    /**
     * Vérifie si la requête est en GET
     */
    public function isGet() {
        return $_SERVER['REQUEST_METHOD'] === 'GET';
    }

    /**
     * Récupère et nettoie les données POST
     */
    public function getPostData() {
        $sanitized = [];
        if ($this->isPost()) {
            foreach ($_POST as $key => $value) {
                if (is_array($value)) {
                    $sanitized[$key] = array_map(function($val) {
                        return is_string($val) ? trim($val) : $val;
                    }, $value);
                } else {
                    $sanitized[$key] = is_string($value) ? trim($value) : $value;
                }
            }
        }
        return $sanitized;
    }

    /**
     * Génère un jeton CSRF et le stocke en session
     */
    public function generateCsrfToken() {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    /**
     * Valide un jeton CSRF
     */
    public function verifyCsrfToken($token) {
        if (!isset($_SESSION['csrf_token']) || empty($token)) {
            return false;
        }
        return hash_equals($_SESSION['csrf_token'], $token);
    }

    /**
     * Vérifie si l'administrateur est connecté
     */
    public function isAdminLoggedIn() {
        return isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
    }

    /**
     * Exige que l'administrateur soit connecté (sinon redirige vers login)
     */
    public function requireAdmin() {
        if (!$this->isAdminLoggedIn()) {
            $this->redirect('/admin/login');
        }
    }
}
