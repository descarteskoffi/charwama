<?php
/**
 * CartController - Contrôleur pour la gestion du panier et de la commande
 */
class CartController extends Controller {

    /**
     * Affiche la page du panier
     */
    public function index() {
        require_once APPROOT . '/models/Setting.php';
        $settingModel = new Setting();
        
        $siteSettings = $settingModel->getAll();

        $data = [
            'title' => 'Votre Panier de Commande',
            'activePage' => 'menu', // On garde l'onglet menu actif ou neutre
            'siteSettings' => $siteSettings
        ];

        $this->render('cart', $data);
    }
}
