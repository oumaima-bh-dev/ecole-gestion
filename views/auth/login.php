<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - École Privée</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body class="login-body">
    <header class="public-header login-public-header">
        <nav class="public-nav container-fluid px-4 px-xl-5">
            <a class="public-brand" href="index.php" aria-label="Accueil École Privée">
                <span class="public-logo"><i class="fa-solid fa-book-open-reader"></i></span>
                <span>
                    <strong class="brand-title">École Privée</strong>
                    <small>Gestion Scolaire</small>
                </span>
            </a>

            <div class="public-menu">
                <a class="active" href="index.php">Accueil</a>
                <a href="index.php#services">Fonctionnalités</a>
                <a href="index.php#apropos">À propos</a>
                <a href="index.php#contact">Contact</a>
            </div>

            <div class="public-actions">
                <button class="btn public-search" type="button" aria-label="Rechercher">
                    <i class="fa-solid fa-magnifying-glass"></i>
                </button>
                <a href="index.php?controller=auth&action=login" class="btn btn-outline-primary">
                    Espace parents
                </a>
                <a href="index.php?controller=auth&action=login" class="btn btn-primary">
                    <i class="fa-solid fa-user me-2"></i>Se connecter
                </a>
            </div>
        </nav>
    </header>

    <main class="login-page">
        <section class="login-hero-panel">
            <div class="login-hero-copy">
                <span class="eyebrow hero-eyebrow">Une école mieux organisée, un avenir plus serein</span>
                <h1>Simplifiez la gestion, valorisez <span>l'éducation</span></h1>
                <p>
                    Une plateforme complète et intuitive pour gérer votre école : élèves, enseignants, classes, emplois du temps, notes, paiements... Tout en un seul espace, pour une école plus efficace et connectée.
                </p>
            </div>
        </section>

        <section class="login-form-panel">
            <div class="login-access-card">
                <div class="login-access-header">
                    <span class="public-logo"><i class="fa-solid fa-book-open-reader"></i></span>
                    <h1>Bienvenue <span>!</span></h1>
                    <p>Connectez-vous à votre espace<br>pour accéder à votre <span>tableau de bord.</span></p>
                </div>

                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger alert-dismissible fade show bg-danger text-white mb-4" role="alert">
                        <i class="fa-solid fa-triangle-exclamation me-2"></i>
                        <?php echo htmlspecialchars($error); ?>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <form action="index.php?controller=auth&action=login" method="POST">
                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

                    <div class="login-field">
                        <label for="username">Nom d'utilisateur</label>
                        <div class="login-input">
                            <i class="fa-solid fa-user"></i>
                            <input type="text" id="username" name="username" placeholder="Entrez votre nom d'utilisateur" required>
                        </div>
                    </div>

                    <div class="login-field">
                        <label for="password">Mot de passe</label>
                        <div class="login-input">
                            <i class="fa-solid fa-lock"></i>
                            <input type="password" id="password" name="password" placeholder="Entrez votre mot de passe" required>
                            <button type="button" aria-label="Afficher le mot de passe">
                                <i class="fa-solid fa-eye"></i>
                            </button>
                        </div>
                    </div>

                    <div class="login-options">
                        <label class="form-check m-0">
                            <input class="form-check-input" type="checkbox" value="1" id="remember">
                            <span class="form-check-label">Se souvenir de moi</span>
                        </label>
                        <a href="index.php">Mot de passe oublié ?</a>
                    </div>

                    <button type="submit" class="btn login-submit">
                        <i class="fa-solid fa-right-to-bracket me-2"></i>Se connecter
                    </button>
                </form>
            </div>
        </section>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/main.js"></script>
</body>
</html>
