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
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    avatar     VARCHAR(100) NOT NULL
);

-- Trigger to set updated_at timestamp
DELIMITER $$

CREATE TRIGGER before_users_update
    BEFORE UPDATE ON users
    FOR EACH ROW
BEGIN
    SET NEW.updated_at = CURRENT_TIMESTAMP;
END$$

DELIMITER ;

-- Table des compétences (Ajout d'une description)
CREATE TABLE skills
(
    id          INT PRIMARY KEY AUTO_INCREMENT,
    name        VARCHAR(100) NOT NULL UNIQUE,
    description TEXT         NULL, -- Optionnel mais utile pour mieux définir les compétences
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Table de liaison utilisateur-compétences
CREATE TABLE user_skills
(
    id         INT PRIMARY KEY AUTO_INCREMENT,
    user_id    INT                                                                       NOT NULL,
    skill_id   INT                                                                       NOT NULL,
    level      ENUM ('beginner', 'medium', 'advanced', 'expert') DEFAULT 'beginner' NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE,
    FOREIGN KEY (skill_id) REFERENCES skills (id) ON DELETE CASCADE
);

-- Trigger to set updated_at timestamp
DELIMITER $$

CREATE TRIGGER before_user_skills_update
    BEFORE UPDATE ON user_skills
    FOR EACH ROW
BEGIN
    SET NEW.updated_at = CURRENT_TIMESTAMP;
END$$

DELIMITER ;

-- Table des projets
CREATE TABLE projects
(
    id            INT PRIMARY KEY AUTO_INCREMENT,
    title         VARCHAR(255)                                 NOT NULL,
    description   TEXT                                         NOT NULL,
    external_link VARCHAR(255),
    visibility     ENUM ('private', 'public') DEFAULT 'private' NOT NULL,
    created_at    TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at    TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Trigger to set updated_at timestamp
DELIMITER $$

CREATE TRIGGER before_projects_update
    BEFORE UPDATE ON projects
    FOR EACH ROW
BEGIN
    SET NEW.updated_at = CURRENT_TIMESTAMP;
END$$

DELIMITER ;

-- table pour gérer plusieurs images par projet
CREATE TABLE project_images
(
    id          INT PRIMARY KEY AUTO_INCREMENT,
    project_id  INT          NOT NULL,
    image_path  VARCHAR(255) NOT NULL,
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
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (project_id) REFERENCES projects (id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE,
    UNIQUE (project_id, user_id) -- Empêche les doublons
);

-- Trigger to set updated_at timestamp
DELIMITER $$

CREATE TRIGGER before_project_users_update
    BEFORE UPDATE ON project_users
    FOR EACH ROW
BEGIN
    SET NEW.updated_at = CURRENT_TIMESTAMP;
END$$

DELIMITER ;

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

CREATE TABLE password_resets
(
    id         INT AUTO_INCREMENT PRIMARY KEY,
    email      VARCHAR(100) NOT NULL,
    token      VARCHAR(255) NOT NULL,
    expires_at DATETIME     NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (email) REFERENCES users (email) ON DELETE CASCADE
);

-- Insert Admin User
INSERT INTO users (id, username, email, password, role, avatar)
VALUES (1, 'admin', 'admin@example.com',
        'JGFyZ29uMmlkJHY9MTkkbT02NTUzNix0PTMscD00JEdNUjhWMkt6ZkNpQWRrelVuQnY2OUEkcWJWNnRqQUhMam0rUE9FL3Jta3JadGphdWtxVUFRNDE5WjNpUWhuS2NuOA==',
        'admin', 'admin_avatar.png');

-- Insert Regular User
INSERT INTO users (id, username, email, password, role, avatar)
VALUES (2, 'user', 'user@example.com',
        'JGFyZ29uMmlkJHY9MTkkbT02NTUzNix0PTMscD00JEdNUjhWMkt6ZkNpQWRrelVuQnY2OUEkcWJWNnRqQUhMam0rUE9FL3Jta3JadGphdWtxVUFRNDE5WjNpUWhuS2NuOA==',
        'user', 'user_avatar.png');

-- Insert Random Users
INSERT INTO users (id, username, email, password, role, avatar)
VALUES
    (3, 'john_doe', 'john@example.com', 'random_hashed_password_1', 'user', 'john_avatar.png'),
    (4, 'jane_doe', 'jane@example.com', 'random_hashed_password_2', 'user', 'jane_avatar.png'),
    (5, 'dev_master', 'dev@example.com', 'random_hashed_password_3', 'admin', 'dev_avatar.png');

-- Insert Random Skills
INSERT INTO skills (name, description)
VALUES
    ('JavaScript', 'Programming language for web development'),
    ('Python', 'Versatile programming language used for web development, data science, and AI'),
    ('SQL', 'Language used to manage and manipulate databases'),
    ('React', 'JavaScript library for building interactive user interfaces'),
    ('Machine Learning', 'Branch of artificial intelligence focused on learning models');

-- Assign Random Skills to Users
INSERT INTO user_skills (user_id, skill_id, level)
VALUES
    (1, 1, 'expert'),
    (1, 2, 'avanced'),
    (2, 3, 'medium'),
    (3, 4, 'beginner'),
    (4, 5, 'advanced');

-- Insert Random Projects
INSERT INTO projects (title, description, external_link, visibility)
VALUES
    ('Web Project Management App', 'An application for managing tasks and projects in a team.', 'https://github.com/example/project1', 'public'),
    ('Python AI Bot', 'An intelligent chatbot based on machine learning.', 'https://github.com/example/project2', 'private'),
    ('Modern E-commerce Site', 'Development of a modern e-commerce site using React and Node.js.', 'https://github.com/example/project3', 'public');

-- Assign Users to Projects
INSERT INTO project_users (project_id, user_id, role)
VALUES
    (1, 1, 'owner'),
    (1, 2, 'contributor'),
    (2, 3, 'owner'),
    (3, 4, 'contributor'),
    (3, 5, 'viewer');

-- Insert Project Images
INSERT INTO project_images (project_id, image_path)
VALUES
    (1, 'images/project1_img1.png'),
    (1, 'images/project1_img2.png'),
    (2, 'images/project2_img1.png'),
    (3, 'images/project3_img1.png'),
    (3, 'images/project3_img2.png');

-- Insert Random Sessions
INSERT INTO sessions (user_id, token, expires_at)
VALUES
    (1, 'random_token_1', DATE_ADD(NOW(), INTERVAL 7 DAY)),
    (2, 'random_token_2', DATE_ADD(NOW(), INTERVAL 7 DAY)),
    (3, 'random_token_3', DATE_ADD(NOW(), INTERVAL 7 DAY));

-- Insert Password Reset Requests
INSERT INTO password_resets (email, token, expires_at)
VALUES
    ('john@example.com', 'reset_token_1', DATE_ADD(NOW(), INTERVAL 1 DAY)),
    ('jane@example.com', 'reset_token_2', DATE_ADD(NOW(), INTERVAL 1 DAY));