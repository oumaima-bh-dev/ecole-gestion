<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>École Privée - Gestion scolaire</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body class="public-body">
    <header class="public-header">
        <nav class="public-nav container">
            <a class="public-brand" href="index.php" aria-label="Accueil École Privée">
                <span class="public-logo"><i class="fa-solid fa-book-open-reader"></i></span>
                <span>
                    <strong class="brand-title">École Privée</strong>
                    <small>Gestion Scolaire</small>
                </span>
            </a>

            <div class="public-menu">
                <a class="active" href="index.php">Accueil</a>
                <a href="#services">Fonctionnalités</a>
                <a href="#apropos">À propos</a>
                <a href="#contact">Contact</a>
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

    <main>
        <section class="public-hero">
            <div class="container">
                <div class="hero-layout">
                    <div class="hero-copy">
                        <span class="eyebrow hero-eyebrow">Une école mieux organisée, un avenir plus serein</span>
                        <h1>Simplifiez la gestion, valorisez <span>l'éducation</span></h1>
                        <p class="lead">
                            Une plateforme complète et intuitive pour gérer votre école privée : élèves, enseignants, classes, emplois du temps, notes, paiements... Tout en un seul espace, pour une école plus efficace et connectée.
                        </p>
                        <div class="d-flex flex-wrap gap-3 mt-4">
                            <a href="index.php?controller=auth&action=login" class="btn btn-primary btn-lg">
                                <i class="fa-solid fa-arrow-right me-2"></i>Accéder à mon espace
                            </a>
                            <a href="#services" class="btn btn-outline-primary btn-lg">
                                <i class="fa-solid fa-circle-play me-2"></i>Découvrir la plateforme
                            </a>
                        </div>
                    </div>

                     
                     
                        <div class="hero-hand-note">
                            Ensemble pour la réussite de chaque élève
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="public-section" id="services">
            <div class="container">
                <div class="section-heading">
                    <span class="eyebrow">Une solution complète</span>
                    <h2>Tout gérer depuis une seule plateforme</h2>
                    <p>Une interface simple, moderne et adaptée aux besoins des écoles privées</p>
                </div>

                <div class="features-grid">
                    <article class="public-card">
                        <i class="fa-solid fa-users bg-primary-soft"></i>
                        <h3>Suivi des élèves</h3>
                        <p>Dossiers complets, classes, parents associés et statut en un clic.</p>
                    </article>
                    <article class="public-card">
                        <i class="fa-solid fa-chalkboard-user bg-success-soft"></i>
                        <h3>Espace enseignant</h3>
                        <p>Saisie des notes, gestion des présences et suivi des classes.</p>
                    </article>
                    <article class="public-card">
                        <i class="fa-solid fa-calendar-days bg-purple-soft"></i>
                        <h3>Emplois du temps</h3>
                        <p>Organisation simple des cours et des matières par classe.</p>
                    </article>
                    <article class="public-card">
                        <i class="fa-solid fa-coins bg-warning-soft"></i>
                        <h3>Paiements en DH</h3>
                        <p>Suivi des frais scolaires, historique des paiements et reçus imprimables.</p>
                    </article>
                    <article class="public-card">
                        <i class="fa-solid fa-chart-simple bg-pink-soft"></i>
                        <h3>Rapports & statistiques</h3>
                        <p>Vue claire sur les effectifs, les résultats et l'activité de l'école.</p>
                    </article>
                    <article class="public-card">
                        <i class="fa-solid fa-shield-halved bg-success-soft"></i>
                        <h3>Accès sécurisé</h3>
                        <p>Des espaces dédiés pour l'administration, les enseignants et les parents.</p>
                    </article>
                </div>
            </div>
        </section>

        <section class="school-band" id="apropos">
            <div class="container">
                <div class="school-band-inner">
                    <div class="school-illustration" aria-hidden="true">
                        <i class="fa-solid fa-school-flag"></i>
                    </div>
                    <div class="school-band-title">
                        <strong>Une école bien gérée,</strong>
                        <span>des élèves qui avancent !</span>
                    </div>
                    <div class="school-stat">
                        <i class="fa-solid fa-graduation-cap"></i>
                        <strong>320+</strong>
                        <span>Élèves accompagnés</span>
                    </div>
                    <div class="school-stat">
                        <i class="fa-solid fa-building-columns"></i>
                        <strong>24</strong>
                        <span>Classes actives</span>
                    </div>
                    <div class="school-stat">
                        <i class="fa-solid fa-users"></i>
                        <strong>18</strong>
                        <span>Enseignants engagés</span>
                    </div>
                    <div class="school-stat">
                        <i class="fa-solid fa-star"></i>
                        <strong>100%</strong>
                        <span>Parents satisfaits</span>
                    </div>
                </div>
            </div>
        </section>

        <section class="public-section visually-hidden" id="contact">
            <h2>Contact</h2>
        </section>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
