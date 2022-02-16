-- Script to initialise the tables of the database group40

SET NAMES 'utf8'; -- Change character encoding to UTF-8

---------- CREATION OF TABLES ----------
-- Constraints of type
-- ALTER TABLE ... add constraint FOREIGN KEY(...) REFERENCES ...;
-- to add line per line in phpMyAdmin
-- because the lines FOREIGN KEY at the table creation are ignored

-- USERS
CREATE TABLE IF NOT EXISTS group40.users(
	login VARCHAR(50) NOT NULL,
	pass VARCHAR(50) NOT NULL,
	PRIMARY KEY(login)
)Engine=InnoDB;

INSERT INTO group40.users VALUES ('group40',  'mSoB1DTjxu');

-- SERIE
CREATE TABLE IF NOT EXISTS group40.serie(
	nom VARCHAR(50) NOT NULL,
	description VARCHAR(1000) NOT NULL,
	PRIMARY KEY(nom)
)Engine=InnoDB;

-- EPISODE
CREATE TABLE IF NOT EXISTS group40.episodes(
	n_saison INT NOT NULL,
	n_episode INT NOT NULL,
	duree INT NOT NULL,
	synopsis VARCHAR(500) NOT NULL,
	nom VARCHAR(50) NOT NULL,
	PRIMARY KEY(n_saison, n_episode, nom),
	FOREIGN KEY(nom) REFERENCES group40.serie(nom)
)Engine=InnoDB;

ALTER TABLE group40.episodes add constraint FOREIGN KEY(nom) REFERENCES group40.serie(nom);

-- PLATEFORME_STREAMING
CREATE TABLE IF NOT EXISTS group40.plateforme_streaming(
	nom VARCHAR(50) NOT NULL,
	societe VARCHAR(50) NOT NULL,
	PRIMARY KEY(nom)
)Engine=InnoDB;

-- PAYS
CREATE TABLE IF NOT EXISTS group40.pays(
	nom VARCHAR(50) NOT NULL,
	ordre INT NOT NULL,
	pays VARCHAR(50) NOT NULL,
	PRIMARY KEY(nom, ordre),
	FOREIGN KEY(nom) REFERENCES group40.plateforme_streaming(nom)
)Engine=InnoDB;

ALTER TABLE pays add constraint FOREIGN KEY(nom) REFERENCES group40.plateforme_streaming(nom);

-- PERSONNE
CREATE TABLE IF NOT EXISTS group40.personne(
  numero INT AUTO_INCREMENT,
	nom VARCHAR(50) NOT NULL,
	prenom VARCHAR(50) NOT NULL,
	date_naissance DATE NOT NULL,
	PRIMARY KEY(numero)
)Engine=InnoDB;

-- UTILISATEUR
CREATE TABLE IF NOT EXISTS group40.utilisateur(
  numero INT AUTO_INCREMENT,
	adresse_email VARCHAR(50) NOT NULL,
	PRIMARY KEY(numero),
	UNIQUE KEY(adresse_email),
	FOREIGN KEY(numero) REFERENCES group40.personne(numero)
)Engine=InnoDB;

ALTER TABLE utilisateur add constraint FOREIGN KEY(numero) REFERENCES group40.personne(numero);

-- ACTEUR
CREATE TABLE IF NOT EXISTS group40.acteur(
	numero INT NOT NULL,
	golden_globes INT NOT NULL,
	emmy_awards INT NOT NULL,
	PRIMARY KEY(numero),
	FOREIGN KEY(numero) REFERENCES group40.personne(numero)
)Engine=InnoDB;

ALTER TABLE acteur add constraint FOREIGN KEY(numero) REFERENCES group40.personne(numero);

-- DISPONIBLE_SUR
CREATE TABLE IF NOT EXISTS group40.disponible_sur(
	nom_serie VARCHAR(50) NOT NULL,
	nom_platf VARCHAR(50) NOT NULL,
	PRIMARY KEY(nom_serie, nom_platf),
	FOREIGN KEY(nom_serie) REFERENCES group40.serie(nom),
	FOREIGN KEY(nom_platf) REFERENCES group40.plateforme_streaming(nom)
)Engine=InnoDB;

ALTER TABLE disponible_sur add constraint FOREIGN KEY(nom_serie) REFERENCES group40.serie(nom);
ALTER TABLE disponible_sur add constraint FOREIGN KEY(nom_platf) REFERENCES group40.plateforme_streaming(nom);

-- JOUE_DANS
CREATE TABLE IF NOT EXISTS group40.joue_dans(
	numero INT NOT NULL,
	n_saison INT NOT NULL,
	n_episode INT NOT NULL,
	nom VARCHAR(50) NOT NULL,
	PRIMARY KEY(numero, n_saison, n_episode, nom),
	FOREIGN KEY(numero) REFERENCES group40.personne(numero),
	FOREIGN KEY(n_saison, n_episode) REFERENCES group40.episodes(n_saison, n_episode),
	FOREIGN KEY(nom) REFERENCES group40.serie(nom)
)Engine=InnoDB;

ALTER TABLE joue_dans add constraint FOREIGN KEY(numero) REFERENCES group40.personne(numero);
ALTER TABLE joue_dans add constraint FOREIGN KEY(n_saison, n_episode) REFERENCES group40.episodes(n_saison, n_episode);
ALTER TABLE joue_dans add constraint FOREIGN KEY(nom) REFERENCES group40.serie(nom);

-- EST_ABONNE
CREATE TABLE IF NOT EXISTS group40.est_abonne(
	date_debut DATE NOT NULL,
	date_fin DATE, -- Can be NULL if the abonnement has never ended yet
	numero INT NOT NULL,
	nom VARCHAR(50) NOT NULL,
	PRIMARY KEY(date_debut, numero, nom),
	FOREIGN KEY(numero) REFERENCES group40.personne(numero),
	FOREIGN KEY(nom) REFERENCES group40.plateforme_streaming(nom)
)Engine=InnoDB;

ALTER TABLE est_abonne add constraint FOREIGN KEY(numero) REFERENCES group40.personne(numero);
ALTER TABLE est_abonne add constraint FOREIGN KEY(nom) REFERENCES group40.plateforme_streaming(nom);

-- REGARDE
CREATE TABLE IF NOT EXISTS group40.regarde(
	numero INT NOT NULL,
	nom_plateforme VARCHAR(50) NOT NULL,
	n_episode INT NOT NULL,
	n_saison INT NOT NULL,
	nom_serie VARCHAR(50) NOT NULL,
	date_debut DATE NOT NULL,
	PRIMARY KEY(numero, nom_plateforme, n_episode, n_saison, nom_serie),
	FOREIGN KEY(numero) REFERENCES group40.personne(numero),
	FOREIGN KEY(nom_plateforme) REFERENCES group40.plateforme_streaming(nom),
	FOREIGN KEY(n_saison, n_episode) REFERENCES group40.episodes(n_saison, n_episode),
	FOREIGN KEY(nom_serie) REFERENCES group40.serie(nom)
)Engine=InnoDB;

ALTER TABLE regarde add constraint FOREIGN KEY(numero) REFERENCES group40.personne(numero);
ALTER TABLE regarde add constraint FOREIGN KEY(nom_plateforme) REFERENCES group40.plateforme_streaming(nom);
ALTER TABLE regarde add constraint FOREIGN KEY(n_saison, n_episode) REFERENCES group40.episodes(n_saison, n_episode);
ALTER TABLE regarde add constraint FOREIGN KEY(nom_serie) REFERENCES group40.serie(nom);
