<?php
/**
 * PageController - Contrôleur pour les pages statiques et informatives
 */
class PageController extends Controller {

    private function getSettings() {
        require_once APPROOT . '/models/Setting.php';
        $settingModel = new Setting();
        return $settingModel->getAll();
    }

    /**
     * Page À Propos
     */
    public function about() {
        $siteSettings = $this->getSettings();
        
        $data = [
            'title' => 'Qui sommes-nous ?',
            'activePage' => 'about',
            'siteSettings' => $siteSettings
        ];
        
        $this->render('about', $data);
    }

    /**
     * Page Contact
     */
    public function contact() {
        $siteSettings = $this->getSettings();
        
        $data = [
            'title' => 'Contactez-nous',
            'activePage' => 'contact',
            'siteSettings' => $siteSettings
        ];
        
        $this->render('contact', $data);
    }

    /**
     * Page Mentions Légales
     */
    public function legal() {
        $siteSettings = $this->getSettings();
        
        $data = [
            'title' => 'Mentions Légales',
            'activePage' => 'legal',
            'siteSettings' => $siteSettings
        ];
        
        $this->render('legal', $data);
    }

    /**
     * Page d'erreur 404
     */
    public function notFound() {
        $siteSettings = $this->getSettings();
        
        $data = [
            'title' => 'Page Non Trouvée (404)',
            'activePage' => '',
            'siteSettings' => $siteSettings
        ];
        
        $this->render('404', $data);
    }
}
