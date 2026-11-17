<?php

namespace App\Models;

use CodeIgniter\Model;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Processor\PsrLogMessageProcessor;

class Modele extends Model
{
    public function getVisiteur($login, $mdp)
    {
        try {
            $db = db_connect();
        } catch (\Throwable $e) {
            $logger = new Logger('test_log');
            $logger->pushHandler(new StreamHandler('/tmp/monfichier.log', Logger::DEBUG));
            $logger->pushProcessor(new PsrLogMessageProcessor());
            $logger->emergency('Connexion impossible à la base de données : {message}', ['message' => $e->getMessage()]);

            throw $e;
        }

        $sql = 'SELECT id, nom, prenom FROM visiteur WHERE login = ? AND mdp = ?';
        $resultat = $db->query($sql, [$login, $mdp]);

        return $resultat->getResult();
    }

public function getFrais($idVisiteur,$mois) {
    $db = db_connect();	
	
	$sql = 'SELECT * FROM FicheFrais WHERE idVisiteur = ? AND mois = ?';
	
    $resultat = $db->query($sql, [$idVisiteur,$mois]);
	
	$resultat = $resultat->getResult();

    return $resultat;
  
}

//=========================================================================
// Fonction 3 
// récupère les données BDD dans une fonction getQuantites
//=========================================================================
public function getQuantites($idVisiteur,$mois) {
    // Calcule le total des frais forfaitisés pour un visiteur et un mois.
	
//==========================================================================================
// Connexion à la BDD en utilisant les données féninies dans le fichier app/Config/Database.php
//==========================================================================================
    $db = db_connect();	
	
//=====================================
// rédaction de la requete sql préparée
//=====================================
	$sql = 'SELECT SUM(l.quantite * f.montant) as total
                FROM LigneFraisForfait l
                JOIN FraisForfait f ON l.idFraisForfait = f.id
                WHERE l.idVisiteur = ? AND l.mois = ?';
	
//=====================================================
// execution de la requete sql en passant un parametre id
//=====================================================	
    $resultat = $db->query($sql, [$idVisiteur,$mois]);
	
//=============================
// récupération des données de la requete sql
//=============================
	$resultat = $resultat->getResult();

//=============================
// renvoi du résultat au Controleur
//=============================		
    return $resultat;
  
}

//=========================================================================
// Fonction 4 
// récupère les données BDD dans une fonction getMontant
//=========================================================================
public function getMontant($idVisiteur,$mois) {
    // Calcule le total des dépenses hors forfait pour un visiteur et un mois.
	
//==========================================================================================
// Connexion à la BDD en utilisant les données féninies dans le fichier app/Config/Database.php
//==========================================================================================
    $db = db_connect();	
	
//=====================================
// rédaction de la requete sql préparée
//=====================================
	$sql = 'SELECT SUM(montant) 
                FROM LigneFraisHorsForfait
                WHERE idVisiteur = ? AND mois = ?';
	
//=====================================================
// execution de la requete sql en passant un parametre id
//=====================================================	
    $resultat = $db->query($sql, [$idVisiteur,$mois]);
	
//=============================
// récupération des données de la requete sql
//=============================
	$resultat = $resultat->getResult();

//=============================
// renvoi du résultat au Controleur
//=============================		
    return $resultat;
  
}

//=========================================================================
// Fonction 4 
// récupère les données BDD dans une fonction insertFicheFrai
//=========================================================================
public function insertFicheFrai($idVisiteur,$mois,$dateModif) {
    // Crée la fiche mensuelle de frais avec son état initial.
	
//==========================================================================================
// Connexion à la BDD en utilisant les données féninies dans le fichier app/Config/Database.php
//==========================================================================================
    $db = db_connect();	
	
//=====================================
// rédaction de la requete sql préparée
//=====================================
	$sql = 'INSERT INTO FicheFrais (idVisiteur, mois, nbJustificatif, montantValide, dateModif, idEtat) VALUES (?, ?, 0, 0.00, ?, "CR")';
	
//=====================================================
// execution de la requete sql en passant un parametre id
//=====================================================	
    $resultat = $db->query($sql, [$idVisiteur,$mois,$dateModif]);

//=============================
// renvoi du résultat au Controleur
//=============================		
    return $resultat;
  
}

//=========================================================================
// Fonction 5 
// récupère les données BDD dans une fonction getHorsFrais
//=========================================================================
// Rassemble les frais forfaitisés et hors forfait pour afficher l'historique.
public function getDetailsFrais($idVisiteur) {
    $db = db_connect();
    // UNION ALL rassemble les deux catégories en conservant chaque ligne.
    $sql = 'SELECT frais.mois, frais.categorie, frais.dateFrais, frais.libelle,
                   frais.quantite, frais.montantUnitaire, frais.montantLigne
            FROM (
                SELECT lff.mois, "Forfait" AS categorie, NULL AS dateFrais,
                       ff.libelle, lff.quantite, ff.montant AS montantUnitaire,
                       lff.quantite * ff.montant AS montantLigne
                FROM LigneFraisForfait lff
                JOIN FraisForfait ff ON ff.id = lff.idFraisForfait
                WHERE lff.idVisiteur = ?

                UNION ALL

                SELECT lhf.mois, "Hors forfait" AS categorie, lhf.date AS dateFrais,
                       lhf.libelle, 1 AS quantite, lhf.montant AS montantUnitaire,
                       lhf.montant AS montantLigne
                FROM LigneFraisHorsForfait lhf
                WHERE lhf.idVisiteur = ?
            ) AS frais
            ORDER BY frais.mois DESC, frais.dateFrais DESC, frais.libelle ASC';

    return $db->query($sql, [$idVisiteur, $idVisiteur])->getResultArray();
}

//=========================================================================
// Fonction 6 
// récupère les données BDD dans une fonction insertFraisForfait
//=========================================================================
public function insertFraisForfait($idVisiteur,$mois,$idFraisForfait,$quantite,$quantites) {
    // Insère une quantité forfaitaire ou actualise la ligne si elle existe déjà.
	
//==========================================================================================
// Connexion à la BDD en utilisant les données féninies dans le fichier app/Config/Database.php
//==========================================================================================
    $db = db_connect();	
	
//=====================================
// rédaction de la requete sql préparée
//=====================================
	$sql = 'INSERT INTO LigneFraisForfait (idVisiteur, mois, idFraisForfait, quantite) VALUES (?, ?, ?, ?) ON DUPLICATE KEY UPDATE quantite = ?';
	
//=====================================================
// execution de la requete sql en passant un parametre id
//=====================================================	
    $resultat = $db->query($sql, [$idVisiteur,$mois,$idFraisForfait,$quantite,$quantites]);

//=============================
// renvoi du résultat au Controleur
//=============================		
    return $resultat;
  
}

//=========================================================================
// Fonction 7 
// récupère les données BDD dans une fonction updateFicheFrais
//=========================================================================
public function updateFicheFrais($dateModif,$idVisiteur,$mois) {
    // Actualise la date de modification et incrémente le nombre de justificatifs.
	
//==========================================================================================
// Connexion à la BDD en utilisant les données féninies dans le fichier app/Config/Database.php
//==========================================================================================
    $db = db_connect();	
	
//=====================================
// rédaction de la requete sql préparée
//=====================================
	$sql = 'UPDATE FicheFrais SET dateModif = ?, nbJustificatif = nbJustificatif + 1 WHERE idVisiteur = ? AND mois = ?';
	
//=====================================================
// execution de la requete sql en passant un parametre id
//=====================================================	
    $resultat = $db->query($sql, [$dateModif,$idVisiteur,$mois]);

//=============================
// renvoi du résultat au Controleur
//=============================		
    return $resultat;
  
}

//=========================================================================
// Fonction 8 
// récupère les données BDD dans une fonction updateMontant
//=========================================================================
public function updateMontant($montant,$idVisiteur,$mois) {
    // Enregistre le montant validé de la fiche de frais mensuelle.
	
//==========================================================================================
// Connexion à la BDD en utilisant les données féninies dans le fichier app/Config/Database.php
//==========================================================================================
    $db = db_connect();	
	
//=====================================
// rédaction de la requete sql préparée
//=====================================
	$sql = 'UPDATE FicheFrais 
                SET montantValide = ?
                WHERE idVisiteur = ? AND mois = ?';
	
//=====================================================
// execution de la requete sql en passant un parametre id
//=====================================================	
    $resultat = $db->query($sql, [$montant,$idVisiteur,$mois]);
	
//=============================
// récupération des données de la requete sql
//=============================
	$resultat = $resultat->getResult();

//=============================
// renvoi du résultat au Controleur
//=============================		
    return $resultat;
  
}

//=========================================================================
// Fonction 9 
// récupère les données BDD dans une fonction insertFicheHorsFrais
//=========================================================================
public function insertFicheHorsFrais($idVisiteur,$mois,$libelle,$date,$montant) {
    // Ajoute une dépense hors forfait avec sa date, son libellé et son montant.
	
//==========================================================================================
// Connexion à la BDD en utilisant les données féninies dans le fichier app/Config/Database.php
//==========================================================================================
    $db = db_connect();	
	
//=====================================
// rédaction de la requete sql préparée
//=====================================
	$sql = 'INSERT INTO LigneFraisHorsForfait (idVisiteur, mois, libelle, date, montant) VALUES (?, ?, ?, ?, ?)';
	
//=====================================================
// execution de la requete sql en passant un parametre id
//=====================================================	
    $resultat = $db->query($sql, [$idVisiteur,$mois,$libelle,$date,$montant]);

//=============================
// renvoi du résultat au Controleur
//=============================		
    return $resultat;
  
}

//=========================================================================
// Fonction 10 
// récupère les données BDD dans une fonction updateJustificatif
//=========================================================================
public function updateJustificatif($dateModif,$idVisiteur,$mois) {
    // Actualise la date de modification et le nombre de justificatifs de la fiche.
	
//==========================================================================================
// Connexion à la BDD en utilisant les données féninies dans le fichier app/Config/Database.php
//==========================================================================================
    $db = db_connect();	
	
//=====================================
// rédaction de la requete sql préparée
//=====================================
	$sql = 'UPDATE FicheFrais SET dateModif = ?, nbJustificatif = nbJustificatif + 1 WHERE idVisiteur = ? AND mois = ?';
	
//=====================================================
// execution de la requete sql en passant un parametre id
//=====================================================	
    $resultat = $db->query($sql, [$dateModif,$idVisiteur,$mois]);
	
//=============================
// récupération des données de la requete sql
//=============================
	$resultat = $resultat->getResult();

//=============================
// renvoi du résultat au Controleur
//=============================		
    return $resultat;
  
}

//=========================================================================
// Fonction 11 
// récupère les données BDD dans une fonction updateMontantValide
//=========================================================================
public function updateMontantValide($montant,$idVisiteur,$mois) {
    // Modifie le montant validé enregistré pour la fiche mensuelle.
	
//==========================================================================================
// Connexion à la BDD en utilisant les données féninies dans le fichier app/Config/Database.php
//==========================================================================================
    $db = db_connect();	
	
//=====================================
// rédaction de la requete sql préparée
//=====================================
	$sql = 'UPDATE FicheFrais 
                SET montantValide = ?
                WHERE idVisiteur = ? AND mois = ?';
	
//=====================================================
// execution de la requete sql en passant un parametre id
//=====================================================	
    $resultat = $db->query($sql, [$montant,$idVisiteur,$mois]);
	
//=============================
// récupération des données de la requete sql
//=============================
	$resultat = $resultat->getResult();

//=============================
// renvoi du résultat au Controleur
//=============================		
    return $resultat;
  
}

//=========================================================================
public function getFraisForfat($idVisiteur,$mois,$idFraisForfait) {
    // Récupère la quantité d'un type de forfait pour un visiteur et un mois.
	
//==========================================================================================
// Connexion à la BDD en utilisant les données féninies dans le fichier app/Config/Database.php
//==========================================================================================
    $db = db_connect();	
	
//=====================================
// rédaction de la requete sql préparée
//=====================================
	$sql = 'SELECT quantite FROM LigneFraisForfait WHERE idVisiteur = ? AND mois = ? AND idFraisForfait = ?';
	
//=====================================================
// execution de la requete sql en passant un parametre id
//=====================================================	
    $resultat = $db->query($sql, [$idVisiteur,$mois,$idFraisForfait]);
	
//=============================
// récupération des données de la requete sql
//=============================
	$resultat = $resultat->getResult();

//=============================
// renvoi du résultat au Controleur
//=============================		
    return $resultat;
  
}

//==========================
// Fin Code du modele
//===========================


//fin de la classe
}


?>