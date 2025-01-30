# Projet Portfolio - Gestion des Utilisateurs et des Compétences

## Présentation du Projet
Ce projet est une application web développée en PHP & MySQL permettant aux utilisateurs de :
- [ ] Gérer leur profil (inscription, connexion, mise à jour des informations).
- [ ] Ajouter et modifier leurs compétences parmi celles définies par un administrateur.
- [ ] Ajouter et gérer leurs projets (titre, description, image et lien).
- [ ] Un administrateur peut gérer les compétences disponibles.

## Fonctionnalités Implémentées

### Authentification & Gestion des Comptes
- [ ] Inscription avec validation des champs
- [ ] Connexion sécurisée avec sessions et option "Se souvenir de moi"
- [ ] Gestion des rôles (Admin / Utilisateur)
- [ ] Mise à jour des informations utilisateur
- [ ] Réinitialisation du mot de passe
- [ ] Déconnexion sécurisée

### Gestion des Compétences
- [ ] L’administrateur peut gérer les compétences proposées
- [ ] Un utilisateur peut sélectionner ses compétences parmi celles disponibles
- [ ] Niveau de compétence défini sur une échelle (débutant → expert)

### Gestion des Projets
- [ ] Ajout, modification et suppression de projets
- [ ] Chaque projet contient : Titre, Description, Image, Lien externe
- [ ] Upload sécurisé des images avec restrictions de format et taille
- [ ] Affichage structuré des projets

### Sécurité
- [ ] Protection contre XSS, CSRF et injections SQL
- [ ] Hachage sécurisé des mots de passe
- [ ] Gestion des erreurs utilisateur avec affichage des messages et conservation des champs remplis
- [ ] Expiration automatique de la session après inactivité

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
    - *à renseigner*

3. Configurer la connexion à la base de données :
   Modifier le fichier `config/database.php` :
   ```php
   define('DB_HOST', 'localhost');
   define('DB_NAME', 'projetb2');
   define('DB_USER', 'projetb2');
   define('DB_PASS', 'password');
   define('DB_PORT', 3306);
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

UN exemple de structure possible (la première ligne doit respecter cette structure).

```
/config/database.php -> Configuration de la base de données
/models/         -> Classes PHP (User, Auth, Project, Skill)
/controllers/    -> Gestion des requêtes et logiques métier
/views/          -> Interfaces utilisateur (HTML, CSS, Bootstrap)
/public/         -> Images et assets du projet
/database.sql    -> Script SQL pour initialiser la base de données
```

## Technologies Utilisées
- **Backend** : **à renseigner**
- **Frontend** : **à renseigner**
- **Sécurité** : **à renseigner**
- **Gestion du Projet** : **à renseigner**

## Licence
Ce projet est sous licence MIT.

## Contact
Une question ou un bug ? Contactez-nous : nicolas.moyon@ynov.com
