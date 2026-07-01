-- ============================================================
-- FitConnect - Script de création de la base de données
-- MLD -> Implémentation MySQL
-- ============================================================

CREATE DATABASE IF NOT EXISTS fitconnect
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE fitconnect;

-- ============================================================
-- Table : salles
-- ============================================================
CREATE TABLE IF NOT EXISTS salles (
    id       INT UNSIGNED NOT NULL AUTO_INCREMENT,
    nom      VARCHAR(100) NOT NULL,
    adresse  VARCHAR(255) NOT NULL,
    ville    VARCHAR(100) NOT NULL,
    telephone VARCHAR(20) DEFAULT NULL,
    PRIMARY KEY (id),
    UNIQUE KEY uq_salle_nom (nom)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- Table : adherents
-- ============================================================
CREATE TABLE IF NOT EXISTS adherents (
    id             INT UNSIGNED NOT NULL AUTO_INCREMENT,
    nom            VARCHAR(100) NOT NULL,
    prenom         VARCHAR(100) NOT NULL,
    email          VARCHAR(150) NOT NULL,
    telephone      VARCHAR(20)  DEFAULT NULL,
    date_naissance DATE         NOT NULL,
    salle_id       INT UNSIGNED NOT NULL,
    created_at     DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_adherent_email (email),
    CONSTRAINT fk_adherent_salle
        FOREIGN KEY (salle_id) REFERENCES salles(id)
        ON UPDATE CASCADE
        ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- Table : types_abonnement
-- ============================================================
CREATE TABLE IF NOT EXISTS types_abonnement (
    id           INT UNSIGNED NOT NULL AUTO_INCREMENT,
    libelle      VARCHAR(50)    NOT NULL,
    duree_jours  SMALLINT UNSIGNED NOT NULL,
    prix         DECIMAL(8,2)   NOT NULL,
    PRIMARY KEY (id),
    UNIQUE KEY uq_type_libelle (libelle)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- Table : abonnements
-- ============================================================
CREATE TABLE IF NOT EXISTS abonnements (
    id           INT UNSIGNED NOT NULL AUTO_INCREMENT,
    adherent_id  INT UNSIGNED NOT NULL,
    type_id      INT UNSIGNED NOT NULL,
    date_debut   DATE         NOT NULL,
    date_fin     DATE         NOT NULL,
    statut       ENUM('actif','expiré','annulé') NOT NULL DEFAULT 'actif',
    created_at   DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    CONSTRAINT fk_abonnement_adherent
        FOREIGN KEY (adherent_id) REFERENCES adherents(id)
        ON UPDATE CASCADE
        ON DELETE RESTRICT,
    CONSTRAINT fk_abonnement_type
        FOREIGN KEY (type_id) REFERENCES types_abonnement(id)
        ON UPDATE CASCADE
        ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- Table : types_activite
-- ============================================================
CREATE TABLE IF NOT EXISTS types_activite (
    id          INT UNSIGNED NOT NULL AUTO_INCREMENT,
    libelle     VARCHAR(100) NOT NULL,
    description TEXT         DEFAULT NULL,
    PRIMARY KEY (id),
    UNIQUE KEY uq_activite_libelle (libelle)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- Table : equipements
-- ============================================================
CREATE TABLE IF NOT EXISTS equipements (
    id       INT UNSIGNED NOT NULL AUTO_INCREMENT,
    libelle  VARCHAR(100) NOT NULL,
    salle_id INT UNSIGNED NOT NULL,
    PRIMARY KEY (id),
    CONSTRAINT fk_equipement_salle
        FOREIGN KEY (salle_id) REFERENCES salles(id)
        ON UPDATE CASCADE
        ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- Table : seances
-- ============================================================
CREATE TABLE IF NOT EXISTS seances (
    id               INT UNSIGNED NOT NULL AUTO_INCREMENT,
    adherent_id      INT UNSIGNED NOT NULL,
    salle_id         INT UNSIGNED NOT NULL,
    type_activite_id INT UNSIGNED NOT NULL,
    equipement_id    INT UNSIGNED DEFAULT NULL,
    date_seance      DATE         NOT NULL,
    duree_minutes    SMALLINT UNSIGNED NOT NULL,
    notes            TEXT         DEFAULT NULL,
    created_at       DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    CONSTRAINT fk_seance_adherent
        FOREIGN KEY (adherent_id) REFERENCES adherents(id)
        ON UPDATE CASCADE
        ON DELETE RESTRICT,
    CONSTRAINT fk_seance_salle
        FOREIGN KEY (salle_id) REFERENCES salles(id)
        ON UPDATE CASCADE
        ON DELETE RESTRICT,
    CONSTRAINT fk_seance_activite
        FOREIGN KEY (type_activite_id) REFERENCES types_activite(id)
        ON UPDATE CASCADE
        ON DELETE RESTRICT,
    CONSTRAINT fk_seance_equipement
        FOREIGN KEY (equipement_id) REFERENCES equipements(id)
        ON UPDATE CASCADE
        ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
