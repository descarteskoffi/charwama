<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion Administration - JP-Charwama</title>
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Stylesheet -->
    <link rel="stylesheet" href="<?php echo DYNAMIC_URLROOT; ?>/assets/css/style.css">
</head>
<body class="admin-login-page">

    <div class="login-card form-card">
        <div class="login-header">
            <span class="tag" style="color: var(--primary); font-family: var(--font-titles); font-weight: 700; letter-spacing: 2px; text-transform: uppercase;">Restaurant</span>
            <h2><i class="fa-solid fa-lock" style="color: var(--primary);"></i> Accès Administration</h2>
        </div>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger">
                <i class="fa-solid fa-triangle-exclamation"></i> <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <form action="<?php echo DYNAMIC_URLROOT; ?>/admin/login" method="POST">
            <!-- Jeton CSRF de sécurité -->
            <input type="hidden" name="csrf_token" value="<?php echo $csrfToken; ?>">
            
            <div class="form-group">
                <label class="form-label" for="email">Adresse e-mail</label>
                <input type="email" id="email" name="email" class="form-control" placeholder="Entrez votre email..." required autofocus>
            </div>
            
            <div class="form-group">
                <label class="form-label" for="password">Mot de passe</label>
                <input type="password" id="password" name="password" class="form-control" placeholder="Entrez votre mot de passe..." required>
            </div>
            
            <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 16px;">
                <i class="fa-solid fa-right-to-bracket"></i> Se connecter
            </button>
        </form>
        
        <div style="text-align: center; margin-top: 24px; font-size: 0.85rem; color: #71717a;">
            <a href="<?php echo DYNAMIC_URLROOT; ?>/" style="text-decoration: underline;"><i class="fa-solid fa-arrow-left"></i> Retour au site public</a>
        </div>
    </div>

</body>
</html>
