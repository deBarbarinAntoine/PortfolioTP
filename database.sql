CREATE DATABASE projetb2;
USE projetb2;

-- Table des utilisateurs
CREATE TABLE users
(
    id         INT PRIMARY KEY AUTO_INCREMENT,
    username   VARCHAR(50)                           NOT NULL UNIQUE,
    email      VARCHAR(100)                          NOT NULL UNIQUE,
    password   VARCHAR(255)                          NOT NULL,
    role       ENUM ('admin', 'user') DEFAULT 'user' NOT NULL,
    created_at TIMESTAMP              DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP              DEFAULT CURRENT_TIMESTAMP
);

-- Table des compétences
CREATE TABLE skills
(
    id   INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL UNIQUE
);

-- Table de liaison utilisateur-compétences
CREATE TABLE user_skills
(
    id       INT PRIMARY KEY AUTO_INCREMENT,
    user_id  INT                                                                       NOT NULL,
    skill_id INT                                                                       NOT NULL,
    level    ENUM ('débutant', 'intermédiaire', 'avancé', 'expert') DEFAULT 'débutant' NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE,
    FOREIGN KEY (skill_id) REFERENCES skills (id) ON DELETE CASCADE
);

-- Table des projets (Sans user_id)
CREATE TABLE projects
(
    id            INT PRIMARY KEY AUTO_INCREMENT,
    title         VARCHAR(255) NOT NULL,
    details       TEXT         NOT NULL,
    image         VARCHAR(255),
    external_link VARCHAR(255),
    created_at    TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at    TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Table de liaison projets-utilisateurs (Many-to-Many)
CREATE TABLE project_users
(
    id         INT PRIMARY KEY AUTO_INCREMENT,
    project_id INT NOT NULL,
    user_id    INT NOT NULL,
    role       ENUM ('owner', 'contributor', 'viewer') DEFAULT 'contributor',
    FOREIGN KEY (project_id) REFERENCES projects (id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE,
    UNIQUE (project_id, user_id) -- To prevent duplicate entries
);

-- Table des sessions pour l'authentification "Se souvenir de moi"
CREATE TABLE sessions
(
    id         INT PRIMARY KEY AUTO_INCREMENT,
    user_id    INT          NOT NULL,
    token      VARCHAR(255) NOT NULL,
    expires_at TIMESTAMP    NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE
);
