<?php
/**
 * HomeController - Contrôleur pour la page d'accueil
 */
class HomeController extends Controller {

    public function index() {
        // Charger les modèles
        require_once APPROOT . '/models/Setting.php';
        require_once APPROOT . '/models/Category.php';
        require_once APPROOT . '/models/Product.php';
        
        $settingModel = new Setting();
        $categoryModel = new Category();
        $productModel = new Product();
        
        // Charger les paramètres généraux
        $siteSettings = $settingModel->getAll();
        
        // Charger les catégories actives
        $categories = $categoryModel->getAllActive();
        
        // Charger un aperçu de produits phares (par exemple les 6 premiers actifs)
        $allProducts = $productModel->getAllActive();
        $featuredProducts = array_slice($allProducts, 0, 6);
        
        // Composer les données pour la vue
        $data = [
            'title' => 'Accueil - Découvrez nos Spécialités',
            'activePage' => 'home',
            'siteSettings' => $siteSettings,
            'categories' => $categories,
            'featuredProducts' => $featuredProducts
        ];
        
        // Rendre la vue
        $this->render('home', $data);
    }
}
