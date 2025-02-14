# Projet Portfolio - Gestion des Utilisateurs et des Compétences

## Présentation du Projet
Ce projet est une application web développée en PHP & MySQL permettant aux utilisateurs de :
- [X] Gérer leur profil (inscription, connexion, mise à jour des informations).
- [X] Ajouter et modifier leurs compétences parmi celles définies par un administrateur.
- [X] Ajouter et gérer leurs projets (titre, description, image et lien).
- [X] Un administrateur peut gérer les compétences disponibles.

## Fonctionnalités Implémentées

### Authentification & Gestion des Comptes
- [X] Inscription avec validation des champs
- [X] Connexion sécurisée avec sessions et option "Se souvenir de moi"
- [X] Gestion des rôles (Admin / Utilisateur)
- [X] Mise à jour des informations utilisateur
- [X] Réinitialisation du mot de passe
- [X] Déconnexion sécurisée

### Gestion des Compétences
- [X] L’administrateur peut gérer les compétences proposées
- [X] Un utilisateur peut sélectionner ses compétences parmi celles disponibles
- [X] Niveau de compétence défini sur une échelle (débutant → expert)

### Gestion des Projets
- [X] Ajout, modification et suppression de projets
- [X] Chaque projet contient : Titre, Description, Image, Lien externe
- [X] Upload sécurisé des images avec restrictions de format et taille
- [X] Affichage structuré des projets

### Sécurité
- [X] Protection contre XSS, CSRF et injections SQL
- [X] Hachage sécurisé des mots de passe
- [ ] Gestion des erreurs utilisateur avec affichage des messages et conservation des champs remplis
- [X] Expiration automatique de la session après inactivité

## Installation et Configuration

### Prérequis
- Serveur local (XAMPP, WAMP, etc.)
- PHP 8.x et MySQL
- Un navigateur moderne

### Étapes d’Installation
1. Cloner le projet sur votre serveur local :
   ```sh
   git clone https://github.com/deBarbarinAntoine/PortfolioTP.git
   cd PortfolioTP
   ```
   
2. Importer la base de données :
    - `PortfolioTP/database.sql`


3. Configurer la connexion à la base de données :

> Attention !
> 
> Il y a deux moyens de configurer la connexion à la base de données et d'autres aspects de l'application web.
> 
> - `config/database.php` est le minimum requis, mais devient optionnel si le `.env` est renseigné.
> - `.env` est optionnel mais fortement conseillé (plus précis et complet), et a la **PRIORITÉ** sur le fichier `config/database.php`.

   - Option 1 : modifier le fichier `config/database.php` :
   ```php
   define('DB_HOST', 'localhost');
   define('DB_NAME', 'projetb2');
   define('DB_USER', 'projetb2');
   define('DB_PASS', 'password');
   define('DB_PORT', 3306);
   ```
   - Option 2 : copier le fichier `.env.example` et le renommer `.env`, ensuite, renseigner les données de configuration :
   ```dotenv
   DB_HOST=localhost
   DB_PORT=3306
   DB_NAME=projetb2
   DB_USER=projetb2
   DB_PASS=password
   ENVIRONMENT=production
   MAIL_SENDER="PortfolioTP <no-reply@PortfolioTP.com>"
   MAIL_ADDRESS=your_mail_address
   MAIL_USERNAME=your_mail_username
   MAIL_PASSWORD=your_mail_password
   MAIL_HOST=smtp.mail.io
   MAIL_PORT=587
   MAIL_ENCRYPTION=PHPMailer::ENCRYPTION_STARTTLS
   ```

4. Démarrer le serveur PHP et tester l'application :
   ```sh
   php -S localhost:8000
   ```
   Puis accéder à l'application via `http://localhost:8000`

## Comptes de Test

### Compte Administrateur
- **Email** : admin@example.com
- **Mot de passe** : password

### Compte Utilisateur
- **Email** : user@example.com
- **Mot de passe** : password

## Structure du Projet

```
/index.php           -> Point d'entrée et routeur du site web
/config/database.php -> Configuration de la base de données (optionnel si .env correct)
/.env                -> Fichier de variables d'environnement (prioritaire au /config/database.php, optionnel)
/database.sql        -> Script SQL pour initialiser la base de données
/composer.json|.lock -> Fichiers de configuration Composer

/Models/             -> Classes PHP (User, Project, Skill, etc.)
/Controllers/        -> Gestion des requêtes et logiques métier
/Views/              -> Interfaces utilisateur (HTML, CSS)
/public/             -> Images et assets du projet
```

## Technologies Utilisées
- **Backend** : **PHP**
- **Frontend** : **PHP / HTML / JS / (S)CSS**
- **Sécurité** : **PHP / FastRoute / encryption**
- **Gestion du Projet** : **Discord / GitHub**

## Licence
Ce projet est sous licence MIT.

## Contact
Une question ou un bug ? Contactez-nous :
- [nicolas.moyon@ynov.com](mailto:nicolas.moyon@ynov.com)
- [antoine.debarbarin@ynov.com](mailto:antoine.debarbarin@ynov.com)
