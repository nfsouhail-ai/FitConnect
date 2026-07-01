-- ============================================================
-- FitConnect - Jeu de données de test
-- ============================================================

USE fitconnect;

-- ============================================================
-- Salles
-- ============================================================
INSERT INTO salles (nom, adresse, ville, telephone) VALUES
('FitConnect Casablanca Centre', '12 Boulevard Mohammed V', 'Casablanca', '0522-100-001'),
('FitConnect Casablanca Maarif', '45 Rue Abdelkrim El Khattabi', 'Casablanca', '0522-100-002'),
('FitConnect Rabat Agdal',       '8 Avenue Al Amir Fal Ould Oumeir', 'Rabat',       '0537-200-001'),
('FitConnect Marrakech Gueliz',  '23 Avenue Mohammed VI', 'Marrakech', '0524-300-001');

-- ============================================================
-- Types d'abonnement
-- ============================================================
INSERT INTO types_abonnement (libelle, duree_jours, prix) VALUES
('Mensuel',      30,  299.00),
('Trimestriel',  90,  799.00),
('Annuel',       365, 2799.00);

-- ============================================================
-- Types d'activité
-- ============================================================
INSERT INTO types_activite (libelle, description) VALUES
('Musculation',    'Entrainement avec charges et haltères'),
('Cardio',         'Exercices cardiovasculaires sur machines'),
('Yoga',           'Seances de yoga et etirements'),
('CrossFit',       'Entrainements fonctionnels haute intensité'),
('Natation',       'Seances en piscine'),
('Cours collectif','Cours en groupe anime par un coach'),
('Arts Martiaux',  'Boxe, Karate, MMA');

-- ============================================================
-- Equipements
-- ============================================================
INSERT INTO equipements (libelle, salle_id) VALUES
('Tapis de course A1',    1),
('Tapis de course A2',    1),
('Vélo elliptique B1',    1),
('Rack à haltères',       1),
('Banc de musculation',   1),
('Tapis de course C1',    2),
('Rameur D1',             2),
('Barre de tractions',    2),
('Vélo stationnaire E1',  3),
('Cage de squat',         3),
('Tapis de sol',          3),
('Sac de frappe',         4),
('Corde à sauter',        4),
('Rack à haltères',       4);

-- ============================================================
-- Adhérents
-- ============================================================
INSERT INTO adherents (nom, prenom, email, telephone, date_naissance, salle_id) VALUES
('Benali',    'Mohamed',  'mohamed.benali@email.com',   '0661-001-001', '1990-03-15', 1),
('Ouali',     'Fatima',   'fatima.ouali@email.com',     '0661-001-002', '1995-07-22', 1),
('Rachidi',   'Youssef',  'youssef.rachidi@email.com',  '0662-001-003', '1988-11-08', 2),
('Amrani',    'Salma',    'salma.amrani@email.com',     '0663-001-004', '1993-05-30', 2),
('Tahiri',    'Karim',    'karim.tahiri@email.com',     '0664-001-005', '1985-09-12', 3),
('El Fassi',  'Nadia',    'nadia.elfassi@email.com',    '0665-001-006', '1998-02-18', 3),
('Bouzidi',   'Omar',     'omar.bouzidi@email.com',     '0666-001-007', '1992-06-25', 4),
('Moroccan',  'Sara',     'sara.moroccan@email.com',    '0667-001-008', '1997-12-03', 4),
('Lahlou',    'Rachid',   'rachid.lahlou@email.com',    '0668-001-009', '1980-04-17', 1),
('Cherkaoui', 'Imane',    'imane.cherkaoui@email.com',  '0669-001-010', '1991-08-09', 2);

-- ============================================================
-- Abonnements (un seul actif par adhérent)
-- ============================================================
INSERT INTO abonnements (adherent_id, type_id, date_debut, date_fin, statut) VALUES
(1,  1, '2026-06-01', '2026-06-30', 'actif'),      -- Mohamed : mensuel
(2,  2, '2026-05-01', '2026-07-29', 'actif'),      -- Fatima  : trimestriel
(3,  3, '2026-01-01', '2026-12-31', 'actif'),      -- Youssef : annuel
(4,  1, '2026-06-10', '2026-07-09', 'actif'),      -- Salma   : mensuel
(5,  2, '2026-04-01', '2026-06-29', 'expiré'),     -- Karim   : expiré
(5,  1, '2026-06-25', '2026-07-24', 'actif'),      -- Karim   : nouveau mensuel
(6,  1, '2026-06-15', '2026-07-14', 'actif'),      -- Nadia   : mensuel
(7,  3, '2026-03-01', '2027-02-28', 'actif'),      -- Omar    : annuel
(8,  2, '2026-05-15', '2026-08-12', 'actif'),      -- Sara    : trimestriel
(9,  1, '2026-05-01', '2026-05-31', 'expiré'),     -- Rachid  : expiré
(10, 2, '2026-06-01', '2026-08-29', 'actif');      -- Imane   : trimestriel

-- ============================================================
-- Séances
-- ============================================================
INSERT INTO seances (adherent_id, salle_id, type_activite_id, equipement_id, date_seance, duree_minutes, notes) VALUES
(1,  1, 1, 4,    '2026-06-02', 60,  'Séance jambes'),
(1,  1, 2, 1,    '2026-06-05', 45,  'Cardio intensif'),
(1,  1, 1, 5,    '2026-06-10', 75,  'Haut du corps'),
(2,  1, 4, NULL, '2026-06-03', 50,  'CrossFit débutant'),
(2,  1, 6, NULL, '2026-06-07', 55,  'Cours zumba'),
(3,  2, 1, 8,    '2026-06-01', 90,  'Full body'),
(3,  2, 2, 6,    '2026-06-08', 40,  'Cardio léger'),
(3,  2, 4, NULL, '2026-06-15', 60,  'WOD CrossFit'),
(4,  2, 3, NULL, '2026-06-12', 60,  'Yoga relaxation'),
(4,  2, 6, NULL, '2026-06-18', 55,  'Pilates'),
(5,  3, 1, 10,   '2026-06-25', 80,  'Squats et deadlifts'),
(6,  3, 3, 11,   '2026-06-16', 60,  'Yoga flow'),
(6,  3, 2, 9,    '2026-06-20', 35,  'Vélo doux'),
(7,  4, 7, 12,   '2026-06-05', 75,  'Boxe thaïlandaise'),
(7,  4, 4, 13,   '2026-06-12', 60,  'CrossFit avancé'),
(8,  4, 6, NULL, '2026-06-17', 50,  'Cours danse fitness'),
(8,  4, 7, 12,   '2026-06-22', 70,  'MMA débutant'),
(10, 2, 2, 6,    '2026-06-03', 45,  'Cardio'),
(10, 2, 1, 8,    '2026-06-10', 65,  'Musculation bras'),
(10, 2, 4, NULL, '2026-06-20', 55,  'CrossFit intermédiaire');
