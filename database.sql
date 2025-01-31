CREATE DATABASE projetb2;
USE projetb2;

-- Table des utilisateurs
CREATE TABLE users
(
    id         INT PRIMARY KEY AUTO_INCREMENT,
    username   VARCHAR(50)  NOT NULL UNIQUE,
    email      VARCHAR(100) NOT NULL UNIQUE,
    password   VARCHAR(255) NOT NULL,
    role       ENUM ('admin', 'user') DEFAULT 'user' NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    avatar     VARCHAR(100) NOT NULL
);

-- Table des compétences (Ajout d'une description)
CREATE TABLE skills
(
    id          INT PRIMARY KEY AUTO_INCREMENT,
    name        VARCHAR(100) NOT NULL UNIQUE,
    description TEXT NULL -- Optionnel mais utile pour mieux définir les compétences
);

-- Table de liaison utilisateur-compétences
CREATE TABLE user_skills
(
    id         INT PRIMARY KEY AUTO_INCREMENT,
    user_id    INT NOT NULL,
    skill_id   INT NOT NULL,
    level      ENUM ('débutant', 'intermédiaire', 'avancé', 'expert') DEFAULT 'débutant' NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE,
    FOREIGN KEY (skill_id) REFERENCES skills (id) ON DELETE CASCADE
);

-- Table des projets
CREATE TABLE projects
(
    id            INT PRIMARY KEY AUTO_INCREMENT,
    title         VARCHAR(255) NOT NULL,
    details       TEXT         NOT NULL,
    external_link VARCHAR(255),
    private       ENUM ('private', 'public') DEFAULT 'private' NOT NULL,
    created_at    TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at    TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- table pour gérer plusieurs images par projet
CREATE TABLE project_images
(
    id         INT PRIMARY KEY AUTO_INCREMENT,
    project_id INT NOT NULL,
    image_path VARCHAR(255) NOT NULL,
    uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (project_id) REFERENCES projects (id) ON DELETE CASCADE
);

-- Table de liaison projets-utilisateurs
CREATE TABLE project_users
(
    id         INT PRIMARY KEY AUTO_INCREMENT,
    project_id INT NOT NULL,
    user_id    INT NOT NULL,
    role       ENUM ('owner', 'contributor', 'viewer') DEFAULT 'contributor',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (project_id) REFERENCES projects (id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE,
    UNIQUE (project_id, user_id) -- Empêche les doublons
);

-- Table des sessions pour l'authentification "Se souvenir de moi"
CREATE TABLE sessions
(
    id         INT PRIMARY KEY AUTO_INCREMENT,
    user_id    INT          NOT NULL,
    token      VARCHAR(255) NOT NULL,
    expires_at TIMESTAMP    NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE
);
