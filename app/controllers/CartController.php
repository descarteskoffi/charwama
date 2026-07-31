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
        require_once APPROOT . '/models/RestaurantTable.php';
        
        $settingModel = new Setting();
        $tableModel = new RestaurantTable();
        
        $siteSettings = $settingModel->getAll();
        $activeTables = $tableModel->getAllActive();

        $data = [
            'title' => 'Votre Panier de Commande',
            'activePage' => 'menu',
            'siteSettings' => $siteSettings,
            'activeTables' => $activeTables
        ];

        $this->render('cart', $data);
    }

    /**
     * Endpoint API pour créer une commande dans la base de données
     */
    public function createOrder() {
        header('Content-Type: application/json');

        // Récupérer le corps de la requête JSON
        $inputRaw = file_get_contents('php://input');
        $post = json_decode($inputRaw, true);

        if (!$post) {
            echo json_encode(['success' => false, 'error' => 'Données de requête invalides.']);
            exit();
        }

        // 1. Vérifier si le restaurant est ouvert
        require_once APPROOT . '/models/Schedule.php';
        $scheduleModel = new Schedule();
        $status = $scheduleModel->checkStatus();
        if (!$status['isOpen']) {
            echo json_encode(['success' => false, 'error' => 'Commande refusée : ' . $status['message']]);
            exit();
        }

        // 2. Extraire et valider les données de commande
        $clientName = trim($post['clientName'] ?? '');
        $telephone = trim($post['telephone'] ?? '');
        $modeReception = trim($post['orderMode'] ?? '');
        $tableNumero = trim($post['tableNumero'] ?? '');
        $adresseLivraison = trim($post['clientAddress'] ?? '');
        $notes = trim($post['orderNotes'] ?? '');
        $prixTotal = (float)($post['prixTotal'] ?? 0);
        $items = $post['items'] ?? [];

        if (empty($clientName)) {
            echo json_encode(['success' => false, 'error' => 'Le nom du client est requis.']);
            exit();
        }
        if (empty($telephone)) {
            echo json_encode(['success' => false, 'error' => 'Le numéro de téléphone est requis.']);
            exit();
        }
        if (empty($modeReception)) {
            echo json_encode(['success' => false, 'error' => 'Le mode de réception est requis.']);
            exit();
        }
        if (empty($items)) {
            echo json_encode(['success' => false, 'error' => 'Le panier est vide.']);
            exit();
        }

        // Vérifications de cohérence métier
        if ($modeReception === 'Sur place') {
            if (empty($tableNumero)) {
                echo json_encode(['success' => false, 'error' => 'Le numéro de table est requis pour la consommation sur place.']);
                exit();
            }
            // Vérifier que la table existe et est active
            require_once APPROOT . '/models/RestaurantTable.php';
            $tableModel = new RestaurantTable();
            $table = $tableModel->getByNumber($tableNumero);
            if (!$table || $table['statut'] != 1) {
                echo json_encode(['success' => false, 'error' => "La table numéro '$tableNumero' n'existe pas ou est inactive."]);
                exit();
            }
        } elseif ($modeReception === 'Livraison') {
            if (empty($adresseLivraison)) {
                echo json_encode(['success' => false, 'error' => 'L\'adresse de livraison est requise pour le mode livraison.']);
                exit();
            }
        }

        // 3. Insérer la commande via le modèle Order
        require_once APPROOT . '/models/Order.php';
        $orderModel = new Order();
        $res = $orderModel->create($clientName, $telephone, $modeReception, $tableNumero, $adresseLivraison, $notes, $prixTotal, $items);

        if ($res['success']) {
            echo json_encode([
                'success' => true,
                'numero_commande' => $res['numero_commande']
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'error' => 'Erreur lors de la sauvegarde en base de données : ' . $res['error']
            ]);
        }
        exit();
    }
}
