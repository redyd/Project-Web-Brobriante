# ------------------------------ #
# CREATION DE LA BASE DE DONNEES #
# ------------------------------ #

# SUPPRESSION #
DROP TABLE IF EXISTS objet;
DROP TABLE IF EXISTS categorie;
DROP TABLE IF EXISTS brocanteur;
DROP TABLE IF EXISTS emplacement;
DROP TABLE IF EXISTS zone;

# ZONE #
CREATE TABLE IF NOT EXISTS `zone`
(
    # Champs globaux
    `zid`         int          NOT NULL AUTO_INCREMENT,
    `nom`         varchar(100) NOT NULL,
    `description` text         NOT NULL,

    # Type de clé
    PRIMARY KEY (`zid`)

) ENGINE = InnoDB
  AUTO_INCREMENT = 1;

# EMPLACEMENT #
CREATE TABLE IF NOT EXISTS `emplacement`
(
    # Champs globaux
    `eid`  int NOT NULL AUTO_INCREMENT,
    `code` int NOT NULL UNIQUE,

    # Clé étrangère
    `zone` int NOT NULL,

    # Types de clés
    PRIMARY KEY (`eid`),
    FOREIGN KEY (`zone`) REFERENCES `zone` (`zid`)

) ENGINE = InnoDB
  AUTO_INCREMENT 1;

# BROCANTEUR #
CREATE TABLE IF NOT EXISTS brocanteur
(
    # Champs globaux
    `bid`                int          NOT NULL AUTO_INCREMENT,
    `nom`                varchar(100) NOT NULL,
    `prenom`             varchar(100) NOT NULL,
    `email`              varchar(100) NOT NULL UNIQUE,
    `mot_passe`          varchar(100) NOT NULL,
    `photo`              varchar(50),
    `description`        text,
    `visible`            boolean DEFAULT FALSE,
    `est_administrateur` boolean DEFAULT FALSE,

    # Clé étrangère
    `emplacement`        int UNIQUE,

    # Types de clés
    PRIMARY KEY (`bid`),
    FOREIGN KEY (`emplacement`) REFERENCES `emplacement` (`eid`)

) ENGINE = InnoDB
  AUTO_INCREMENT 1;

# CATEGORIE #
CREATE TABLE IF NOT EXISTS `categorie`
(
    # Champs globaux
    `cid`      int          NOT NULL AUTO_INCREMENT,
    `intitule` varchar(100) NOT NULL,

    # Type de clé
    PRIMARY KEY (`cid`)

) ENGINE = InnoDB
  AUTO_INCREMENT 1;

# Objet #
CREATE TABLE IF NOT EXISTS `objet`
(
    # Champs globaux
    `oid`         int          NOT NULL AUTO_INCREMENT,
    `intitule`    varchar(100) NOT NULL,
    `description` text,
    `image`       varchar(50),

    # Clé étrangère
    `categorie`   int          NOT NULL,
    `brocanteur`  int          NOT NULL,

    # Types de clé
    PRIMARY KEY (`oid`),
    FOREIGN KEY (`categorie`) REFERENCES `categorie` (`cid`),
    FOREIGN KEY (`brocanteur`) REFERENCES `brocanteur` (`bid`) ON DELETE CASCADE

) ENGINE = InnoDB
  AUTO_INCREMENT 1;

# --------------------- #
# CREATION DES EXEMPLES #
# --------------------- #

DELETE
FROM Q240078.objet
WHERE oid > 0;
DELETE
FROM Q240078.brocanteur
WHERE bid > 0;
DELETE
FROM Q240078.categorie
WHERE cid > 0;
DELETE
FROM Q240078.emplacement
WHERE eid > 0;
DELETE
FROM Q240078.zone
WHERE zid > 0;

-- ZONES
INSERT INTO Q240078.zone (nom, description)
VALUES ('Zone A', 'Secteur principal proche de l’entrée')
     , ('Zone B', 'Espace réservé aux brocanteurs professionnels')
     , ('Zone C', 'Secteur spécialisé en outils rares et anciens')
     , ('Zone D', 'Section pour les gros équipements et machines')
     , ('Zone E', 'Petits outils et accessoires à bas prix');

-- EMPLACEMENTS
INSERT INTO Q240078.emplacement (code, zone)
VALUES (101, 1)
     , (102, 1)
     , (201, 2)
     , (202, 2)
     , (301, 3)
     , (302, 3)
     , (401, 4)
     , (402, 4)
     , (501, 5)
     , (502, 5);

-- CATEGORIES
INSERT INTO Q240078.categorie (intitule)
VALUES ('Outils à main')
     , ('Outils électriques')
     , ('Machines et équipements')
     , ('Accessoires et consommables')
     , ('Outils anciens');

-- BROCANTEURS
INSERT INTO Q240078.brocanteur (nom, prenom, email, mot_passe, photo, description, visible, est_administrateur,
                                emplacement)
VALUES ('Durand', 'Pierre', 'pierre.durand@gmail.com',
        'b73609972528f74abbeb6bf745a2df559c07240e65c933fd3af68aa2ac79c2be', 'img_67dd61f0adbf2.jpg',
        'Spécialiste en outils anciens.', 1, 1, 4),
       ('Leroy', 'Claire', 'claire.leroy@gmail.com',
        'b7ab0b3bc593f9261c4e874f833e030eba8e4a1184d270e673bd7dc194e123cf', 'img_67de8705948a0.jpg',
        'Collectionneuse d’outillage de précision.', 1, 0, null),
       ('Morel', 'Jacques', 'jacques.morel@gmail.com',
        'ef797c8118f02dfb649607dd5d3f8c7623048c9c063d532cc95c5ed7a898a64f', 'img_67dd62150fe53.jpg',
        'Vendeur d’outils professionnels.', 1, 1, 3),
       ('Bernard', 'Julie', 'julie.bernard@gmail.com',
        '31d6d9f9d1d5a1acbb5b8285398988a28affc886a9c0baae7524d22d11b7cf1a', 'img_67dd62872e450.jpg',
        'Passionnée de restauration d’outils.', 1, 0, 10),
       ('Soeur', 'Timëo', 't.soeur@student.helmo.be',
        'dfeebfcfc018ffc4f7fa2abb9ef96fd3ed182e4fae8f59efbf522b5cbca99299', 'img_67e7b2f6a04f2.png',
        'Créateur du site', 0, 1, null),
       ('Dubois', 'William', 'willi.am@gmail.com',
        'fa318b6ed96b6412278f53316fb0a2854be7c71c36529e024f5c54494cb691f0', 'img_67de71d7c892b.jpg',
        'Passionné de plantes', 1, 0, 9),
       ('Donfut', 'Pierre', 'donfutpierre@outlook.fr',
        'd1dfe33b3ef9ee2578d6a9c344b8704b028c5f2b8e29e001e7d00e56aed897cc', 'img_67de73a625390.jpg',
        'Spécialisée en gros outillage', 1, 0, 7),
       ('Sert', 'René', 'renesert@yahoo.com', 'e97624552d22866f0810c92bf410a97e20c6fda9f4e69c060a2a88fc5d9ecff2',
        'img_67de742c0a1cc.jpg', 'Ancien forgeron', 1, 0, 8),
       ('Tailleur', 'Vincent', 'vincent.tailleur@gmail.com',
        'eef03d5997c0a90e215b285317cc25e6c40980e4a4198a879124fdff9f375284', 'img_67ded00f9e13b.jpg', 'Adore l&#039;aventure et la découverte de nouveaux outils.
Passionné de roses', 1, 0, 2),
       ('Varshavsky', 'Vladimir', 'vladimir.varshavsky@gmail.com',
        '62fbdad326116cec9f24216260370c5193d967b5ed1f8466d5212b0a6a3dc429', 'img_67debd0553746.png',
        'Adore les fleurs et le jardinage', 1, 0, null);

-- OBJETS
INSERT INTO Q240078.objet (intitule, description, image, categorie, brocanteur)
VALUES ('Marteau en acier forgé', 'Marteau robuste pour menuiserie.', 'img_67dd608504e47.jpg', 1, 1),
       ('Perceuse Bosch 500W', 'Perceuse en parfait état de fonctionnement.', 'img_67dd60dc43593.jpg', 2, 2),
       ('Tour à bois ancien', 'Machine à bois datant des années 1950.', 'img_67dd61b3987cb.jpg', 3, 3),
       ('Clous et vis anciennes', 'Lot de clous et vis en laiton du XIXe siècle.', 'img_67dd61346bd59.jpg', 4, 4),
       ('Scie égoïne vintage', 'Scie manuelle en bois et acier.', 'img_67dedb49244bf.jpg', 5, 5),
       ('Clé anglaise ajustable', 'Clé de serrage neuve de grande taille.', 'img_67dd6062f0726.jpg', 1, 1),
       ('Meuleuse Makita', 'Outil électrique pour découpe et ponçage.', 'img_67dd60a5f20a6.jpg', 2, 2),
       ('Établi en chêne massif', 'Grand établi de menuisier, solide et stable.', 'img_67dd616cc747c.jpg', 3, 3),
       ('Lime à métaux', 'Lime récente en parfait état.', 'img_67dd61930f40f.jpg', 4, 4),
       ('Scie circulaire Festool', 'Scie électrique haute précision.', 'img_67dd610a196f4.jpg', 2, 2),
       ('Outils de plomberie', 'Ensemble d&#039;outils anciens de plomberie.', 'img_67dd66ccc76e6.jpg', 5, 1),
       ('Rabot en bois', 'Rabot manuel ancien pour ébénisterie.', 'img_67dfeca492d84.jpg', 1, 2),
       ('Pince multiprise', 'Pince robuste pour divers travaux.', 'img_67dfecc6aa51f.jpg', 1, 3),
       ('Burin de maçon', 'Outil de taille en acier trempé.', 'img_67dfeabc484e1.jpg', 4, 4),
       ('Étau d’établi', 'Étau en fonte pour maintenir des pièces.', 'img_67dfeb018e05e.jpg', 3, 5),
       ('Scie à métaux', 'Scie à cadre en métal avec lame interchangeable.', 'img_67dfed248465e.jpg', 1, 1),
       ('Niveau à bulle', 'Niveau de précision pour travaux de construction.', 'img_67dfed7b38469.jpg', 5, 2),
       ('Tournevis isolé', 'Tournevis plat isolé pour électriciens.', 'img_67dfeade23047.jpg', 4, 3),
       ('Poste à souder', 'Poste à souder à l’arc en bon état.', 'img_67dfee043ab4a.jpg', 2, 4),
       ('Brouette en acier', 'Brouette solide pour le transport de matériaux.', 'img_67dfed4d252a4.jpg', 5, 5);

COMMIT;