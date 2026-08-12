# Application de Gestion d'une École Privée

Cette application web est une solution digitale moderne pour la gestion administrative et pédagogique d'une école privée. Elle a été conçue pour être simple d'utilisation, sécurisée et basée sur le motif d'architecture **MVC (Modèle-Vue-Contrôleur)** avec PHP procédural/objet et MySQL.

## Technologies Utilisées

- **HTML5 & CSS3** (Mise en page moderne avec typographie Outfit)
- **Bootstrap 5** (Composants responsifs)
- **JavaScript & Chart.js** (Interactions dynamiques et graphiques)
- **PHP (Orienté Objet & PDO)**
- **Base de Données MySQL** (Avec requêtes préparées pour la sécurité)

## Rôles & Permissions (RBAC)

1. **Administrateur** (`admin` / `admin123`) : Gestion complète des classes, élèves, parents, enseignants, matières et comptabilité (transactions/reçus).
2. **Professeur** (`M.ismail` / `121314`) : Saisie des notes par matière, appel et registre des présences/absences.
3. **Parent** (`M.TAHIRI` / `123456`) : Consultation des notes de ses enfants, moyennes calculées, registre des absences (justifiées ou non) et historique des paiements.

---

## Installation & Configuration

1. **Serveur Local (WampServer, XAMPP, Laragon)** :
   Déplacez ce dossier `ecole_1` dans votre répertoire web (par exemple `C:\wamp64\www\dev_web\ecole_1\`).

2. **Base de Données** :
   - Assurez-vous que votre serveur MySQL est démarré.
   - L'application est intelligente ! **Elle crée et peuple automatiquement** la base de données `ecole_db` lors de votre première visite ou première connexion si celle-ci n'existe pas. Vous n'avez pas besoin d'importer manuellement de fichier `.sql`.

3. **Accès Web** :
   Ouvrez votre navigateur et accédez à :
   `http://localhost/dev_web/ecole_1/`

---

## Architecture MVC du Projet

- `config/Database.php` : Classe de connexion PDO configurant automatiquement la structure et insérant les données de démonstration.
- `index.php` : Contrôleur Frontal (Front Controller) qui route les requêtes HTTP vers le bon contrôleur en fonction de l'action demandée.
- `controllers/` : Contient la logique applicative (les actions possibles).
- `models/` : Contient la logique d'interaction sécurisée avec la base de données (requêtes préparées PDO).
- `views/` : Dossier contenant les fichiers d'affichage HTML/Bootstrap (les interfaces utilisateurs).
- `assets/` : Regroupe les fichiers CSS personnalisés et JS utilitaires.
- `schema.sql` : Script d'initialisation de la base de données (tables et relations).

## Mesures de Sécurité Appliquées

- **Injections SQL** : Utilisation systématique d'objets de requêtes préparées PDO avec typage d'arguments.
- **Faille XSS** : Nettoyage et échappement automatique de toutes les sorties à l'aide de filtres `htmlspecialchars`.
- **Vol de Session** : Cookies HTTP-Only activés, régénération de session après connexion et vérification dynamique du rôle en session.
- **Faille CSRF** : Génération d'un jeton (token) cryptographique unique lors de chaque session utilisateur pour valider la provenance de chaque formulaire POST soumis.
- **Sécurité des Mots de Passe** : Hachage fort unidirectionnel à l'aide de l'algorithme par défaut de PHP (`password_hash` avec Blowfish/Bcrypt).
