# FitConnect

FitConnect est une application de gestion pour un réseau de 4 salles de sport. Elle permet d'enregistrer les adhérents, gérer leurs abonnements et tracer leurs séances d'entraînement selon des règles métier précises.

## Fonctionnalités principales

*   **Gestion des Adhérents :** Inscription, modification, et suivi des membres répartis dans les 4 salles.
*   **Gestion des Abonnements :** Attribution d'abonnements (Mensuel, Trimestriel, Annuel) avec gestion automatique de l'expiration. *Règle métier : un seul abonnement actif par adhérent.*
*   **Gestion des Séances :** Enregistrement des visites, choix de l'activité (Cardio, Musculation, etc.) et de l'équipement. *Règle métier : l'enregistrement d'une séance requiert un abonnement valide à la date du jour.*
*   **Tableau de bord (Dashboard) :** Vue d'ensemble des statistiques du réseau (nombre d'adhérents, abonnements actifs, répartition des séances par salle et par activité).

## Architecture Technique

L'application est construite en PHP pur (sans framework) selon une architecture en couches pour une meilleure maintenabilité :

1.  **Entities (`app/Entities/`) :** Classes métier pures (`Adherent`, `Abonnement`, `Seance`) encapsulant les données avec des getters/setters.
2.  **Repositories (`app/Repositories/`) :** Gèrent exclusivement les requêtes SQL (avec PDO paramétré pour éviter les injections).
3.  **Services (`app/Services/`) :** Contiennent la logique métier (ex: vérification de la validité d'un abonnement avant l'enregistrement d'une séance). Ils font le pont entre les Controllers et les Repositories.
4.  **Controllers (`app/Controllers/`) :** Orchestrent les actions en recevant les requêtes HTTP, appelant les Services, et renvoyant les Vues.
5.  **Views (`views/`) :** Fichiers PHP/HTML gérant l'affichage (interface web).
6.  **Point d'entrée unique (`public/index.php`) :** Intercepte toutes les requêtes, initialise les dépendances et route vers le bon contrôleur.

## Installation et Déploiement

1.  **Cloner le dépôt :**
    Clonez ce projet dans le répertoire racine de votre serveur web (ex: `htdocs` pour XAMPP, `www` pour WAMP).

2.  **Base de données :**
    *   Créez une base de données MySQL nommée `fitconnect`.
    *   Importez le fichier de structure : `sql/fitconnect_structure.sql`.
    *   (Optionnel) Importez le jeu de données de test : `sql/fitconnect_data.sql`.

3.  **Configuration :**
    *   Ouvrez le fichier `config/Database.php`.
    *   Vérifiez et modifiez si nécessaire les identifiants de connexion à la base de données (host, dbname, user, pass).

4.  **Accès à l'application :**
    *   Ouvrez votre navigateur web.
    *   Accédez à l'URL correspondant à votre installation locale, par exemple : `http://localhost/FitConnect/public/`.

## Base de données (MLD)

La structure de la base de données respecte l'intégrité référentielle :
*   `salles` (id, nom, adresse, ville, telephone)
*   `adherents` (id, nom, prenom, email, telephone, date_naissance, salle_id, created_at)
*   `types_abonnement` (id, libelle, duree_jours, prix)
*   `abonnements` (id, adherent_id, type_id, date_debut, date_fin, statut, created_at)
*   `types_activite` (id, libelle, description)
*   `equipements` (id, libelle, salle_id)
*   `seances` (id, adherent_id, salle_id, type_activite_id, equipement_id, date_seance, duree_minutes, notes, created_at)

## Tests

Un script de test est disponible pour vérifier la bonne connexion à la base de données : `http://localhost/FitConnect/public/test.php`.
