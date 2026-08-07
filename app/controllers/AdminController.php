<?php
/**
 * AdminController - Gestionnaire du Back-Office
 */
class AdminController extends Controller {

    private $adminModel;
    private $categoryModel;
    private $productModel;
    private $settingModel;
    private $orderModel;
    private $tableModel;
    private $scheduleModel;

    public function __construct() {
        require_once APPROOT . '/models/Admin.php';
        require_once APPROOT . '/models/Category.php';
        require_once APPROOT . '/models/Product.php';
        require_once APPROOT . '/models/Setting.php';
        require_once APPROOT . '/models/Order.php';
        require_once APPROOT . '/models/RestaurantTable.php';
        require_once APPROOT . '/models/Schedule.php';

        $this->adminModel = new Admin();
        $this->categoryModel = new Category();
        $this->productModel = new Product();
        $this->settingModel = new Setting();
        $this->orderModel = new Order();
        $this->tableModel = new RestaurantTable();
        $this->scheduleModel = new Schedule();
    }

    /**
     * Connexion Administrateur
     */
    public function login() {
        if ($this->isAdminLoggedIn()) {
            $this->redirect('/admin/dashboard');
        }

        $error = '';
        $ip = $_SERVER['REMOTE_ADDR'];

        // Vérifier si l'IP est temporairement bloquée (brute force)
        $failedAttempts = $this->adminModel->countFailedAttempts($ip, 15);
        if ($failedAttempts >= 5) {
            $error = 'Trop de tentatives échouées. Votre adresse IP est bloquée pendant 15 minutes.';
        }

        if ($this->isPost()) {
            $post = $this->getPostData();

            // Valider le token CSRF
            if (!isset($post['csrf_token']) || !$this->verifyCsrfToken($post['csrf_token'])) {
                die("Erreur de validation de session (CSRF).");
            }

            if ($failedAttempts < 5) {
                $email = filter_var($post['email'], FILTER_VALIDATE_EMAIL);
                $password = $post['password'];

                if ($email && !empty($password)) {
                    $admin = $this->adminModel->getByEmail($email);

                    if ($admin) {
                        // Vérifier le mot de passe
                        if (password_verify($password, $admin['mot_de_passe_hash'])) {
                            // Succès de la connexion
                            $_SESSION['admin_logged_in'] = true;
                            $_SESSION['admin_id'] = $admin['id'];
                            $_SESSION['admin_name'] = $admin['nom'];
                            $_SESSION['last_activity'] = time(); // Pour déconnexion automatique

                            // Logger le succès
                            $this->adminModel->logAttempt($admin['id'], $ip, 'succes');
                            
                            $this->redirect('/admin/dashboard');
                        } else {
                            // Mauvais mot de passe
                            $this->adminModel->logAttempt($admin['id'], $ip, 'echec_mdp');
                            $error = 'Identifiants de connexion invalides.';
                        }
                    } else {
                        // Aucun compte trouvé
                        $this->adminModel->logAttempt(null, $ip, 'echec_mdp');
                        $error = 'Identifiants de connexion invalides.';
                    }
                } else {
                    $error = 'Veuillez remplir tous les champs correctement.';
                }
            }
        }

        // Renouveler / obtenir le token CSRF
        $csrfToken = $this->generateCsrfToken();

        $data = [
            'title' => 'Connexion Administration',
            'error' => $error,
            'csrfToken' => $csrfToken
        ];

        $this->render('admin/login', $data);
    }

    /**
     * Déconnexion Administrateur
     */
    public function logout() {
        unset($_SESSION['admin_logged_in']);
        unset($_SESSION['admin_id']);
        unset($_SESSION['admin_name']);
        unset($_SESSION['last_activity']);
        session_destroy();
        $this->redirect('/admin/login');
    }

    /**
     * Vérifie l'inactivité de session admin (30 minutes)
     */
    private function checkSessionTimeout() {
        $this->requireAdmin();
        
        if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > 1800)) {
            // Plus de 30 minutes d'inactivité
            $this->logout();
        }
        $_SESSION['last_activity'] = time(); // Mettre à jour l'activité
    }

    /**
     * Tableau de bord Admin
     */
    public function dashboard() {
        $this->checkSessionTimeout();

        $filter = $_GET['filter'] ?? 'today';
        $start = $_GET['start'] ?? '';
        $end = $_GET['end'] ?? '';

        // Obtenir les statistiques du modèle Order
        $stats = $this->orderModel->getStats($filter, $start, $end);
        
        $prodCount = $this->productModel->count();
        $catCount = $this->categoryModel->count();
        $tableCount = $this->tableModel->count();
        $recentLogs = $this->adminModel->getRecentLogs(5);

        $data = [
            'title' => 'Tableau de bord',
            'activePage' => 'dashboard',
            'prodCount' => $prodCount,
            'catCount' => $catCount,
            'tableCount' => $tableCount,
            'recentLogs' => $recentLogs,
            'stats' => $stats,
            'filter' => $filter,
            'start' => $start,
            'end' => $end
        ];

        $this->render('admin/dashboard', $data);
    }

    /**
     * =========================================================================
     * GESTION DES COMMANDES
     * =========================================================================
     */
    public function orders() {
        $this->checkSessionTimeout();

        $filters = [
            'statut' => $_GET['statut'] ?? '',
            'mode_reception' => $_GET['mode_reception'] ?? '',
            'search' => $_GET['search'] ?? '',
            'date_debut' => $_GET['date_debut'] ?? '',
            'date_fin' => $_GET['date_fin'] ?? ''
        ];

        $orders = $this->orderModel->getAll($filters);

        // Récupérer les articles pour chaque commande
        $ordersWithItems = [];
        foreach ($orders as $order) {
            $order['items'] = $this->orderModel->getItemsByOrderId($order['id']);
            $ordersWithItems[] = $order;
        }

        $csrfToken = $this->generateCsrfToken();

        $data = [
            'title' => 'Gestion des commandes',
            'activePage' => 'orders',
            'orders' => $ordersWithItems,
            'filters' => $filters,
            'csrfToken' => $csrfToken
        ];

        $this->render('admin/orders', $data);
    }

    /**
     * Modification du statut d'une commande
     */
    public function updateOrderStatus() {
        $this->checkSessionTimeout();

        if ($this->isPost()) {
            $post = $this->getPostData();

            // Validation CSRF
            if (!isset($post['csrf_token']) || !$this->verifyCsrfToken($post['csrf_token'])) {
                die("Erreur CSRF lors de la modification de la commande.");
            }

            $orderId = isset($post['order_id']) ? (int)$post['order_id'] : 0;
            $newStatus = $post['statut'] ?? '';

            $allowedStatus = ['Nouvelle commande', 'Confirmée', 'En préparation', 'Prête', 'En livraison', 'Terminée', 'Annulée'];
            if ($orderId > 0 && in_array($newStatus, $allowedStatus)) {
                $this->orderModel->updateStatus($orderId, $newStatus);
            }
        }

        $this->redirect('/admin/commandes');
    }

    /**
     * =========================================================================
     * GESTION DES HORAIRES D'OUVERTURE
     * =========================================================================
     */
    public function schedule() {
        $this->checkSessionTimeout();
        
        $error = '';
        $success = '';

        if ($this->isPost()) {
            $post = $this->getPostData();

            if (!isset($post['csrf_token']) || !$this->verifyCsrfToken($post['csrf_token'])) {
                die("Erreur CSRF lors de l'enregistrement des horaires.");
            }

            // Enregistrer l'état manuel
            $etatManuel = $post['etat_ouverture_manuel'] ?? 'ouvert';
            $this->settingModel->saveAll(['etat_ouverture_manuel' => $etatManuel]);

            // Enregistrer les exceptions de dates
            $excDate = $post['fermeture_exceptionnelle_date'] ?? '';
            $tempDebut = $post['fermeture_temporaire_debut'] ?? '';
            $tempFin = $post['fermeture_temporaire_fin'] ?? '';

            $this->settingModel->saveAll([
                'fermeture_exceptionnelle_date' => $excDate,
                'fermeture_temporaire_debut' => $tempDebut,
                'fermeture_temporaire_fin' => $tempFin
            ]);

            // Mettre à jour les horaires hebdomadaires
            for ($i = 0; $i <= 6; $i++) {
                $ouvert = isset($post["ouvert_$i"]) ? 1 : 0;
                $ouverture = $post["ouverture_$i"] ?? '11:30:00';
                $fermeture = $post["fermeture_$i"] ?? '23:30:00';

                $this->scheduleModel->updateDaySchedule($i, $ouvert, $ouverture, $fermeture);
            }

            $success = "Les horaires d'ouverture et paramètres ont été mis à jour avec succès.";
        }

        $weeklySchedules = $this->scheduleModel->getWeeklySchedules();
        $siteSettings = $this->settingModel->getAll();
        $csrfToken = $this->generateCsrfToken();

        $data = [
            'title' => "Gestion des Horaires d'Ouverture",
            'activePage' => 'schedule',
            'weeklySchedules' => $weeklySchedules,
            'siteSettings' => $siteSettings,
            'csrfToken' => $csrfToken,
            'error' => $error,
            'success' => $success
        ];

        $this->render('admin/schedule', $data);
    }

    /**
     * =========================================================================
     * GESTION DES TABLES (CRUD & QR CODES)
     * =========================================================================
     */
    public function tables() {
        $this->checkSessionTimeout();

        $tables = $this->tableModel->getAll();
        $csrfToken = $this->generateCsrfToken();
        $error = '';
        $success = '';

        $data = [
            'title' => 'Gestion des Tables & QR Codes',
            'activePage' => 'tables',
            'tables' => $tables,
            'csrfToken' => $csrfToken,
            'error' => $error,
            'success' => $success
        ];

        $this->render('admin/tables', $data);
    }

    public function addTable() {
        $this->checkSessionTimeout();

        if ($this->isPost()) {
            $post = $this->getPostData();

            if (!isset($post['csrf_token']) || !$this->verifyCsrfToken($post['csrf_token'])) {
                die("Erreur CSRF.");
            }

            $numero = $post['numero_table'] ?? '';
            $nom = $post['nom_table'] ?? '';
            $statut = isset($post['statut']) ? 1 : 0;

            if (!empty($numero)) {
                $existing = $this->tableModel->getByNumber($numero);
                if ($existing) {
                    $_SESSION['table_error'] = "La table numéro '$numero' existe déjà.";
                } else {
                    $this->tableModel->add($numero, $nom, $statut);
                    $_SESSION['table_success'] = "La table '$numero' a été ajoutée avec succès.";
                }
            } else {
                $_SESSION['table_error'] = "Le numéro de table est obligatoire.";
            }
        }

        $this->redirect('/admin/tables');
    }

    public function editTable() {
        $this->checkSessionTimeout();

        if ($this->isPost()) {
            $post = $this->getPostData();

            if (!isset($post['csrf_token']) || !$this->verifyCsrfToken($post['csrf_token'])) {
                die("Erreur CSRF.");
            }

            $id = isset($post['id']) ? (int)$post['id'] : 0;
            $numero = $post['numero_table'] ?? '';
            $nom = $post['nom_table'] ?? '';
            $statut = isset($post['statut']) ? 1 : 0;

            if ($id > 0 && !empty($numero)) {
                $existing = $this->tableModel->getByNumber($numero);
                if ($existing && $existing['id'] !== $id) {
                    $_SESSION['table_error'] = "Une autre table porte déjà le numéro '$numero'.";
                } else {
                    $this->tableModel->update($id, $numero, $nom, $statut);
                    $_SESSION['table_success'] = "La table a été modifiée avec succès.";
                }
            } else {
                $_SESSION['table_error'] = "Veuillez remplir tous les champs.";
            }
        }

        $this->redirect('/admin/tables');
    }

    public function deleteTable($id) {
        $this->checkSessionTimeout();

        $id = (int)$id;
        if ($id > 0) {
            $this->tableModel->delete($id);
            $_SESSION['table_success'] = "La table a été supprimée avec succès.";
        }

        $this->redirect('/admin/tables');
    }

    /**
     * =========================================================================
     * GESTION DES CATÉGORIES (CRUD)
     * =========================================================================
     */
    public function categories() {
        $this->checkSessionTimeout();

        $categories = $this->categoryModel->getAll();
        $csrfToken = $this->generateCsrfToken();
        $error = '';
        $success = '';

        $data = [
            'title' => 'Gestion des Catégories',
            'activePage' => 'categories',
            'categories' => $categories,
            'csrfToken' => $csrfToken,
            'error' => $error,
            'success' => $success
        ];

        $this->render('admin/categories/index', $data);
    }

    public function addCategory() {
        $this->checkSessionTimeout();

        if ($this->isPost()) {
            $post = $this->getPostData();

            if (!isset($post['csrf_token']) || !$this->verifyCsrfToken($post['csrf_token'])) {
                $_SESSION['error_msg'] = "Erreur de sécurité (CSRF). Veuillez réessayer.";
                $this->redirect('/admin/categories');
            }

            $nom = isset($post['nom']) ? trim($post['nom']) : '';
            $ordre = isset($post['ordre']) ? (int)$post['ordre'] : 0;
            $statut = isset($post['statut']) ? 1 : 0;

            if (!empty($nom)) {
                try {
                    $result = $this->categoryModel->add($nom, $ordre, $statut);
                    if ($result) {
                        $_SESSION['success_msg'] = "La catégorie '" . htmlspecialchars($nom) . "' a été créée avec succès.";
                    } else {
                        $_SESSION['error_msg'] = "Impossible de créer la catégorie dans la base de données.";
                    }
                } catch (Exception $e) {
                    $_SESSION['error_msg'] = "Erreur lors de la création de la catégorie : " . $e->getMessage();
                }
            } else {
                $_SESSION['error_msg'] = "Veuillez renseigner un nom de catégorie.";
            }
        }
        $this->redirect('/admin/categories');
    }

    public function editCategory($id) {
        $this->checkSessionTimeout();
        $id = (int)$id;
        $category = $this->categoryModel->getById($id);

        if (!$category) {
            $_SESSION['error_msg'] = "Catégorie introuvable.";
            $this->redirect('/admin/categories');
        }

        $error = '';

        if ($this->isPost()) {
            $post = $this->getPostData();

            if (!isset($post['csrf_token']) || !$this->verifyCsrfToken($post['csrf_token'])) {
                $_SESSION['error_msg'] = "Erreur de sécurité (CSRF). Veuillez réessayer.";
                $this->redirect('/admin/categories');
            }

            $nom = isset($post['nom']) ? trim($post['nom']) : '';
            $ordre = isset($post['ordre']) ? (int)$post['ordre'] : 0;
            $statut = isset($post['statut']) ? 1 : 0;

            if (!empty($nom)) {
                try {
                    $this->categoryModel->update($id, $nom, $ordre, $statut);
                    $_SESSION['success_msg'] = "La catégorie a été modifiée avec succès.";
                    $this->redirect('/admin/categories');
                } catch (Exception $e) {
                    $error = "Erreur lors de la modification : " . $e->getMessage();
                }
            } else {
                $error = "Le nom ne peut pas être vide.";
            }
        }

        $csrfToken = $this->generateCsrfToken();
        $data = [
            'title' => 'Modifier la Catégorie',
            'activePage' => 'categories',
            'category' => $category,
            'csrfToken' => $csrfToken,
            'error' => $error
        ];

        $this->render('admin/categories/edit', $data);
    }

    public function deleteCategory($id) {
        $this->checkSessionTimeout();
        
        $id = (int)$id;
        $category = $this->categoryModel->getById($id);
        if ($category) {
            try {
                $this->categoryModel->delete($id);
                $_SESSION['success_msg'] = "La catégorie a été supprimée.";
            } catch (Exception $e) {
                $_SESSION['error_msg'] = "Impossible de supprimer cette catégorie car elle est utilisée par des produits.";
            }
        }
        $this->redirect('/admin/categories');
    }

    /**
     * =========================================================================
     * GESTION DES PRODUITS (CRUD)
     * =========================================================================
     */
    public function products() {
        $this->checkSessionTimeout();

        $products = $this->productModel->getAll();

        $data = [
            'title' => 'Gestion des Produits',
            'activePage' => 'products',
            'products' => $products
        ];

        $this->render('admin/products/index', $data);
    }

    public function addProduct() {
        $this->checkSessionTimeout();
        $categories = $this->categoryModel->getAll();
        $error = '';

        if ($this->isPost()) {
            $post = $this->getPostData();

            if (!isset($post['csrf_token']) || !$this->verifyCsrfToken($post['csrf_token'])) {
                die("Erreur CSRF");
            }

            $nom = trim($post['nom']);
            $description = trim($post['description']);
            $prix = parseFloatOrZero($post['prix']);
            $categorie_id = (int)$post['categorie_id'];
            $statut = isset($post['statut']) ? 1 : 0;

            // Traitement de l'image
            $imageFilename = null;
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $targetDir = APPROOT . '/../public/uploads';
                $uploadResult = $this->processImage($_FILES['image'], $targetDir);
                
                if (isset($uploadResult['filename'])) {
                    $imageFilename = $uploadResult['filename'];
                } else {
                    $error = $uploadResult['error'];
                }
            }

            if (empty($error) && !empty($nom) && $prix > 0 && $categorie_id > 0) {
                // Ajouter le produit
                $productId = $this->productModel->add($nom, $description, $prix, $categorie_id, $imageFilename, $statut);
                
                // Gérer les options/suppléments s'il y en a
                $options = [];
                if (isset($_POST['option_nom']) && is_array($_POST['option_nom'])) {
                    for ($i = 0; $i < count($_POST['option_nom']); $i++) {
                        $optNom = htmlspecialchars(trim($_POST['option_nom'][$i]), ENT_QUOTES, 'UTF-8');
                        $optPrix = isset($_POST['option_prix'][$i]) ? (float)$_POST['option_prix'][$i] : 0;
                        if (!empty($optNom)) {
                            $options[] = ['nom_option' => $optNom, 'prix_supplement' => $optPrix];
                        }
                    }
                }
                $this->productModel->saveOptions($productId, $options);

                $_SESSION['success_msg'] = "Le produit '{$nom}' a été ajouté avec succès.";
                $this->redirect('/admin/produits');
            } else {
                if (empty($error)) {
                    $error = "Veuillez remplir les informations obligatoires (Nom, Prix et Catégorie).";
                }
            }
        }

        $csrfToken = $this->generateCsrfToken();
        $data = [
            'title' => 'Ajouter un Produit',
            'activePage' => 'products',
            'categories' => $categories,
            'csrfToken' => $csrfToken,
            'error' => $error
        ];

        $this->render('admin/products/create', $data);
    }

    public function editProduct($id) {
        $this->checkSessionTimeout();
        $product = $this->productModel->getById($id);
        
        if (!$product) {
            $this->redirect('/admin/produits');
        }

        $categories = $this->categoryModel->getAll();
        $options = $this->productModel->getOptions($id);
        $error = '';

        if ($this->isPost()) {
            $post = $this->getPostData();

            if (!isset($post['csrf_token']) || !$this->verifyCsrfToken($post['csrf_token'])) {
                die("Erreur CSRF");
            }

            $nom = trim($post['nom']);
            $description = trim($post['description']);
            $prix = parseFloatOrZero($post['prix']);
            $categorie_id = (int)$post['categorie_id'];
            $statut = isset($post['statut']) ? 1 : 0;

            // Traitement de l'image
            $imageFilename = null;
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $targetDir = APPROOT . '/../public/uploads';
                $uploadResult = $this->processImage($_FILES['image'], $targetDir);
                
                if (isset($uploadResult['filename'])) {
                    $imageFilename = $uploadResult['filename'];
                    // Supprimer l'ancienne image si elle existe
                    if (!empty($product['image'])) {
                        $oldImagePath = $targetDir . '/' . $product['image'];
                        if (file_exists($oldImagePath)) {
                            unlink($oldImagePath);
                        }
                    }
                } else {
                    $error = $uploadResult['error'];
                }
            }

            if (empty($error) && !empty($nom) && $prix > 0 && $categorie_id > 0) {
                // Modifier le produit
                $this->productModel->update($id, $nom, $description, $prix, $categorie_id, $imageFilename, $statut);
                
                // Mettre à jour les options
                $newOptions = [];
                if (isset($_POST['option_nom']) && is_array($_POST['option_nom'])) {
                    for ($i = 0; $i < count($_POST['option_nom']); $i++) {
                        $optNom = htmlspecialchars(trim($_POST['option_nom'][$i]), ENT_QUOTES, 'UTF-8');
                        $optPrix = isset($_POST['option_prix'][$i]) ? (float)$_POST['option_prix'][$i] : 0;
                        if (!empty($optNom)) {
                            $newOptions[] = ['nom_option' => $optNom, 'prix_supplement' => $optPrix];
                        }
                    }
                }
                $this->productModel->saveOptions($id, $newOptions);

                $_SESSION['success_msg'] = "Le produit a été mis à jour avec succès.";
                $this->redirect('/admin/produits');
            } else {
                if (empty($error)) {
                    $error = "Veuillez remplir les informations obligatoires.";
                }
            }
        }

        $csrfToken = $this->generateCsrfToken();
        $data = [
            'title' => 'Modifier le Produit',
            'activePage' => 'products',
            'product' => $product,
            'categories' => $categories,
            'options' => $options,
            'csrfToken' => $csrfToken,
            'error' => $error
        ];

        $this->render('admin/products/edit', $data);
    }

    public function deleteProduct($id) {
        $this->checkSessionTimeout();
        $product = $this->productModel->getById($id);
        
        if ($product) {
            // Supprimer son image
            if (!empty($product['image'])) {
                $imagePath = APPROOT . '/../public/uploads/' . $product['image'];
                if (file_exists($imagePath)) {
                    unlink($imagePath);
                }
            }
            $this->productModel->delete($id);
            $_SESSION['success_msg'] = "Le produit a été supprimé.";
        }
        $this->redirect('/admin/produits');
    }

    /**
     * =========================================================================
     * CONFIGURATION / PARAMÈTRES (WhatsApp, Horaires, etc.)
     * =========================================================================
     */
    public function settings() {
        $this->checkSessionTimeout();
        $error = '';
        $success = '';

        if ($this->isPost()) {
            $post = $this->getPostData();

            if (!isset($post['csrf_token']) || !$this->verifyCsrfToken($post['csrf_token'])) {
                die("Erreur CSRF");
            }

            // Filtrer et sauvegarder
            $settingsToSave = [
                'whatsapp_phone' => trim($post['whatsapp_phone']),
                'contact_email' => trim($post['contact_email']),
                'contact_adresse' => trim($post['contact_adresse']),
                'site_horaires' => trim($post['site_horaires']),
                'lien_facebook' => trim($post['lien_facebook']),
                'lien_instagram' => trim($post['lien_instagram']),
                'accueil_hero_titre' => trim($post['accueil_hero_titre']),
                'accueil_hero_soustitre' => trim($post['accueil_hero_soustitre']),
                'accueil_a_propos_titre' => trim($post['accueil_a_propos_titre']),
                'accueil_a_propos_description' => trim($post['accueil_a_propos_description'])
            ];

            if (!empty($settingsToSave['whatsapp_phone'])) {
                $this->settingModel->saveAll($settingsToSave);
                $success = "Les paramètres généraux ont été mis à jour.";
            } else {
                $error = "Le numéro WhatsApp de commande est obligatoire.";
            }
        }

        $siteSettings = $this->settingModel->getAll();
        $csrfToken = $this->generateCsrfToken();

        $data = [
            'title' => 'Paramètres du Site',
            'activePage' => 'settings',
            'siteSettings' => $siteSettings,
            'csrfToken' => $csrfToken,
            'error' => $error,
            'success' => $success
        ];

        $this->render('admin/settings/index', $data);
    }

    /**
     * =========================================================================
     * UTILITAIRES INTERNES : TRAITEMENT ET CONVERSION WEBP DES IMAGES
     * =========================================================================
     */
    private function processImage($file, $targetDir, $maxWidth = 800) {
        $tempPath = $file['tmp_name'];
        
        // Validation du type MIME réel
        if (!class_exists('finfo')) {
            return ['error' => 'La bibliothèque PHP Fileinfo est désactivée. Impossible de valider l\'image.'];
        }
        
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mimeType = $finfo->file($tempPath);
        
        $allowedMimeTypes = ['image/jpeg', 'image/png', 'image/webp'];
        if (!in_array($mimeType, $allowedMimeTypes)) {
            return ['error' => 'Format de fichier non supporté. Autorisé : JPG, PNG, WEBP.'];
        }

        // Vérification de la taille (max 2 Mo)
        if ($file['size'] > 2 * 1024 * 1024) {
            return ['error' => 'L\'image est trop lourde (limite de 2 Mo dépassée).'];
        }

        // Charger l'image selon son type en utilisant la bibliothèque GD
        if (!function_exists('imagecreatefromjpeg')) {
            return ['error' => 'La bibliothèque PHP GD n\'est pas activée sur le serveur.'];
        }

        switch ($mimeType) {
            case 'image/jpeg':
                $image = imagecreatefromjpeg($tempPath);
                break;
            case 'image/png':
                $image = imagecreatefrompng($tempPath);
                imagealphablending($image, true);
                imagesavealpha($image, true);
                break;
            case 'image/webp':
                $image = imagecreatefromwebp($tempPath);
                break;
            default:
                return ['error' => 'Erreur lors de la lecture du fichier image.'];
        }

        if (!$image) {
            return ['error' => 'Fichier image corrompu ou invalide.'];
        }

        // Dimensions originales
        $width = imagesx($image);
        $height = imagesy($image);
        
        // Redimensionnement si trop large
        if ($width > $maxWidth) {
            $newWidth = $maxWidth;
            $newHeight = (int)floor($height * ($maxWidth / $width));
            
            $resizedImage = imagecreatetruecolor($newWidth, $newHeight);
            
            // Gérer la transparence pour le PNG/WEBP
            imagealphablending($resizedImage, false);
            imagesavealpha($resizedImage, true);
            $transparent = imagecolorallocatealpha($resizedImage, 255, 255, 255, 127);
            imagefill($resizedImage, 0, 0, $transparent);
            
            imagecopyresampled($resizedImage, $image, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
            imagedestroy($image);
            $image = $resizedImage;
        }

        // Générer un nom unique aléatoire
        $filename = uniqid('prod_', true) . '.webp';
        $outputPath = $targetDir . '/' . $filename;

        // Créer le dossier s'il n'existe pas
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0755, true);
        }

        // Sauvegarder l'image convertie en WebP (qualité 80%)
        if (imagewebp($image, $outputPath, 80)) {
            imagedestroy($image);
            return ['filename' => $filename];
        } else {
            imagedestroy($image);
            return ['error' => 'Erreur lors de l\'écriture du fichier WebP sur le disque.'];
        }
    }
}

/**
 * Helper de nettoyage de prix décimal
 */
function parseFloatOrZero($val) {
    $cleaned = str_replace(',', '.', $val);
    $cleaned = preg_replace('/[^0-9.]/', '', $cleaned);
    return !empty($cleaned) ? (float)$cleaned : 0.00;
}
