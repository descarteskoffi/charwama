<?php
/**
 * MenuController - Contrôleur pour le catalogue et les fiches produits
 */
class MenuController extends Controller {

    /**
     * Affiche la page du catalogue complet
     */
    public function index() {
        require_once APPROOT . '/models/Setting.php';
        require_once APPROOT . '/models/Category.php';
        require_once APPROOT . '/models/Product.php';

        $settingModel = new Setting();
        $categoryModel = new Category();
        $productModel = new Product();

        $siteSettings = $settingModel->getAll();
        $categories = $categoryModel->getAllActive();
        
        // Récupérer la catégorie filtrée depuis l'URL s'il y en a une
        $selectedCatId = isset($_GET['cat']) ? (int)$_GET['cat'] : null;
        
        // Charger tous les produits actifs (filtrés ou non)
        $products = $productModel->getAllActive($selectedCatId);

        // Pour chaque produit, charger ses options à des fins de structure HTML rapide
        $productsWithOptions = [];
        foreach ($products as $product) {
            $product['options'] = $productModel->getOptions($product['id']);
            $productsWithOptions[] = $product;
        }

        $data = [
            'title' => 'Notre Menu',
            'activePage' => 'menu',
            'siteSettings' => $siteSettings,
            'categories' => $categories,
            'products' => $productsWithOptions,
            'selectedCatId' => $selectedCatId
        ];

        $this->render('menu', $data);
    }

    /**
     * Affiche la fiche détaillée d'un produit (ou retourne les données en JSON pour AJAX)
     * 
     * @param int $id ID du produit
     */
    public function show($id) {
        require_once APPROOT . '/models/Setting.php';
        require_once APPROOT . '/models/Product.php';

        $settingModel = new Setting();
        $productModel = new Product();

        $product = $productModel->getById($id);

        if (!$product || $product['statut'] != 1) {
            // Produit inexistant ou inactif
            if (isset($_GET['json']) || $this->isAjaxRequest()) {
                header('Content-Type: application/json');
                header('HTTP/1.0 404 Not Found');
                echo json_encode(['error' => 'Produit introuvable']);
                exit();
            }
            $this->redirect('/menu');
        }

        // Récupérer les options
        $options = $productModel->getOptions($id);
        $product['options'] = $options;

        // Si requête AJAX ou paramètre json=1, renvoyer du JSON
        if (isset($_GET['json']) || $this->isAjaxRequest()) {
            header('Content-Type: application/json');
            echo json_encode($product);
            exit();
        }

        // Sinon, charger la vue classique complète (fallbacks SEO)
        $siteSettings = $settingModel->getAll();
        $data = [
            'title' => $product['nom'],
            'activePage' => 'menu',
            'siteSettings' => $siteSettings,
            'product' => $product
        ];

        $this->render('product_detail', $data);
    }

    /**
     * Utilitaire pour détecter les requêtes AJAX
     */
    private function isAjaxRequest() {
        return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
               strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    }
}
