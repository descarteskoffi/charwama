<?php
/**
 * Routeur de l'application
 */
class Router {
    private $routes = [];

    /**
     * Ajoute une route au registre
     * 
     * @param string $route Pattern de la route (ex: '/menu', '/produit/{id}')
     * @param string $controller Nom du contrôleur (ex: 'MenuController')
     * @param string $action Nom de la méthode dans le contrôleur (ex: 'show')
     */
    public function add($route, $controller, $action) {
        // Convertit les variables comme {id} ou {slug} en regex de capture
        $routeRegex = preg_replace('/\{[a-zA-Z0-9-_]+\}/', '([a-zA-Z0-9-_]+)', $route);
        $routeRegex = '#^' . $routeRegex . '$#';
        
        $this->routes[] = [
            'route' => $route,
            'regex' => $routeRegex,
            'controller' => $controller,
            'action' => $action
        ];
    }

    /**
     * Dispatch la requête vers le bon contrôleur
     * 
     * @param string $url URL demandée
     */
    public function dispatch($url) {
        // Nettoyer l'URL des paramètres de requête (?page=1...)
        $url = parse_url($url, PHP_URL_PATH);
        
        // Retirer le slash de fin s'il existe (sauf pour la racine)
        if ($url !== '/' && substr($url, -1) === '/') {
            $url = rtrim($url, '/');
        }

        // Si l'URL est vide, on force la racine
        if (empty($url)) {
            $url = '/';
        }

        // Parcourir toutes les routes enregistrées
        foreach ($this->routes as $routeInfo) {
            if (preg_match($routeInfo['regex'], $url, $matches)) {
                // Retirer le premier élément qui contient toute la chaîne assortie
                array_shift($matches);
                
                $controllerName = $routeInfo['controller'];
                $actionName = $routeInfo['action'];

                // Inclure le contrôleur
                $controllerFile = APPROOT . '/controllers/' . $controllerName . '.php';
                if (file_exists($controllerFile)) {
                    require_once $controllerFile;
                    
                    if (class_exists($controllerName)) {
                        $controllerObj = new $controllerName();
                        
                        if (method_exists($controllerObj, $actionName)) {
                            // Appelle la méthode en lui passant les paramètres d'URL capturés
                            call_user_func_array([$controllerObj, $actionName], $matches);
                            return;
                        } else {
                            die("Action '{$actionName}' introuvable dans le contrôleur '{$controllerName}'.");
                        }
                    } else {
                        die("Classe '{$controllerName}' introuvable.");
                    }
                } else {
                    die("Fichier du contrôleur '{$controllerName}' introuvable.");
                }
            }
        }

        // Route non trouvée : Code 404
        header("HTTP/1.0 404 Not Found");
        
        // Charger un affichage 404 s'il existe, sinon message brut
        $errorControllerFile = APPROOT . '/controllers/PageController.php';
        if (file_exists($errorControllerFile)) {
            require_once $errorControllerFile;
            $pageController = new PageController();
            if (method_exists($pageController, 'notFound')) {
                $pageController->notFound();
                return;
            }
        }
        
        echo "<h1 style='text-align:center; margin-top: 100px; font-family:sans-serif;'>404 - Page non trouvée</h1>";
    }
}
