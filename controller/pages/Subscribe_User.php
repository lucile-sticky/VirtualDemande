<?php

/*
 * Envoie de l’id d'un utilisateur et de l'id du groupe 
 * Script (controller) pour supprimer l’utilisateur d’un groupe
 * (=> suppression de toute ces VM partagé dans ce groupe)
 */

//import
require_once($_SERVER['DOCUMENT_ROOT'] . '/VirtualDemande/model/DAL/Utilisateur_has_GroupeDAL.php');

$newUtilisateurHasGroupe=new Utilisateur_has_Groupe();

//=====Vérification de ce qui est renvoyé par le formulaire
$validIdUser = filter_input(INPUT_POST, 'idUser', FILTER_SANITIZE_STRING);
$newUtilisateurHasGroupe->setUtilisateur($validIdUser);
echo "OK pour Id User : ".$newUtilisateurHasGroupe->getUtilisateur()->getId();

$validIdGroupe = filter_input(INPUT_POST, 'idGroupe', FILTER_SANITIZE_STRING);
$newUtilisateurHasGroupe->setGroupe($validIdGroupe);
echo "OK pour Id Groupe : ".$newUtilisateurHasGroupe->getGroupe()->getId();

//Vérification si l'utilisateur fait partie du groupe
if(Utilisateur_has_GroupeDAL::findByGU($validIdGroupe,$validIdUser)==null)
{
    echo "Utilisateur n'est pas dans le groupe";
    
    //Ajout de l'utilisateur du groupe
    $validInsert=Utilisateur_has_GroupeDAL::insertOnDuplicate($newUtilisateurHasGroupe);

    //Renvoie à la page précédante
    echo "<meta http-equiv='refresh' content='1; url=".$_SERVER["HTTP_REFERER"]. "' />";

}
else
{
    echo "Utilisateur est deja dans le groupe";
}