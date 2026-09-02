# 🛠️ Guide Utilisateur — Espace Administrateur

## Accès au Back-Office

**URL :** `https://votre-domaine.com/admin/login`

**Identifiants par défaut :**
- Login : `admin`
- Mot de passe : `Admin@2025!` *(à changer immédiatement après la première connexion)*

> ⚠️ **Changez votre mot de passe** dès la première connexion en contactant votre développeur ou via la base de données.

---

## 1. Tableau de Bord (Dashboard)

La page d'accueil de l'espace administrateur présente un **résumé instantané** :

| Carte statistique | Description |
|---|---|
| **Total Produits** | Nombre de produits dans le catalogue |
| **Total Catégories** | Nombre de catégories créées |
| **Connexions récentes** | Dernières connexions réussies (journal de sécurité) |

En bas de page, un tableau liste les **produits récemment ajoutés** avec leur image, nom, catégorie et prix.

---

## 2. Gestion des Catégories

**Menu :** Sidebar → 🏷️ Catégories

### Ajouter une catégorie

1. Cliquer sur le bouton **"+ Ajouter une catégorie"**
2. Remplir le formulaire :
   - **Nom** : Nom affiché dans les onglets du menu (ex: "Chawarma", "Panini")
   - **Description** *(optionnel)* : Courte description de la catégorie
3. Cliquer sur **"Enregistrer"**

La catégorie apparaîtra immédiatement dans les filtres du catalogue public.

### Modifier une catégorie

1. Dans la liste des catégories, cliquer sur l'icône ✏️ (crayon jaune)
2. Modifier les champs souhaités
3. Cliquer sur **"Mettre à jour"**

### Supprimer une catégorie

1. Cliquer sur l'icône 🗑️ (rouge) en face de la catégorie
2. Confirmer la suppression dans la fenêtre de confirmation

> ⚠️ **Attention** : Supprimer une catégorie supprimera automatiquement tous les produits associés. Cette action est irréversible.

---

## 3. Gestion des Produits

**Menu :** Sidebar → 🍔 Produits

### Ajouter un produit

1. Cliquer sur **"+ Ajouter un produit"**
2. Remplir le formulaire :

| Champ | Obligatoire | Description |
|-------|-------------|-------------|
| Nom du produit | ✅ | Nom affiché dans le catalogue |
| Description | ✅ | Description affichée dans la fiche produit |
| Prix (FCFA) | ✅ | Prix en francs CFA (entier, sans décimales) |
| Catégorie | ✅ | Sélectionner la catégorie parmi celles créées |
| Image | ❌ | Photo du plat (formats : JPG, PNG, WebP — max 5 Mo) |
| Produit actif | ❌ | Décocher pour masquer sans supprimer |

3. **Options / Suppléments** : Pour ajouter des options personnalisables (sauces, suppléments) :
   - Cliquer sur **"+ Ajouter une option"**
   - Renseigner le **nom de l'option** (ex: "Sauce harissa") et le **prix supplément** (0 si gratuit)
   - Répéter pour chaque option

4. Cliquer sur **"Enregistrer le produit"**

### Upload d'images

- Les images sont automatiquement **converties en WebP** et **redimensionnées** à 800px de large pour optimiser les performances
- Formats acceptés : `.jpg`, `.jpeg`, `.png`, `.webp`, `.gif`
- Taille maximale : **5 Mo**
- Astuce : utilisez des photos carrées ou en paysage pour un meilleur rendu

### Modifier un produit

1. Cliquer sur l'icône ✏️ en face du produit
2. Modifier les champs nécessaires
3. Pour changer l'image : télécharger une nouvelle photo (l'ancienne sera remplacée)
4. Cliquer sur **"Mettre à jour"**

### Supprimer un produit

1. Cliquer sur l'icône 🗑️
2. Confirmer la suppression

> L'image associée au produit sera également supprimée du serveur.

---

## 4. Paramètres du Site

**Menu :** Sidebar → ⚙️ Paramètres

Cette section vous permet de personnaliser les informations de votre restaurant **sans toucher au code**.

### Informations générales

| Paramètre | Description | Exemple |
|-----------|-------------|---------|
| **Titre du site** | Nom affiché dans l'onglet du navigateur | "Chawarma Chez Fatou" |
| **Numéro WhatsApp** | Numéro qui **reçoit** les commandes (format international sans espaces) | `+2250700000000` |
| **Adresse** | Adresse physique du restaurant | "12 Rue du Commerce, Abidjan" |
| **Horaires** | Horaires d'ouverture | "Lun - Sam : 10h - 22h" |
| **Email** | Email de contact affiché sur le site | "contact@chawarma.ci" |

### Contenu de la page d'accueil

| Paramètre | Description |
|-----------|-------------|
| **Titre héro** | Grande phrase d'accroche de la bannière principale |
| **Sous-titre héro** | Phrase secondaire sous le titre |
| **Titre "À propos"** | Titre de la section histoire sur l'accueil |
| **Description "À propos"** | Texte présenté dans la section histoire |

### Réseaux sociaux

| Paramètre | Description |
|-----------|-------------|
| **Lien Facebook** | URL complète de votre page Facebook |
| **Lien Instagram** | URL complète de votre compte Instagram |

Après toute modification, cliquer sur **"Enregistrer les paramètres"**.

---

## 5. Sécurité

### Protection anti-brute force

Le système bloque automatiquement les tentatives de connexion répétées :
- Après **5 tentatives** échouées depuis la même adresse IP, l'accès est bloqué pendant **15 minutes**

### Journal de connexion

Le tableau de bord affiche les dernières connexions réussies et échouées, permettant de détecter toute tentative d'accès non autorisée.

### Bonnes pratiques recommandées

- ✅ Utiliser un mot de passe fort (minimum 12 caractères, majuscules, chiffres, symboles)
- ✅ Se déconnecter après chaque session (`Sidebar → Se déconnecter`)
- ✅ Ne jamais partager les identifiants administrateur
- ✅ Changer le mot de passe tous les 3 mois
- ✅ Vérifier régulièrement le journal de connexion

---

## 6. Comment passent les commandes clients ?

1. Le client visite le site → consulte le catalogue → choisit ses produits
2. Les produits sont ajoutés à son **panier** (sauvegardé dans son navigateur)
3. Sur la page panier, le client saisit son nom et choisit entre **À emporter** ou **Livraison**
4. En cliquant **"Passer la commande sur WhatsApp"**, son application WhatsApp s'ouvre avec un **message pré-rempli** et structuré
5. Il envoie le message → vous le **recevez sur le numéro WhatsApp** configuré dans les paramètres
6. Vous traitez la commande manuellement et répondez au client

---

## 7. Conseils d'utilisation

### Bonnes pratiques catalogue

- 💡 **Nommez clairement** les produits (ex: "Chawarma Poulet Extra" plutôt que "Chawarma 1")
- 💡 **Rédigez des descriptions appétissantes** en mentionnant les ingrédients principaux
- 💡 **Utilisez de belles photos** bien éclairées : elles augmentent les commandes de 30%+
- 💡 **Masquez les produits** indisponibles plutôt que de les supprimer (décocher "Actif")
- 💡 **Créez des catégories logiques** : "Chawarma", "Panini", "Sandwichs", "Boissons", "Desserts"

### Gestion des prix

- Les prix sont en **FCFA (Francs CFA)** sans décimales
- Modifiez rapidement un prix en cliquant ✏️ sur le produit
- Les suppléments peuvent avoir un **prix à 0** pour les options gratuites

---

*Guide rédigé pour JP-Charwama — Version 1.0*  
*En cas de problème technique, contactez votre développeur.*
