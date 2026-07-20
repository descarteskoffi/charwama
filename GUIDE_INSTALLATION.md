# 📚 Guide d'Installation — Chawarma Premium

## Prérequis techniques

| Composant | Version minimale |
|-----------|-----------------|
| PHP       | 7.4+ (8.x recommandé) |
| MySQL / MariaDB | 5.7+ |
| Extension PDO (php-pdo) | ✅ requise |
| Extension GD (php-gd) | ✅ requise (conversion WebP) |
| mod_rewrite Apache | ✅ requis |
| HTTPS | Recommandé en production |

---

## 1. Déploiement des fichiers

### Option A — Hébergement mutualisé (ex: Alwaysdata, o2switch, Hostinger)

1. **Transférer les fichiers** via FTP/SFTP dans le répertoire web de votre hébergeur.
   - Exemple sur Alwaysdata : dans le dossier `www/` de votre compte.
   - Le dossier `public/` doit être la **racine web** accessible publiquement.

```
/votre-compte/
├── app/           ← NON accessible depuis le web
├── database.sql   ← NON accessible depuis le web
└── public/        ← Racine web publique (www/)
    ├── index.php
    ├── .htaccess
    └── assets/
```

> ⚠️ **Important** : Sur Alwaysdata, configurez le Document Root du site pour pointer vers le dossier `public/`. Cela se fait dans le panneau de contrôle : Sites → votre site → Modifier → "Répertoire racine" = `www/chawarma/public`

2. **Créer un dossier d'uploads** avec les bonnes permissions :

```bash
mkdir public/uploads
chmod 755 public/uploads
```

---

### Option B — Serveur VPS / Dédié (Apache)

1. Cloner ou déposer les fichiers dans `/var/www/chawarma/`

2. Configurer un VirtualHost Apache :

```apache
<VirtualHost *:80>
    ServerName votre-domaine.com
    DocumentRoot /var/www/chawarma/public

    <Directory /var/www/chawarma/public>
        AllowOverride All
        Require all granted
    </Directory>

    ErrorLog ${APACHE_LOG_DIR}/chawarma-error.log
    CustomLog ${APACHE_LOG_DIR}/chawarma-access.log combined
</VirtualHost>
```

3. Activer le site et mod_rewrite :

```bash
a2ensite chawarma.conf
a2enmod rewrite
systemctl reload apache2
```

---

## 2. Configuration de la base de données

### 2.1 Créer la base de données

Sur phpMyAdmin ou via CLI :

```sql
CREATE DATABASE chawarma_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'chawarma_user'@'localhost' IDENTIFIED BY 'mot_de_passe_fort';
GRANT ALL PRIVILEGES ON chawarma_db.* TO 'chawarma_user'@'localhost';
FLUSH PRIVILEGES;
```

### 2.2 Importer le schéma SQL

```bash
mysql -u chawarma_user -p chawarma_db < database.sql
```

Ou via phpMyAdmin : Importer → Choisir le fichier `database.sql` → Exécuter.

### 2.3 Modifier la configuration PHP

Ouvrir `app/config/config.php` et adapter les paramètres :

```php
<?php
// URL racine de l'application (laisser vide pour auto-détection)
define('URLROOT', '');                    // Ou 'https://votre-domaine.com'

// Base de données
define('DB_HOST', 'localhost');           // Hôte BDD (ex: 'mysql1.alwaysdata.net')
define('DB_NAME', 'chawarma_db');         // Nom de la BDD
define('DB_USER', 'chawarma_user');       // Utilisateur
define('DB_PASS', 'mot_de_passe_fort');   // Mot de passe

// Chemin racine de l'application
define('APPROOT', dirname(dirname(__FILE__)));

// Numéro WhatsApp par défaut (format international sans +)
define('DEFAULT_PHONE', '33600000000');
```

---

## 3. Sécurisation de l'installation

### 3.1 Désactiver l'affichage des erreurs PHP en production

Dans `public/index.php`, modifier les premières lignes :

```php
// En PRODUCTION — désactiver les erreurs
error_reporting(0);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/../logs/php_errors.log');
```

### 3.2 Protéger le dossier `app/` des accès directs

Le fichier `.htaccess` à la racine du projet protège déjà `app/`. Vérifier qu'il contient :

```apache
# Interdire l'accès direct au dossier app/
RedirectMatch 403 ^/app/.*
```

### 3.3 Sécuriser le dossier uploads

Ajouter un fichier `public/uploads/.htaccess` :

```apache
# Interdire l'exécution de scripts PHP dans le dossier uploads
<FilesMatch "\.(php|php3|php4|php5|phtml|pl|py|jsp|asp|htm|shtml|sh|cgi)$">
    Deny from all
</FilesMatch>
Options -Indexes
```

### 3.4 Changer le mot de passe administrateur

1. Se connecter sur `/admin/login`
2. Aller dans Paramètres → sécurité
3. **Ou** via SQL directement :

```sql
UPDATE admin SET mot_de_passe = SHA2('nouveau_mot_de_passe_fort', 256) WHERE id = 1;
```

---

## 4. Configuration sur Alwaysdata (Spécifique)

1. **Créer un site** : Administration → Sites → Ajouter un site
   - Type : Apache
   - Domaine : votre sous-domaine ou domaine personnalisé
   - Répertoire racine : `chawarma/public`

2. **Créer une BDD** : Administration → Bases de données → MySQL → Ajouter
   - Nom : `votre_identifiant_chawarma`
   - Note le nom d'hôte MySQL fourni (ex: `mysql1.alwaysdata.net`)

3. **Mettre à jour config.php** avec les identifiants Alwaysdata

4. **Vérifier que mod_rewrite** est activé (il l'est par défaut sur Alwaysdata)

---

## 5. Test de l'installation

Après déploiement, vérifier les points suivants :

| Test | URL | Résultat attendu |
|------|-----|-----------------|
| Page d'accueil | `https://votre-domaine.com/` | ✅ Hero banner visible |
| Page catalogue | `https://votre-domaine.com/menu` | ✅ Produits affichés |
| Panier | `https://votre-domaine.com/panier` | ✅ Panier vide ou avec articles |
| Admin login | `https://votre-domaine.com/admin/login` | ✅ Formulaire de connexion |
| Route 404 | `https://votre-domaine.com/inexistant` | ✅ Page 404 personnalisée |

---

## 6. Mises à jour futures

Pour mettre à jour le site :
1. Sauvegarder la BDD avant toute modification : `mysqldump chawarma_db > backup.sql`
2. Transférer les nouveaux fichiers par FTP
3. Appliquer les migrations SQL si nécessaires
4. Vider le cache navigateur et tester

---

*Guide rédigé pour Chawarma Premium — Version 1.0*
