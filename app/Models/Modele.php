<?php
//acces au Modele parent pour l heritage
namespace App\Models;
use CodeIgniter\Model;

//=========================================================================================
//définition d'une classe Modele (meme nom que votre fichier Modele.php) 
//héritée de Modele et permettant d'utiliser les raccoucis et fonctions de CodeIgniter
//  Attention vos Fichiers et Classes Controleur et Modele doit commencer par une Majuscule 
//  et suivre par des minuscules
//=========================================================================================
class Modele extends Model {

//==========================
// Code du modele
//==========================

//=========================================================================
// Fonction 1
// récupère les données BDD dans une fonction getVisiteur
//=========================================================================
public function getVisiteur($login,$mdp) { 
// Recherche un visiteur à partir de son identifiant et de son mot de passe.

//==========================================================================================
// Connexion à la BDD en utilisant les données féninies dans le fichier app/Config/Database.php
//==========================================================================================
    $db = db_connect();
    #$db->table(visiteur);
    #$table = 'visiteur';

//=============================
// rédaction de la requete sql
//=============================
    $sql = 'SELECT id, nom, prenom FROM visiteur WHERE login = ? AND mdp = ?';
	#$db->table('visiteur')
     #   ->where ('login',$login)
     #   ->where ('mdp',$mdp)
      #  ->get()
       # ->getResultArray();


//=============================
// execution de la requete sql
//=============================	
    $resultat = $db->query($sql,[$login,$mdp]);

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
// Fonction 2 
// récupère les données BDD dans une fonction getFrais
//=========================================================================
public function getFrais($idVisiteur,$mois) {
    // Vérifie si une fiche existe pour ce visiteur et ce mois.
	
//==========================================================================================
// Connexion à la BDD en utilisant les données féninies dans le fichier app/Config/Database.php
//==========================================================================================
    $db = db_connect();	
	
//=====================================
// rédaction de la requete sql préparée
//=====================================
	$sql = 'SELECT * FROM FicheFrais WHERE idVisiteur = ? AND mois = ?';
	
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