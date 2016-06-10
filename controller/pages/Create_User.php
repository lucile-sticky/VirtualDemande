<?php

//Script de création d'un utilisateur dans les deux bases de données
    //B+

//import
require_once($_SERVER['DOCUMENT_ROOT'] . '/VirtualDemande/model/DAL/UtilisateurDAL.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/VirtualDemande/model/DAL/Guacamole_UserDAL.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/VirtualDemande/model/DAL/Guacamole_User_PermissionDAL.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/VirtualDemande/model/DAL/RoleDAL.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/VirtualDemande/model/DAL/GroupeDAL.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/VirtualDemande/model/DAL/Utilisateur_has_GroupeDAL.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/VirtualDemande/model/DAL/LimitantDAL.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/VirtualDemande/model/DAL/Table_logDAL.php');

//Définition d'un objet Table_log pour faire des insert de log
$newLog = new Table_log();
$newLog->setLoginUtilisateur("anonyme");

//Définition du message renvoyé
$message="error";

//Checker de où il vient

$validPage = filter_input(INPUT_POST, 'page', FILTER_SANITIZE_STRING);

if($validPage == "register.php")
{
    $nbreUtilisateursRestants=UtilisateurDAL::GetNumberAvailableUsers();
    if($nbreUtilisateursRestants>0)
    {
        $newLog->setLevel("INFO");
        $newLog->setMsg("Initialisation de la création d'un utilisateur.");
        $validTableLog = Table_logDAL::insertOnDuplicate($newLog);
        //Création d'un Utilisateur par défaut
        $newUtilisateur=new Utilisateur();

        //=====Vérification de ce qui est renvoyé par le formulaire
        $validLogin = filter_input(INPUT_POST, 'pseudo', FILTER_SANITIZE_STRING);

        if ($validLogin != null)
        {
            $newUtilisateur->setLogin($validLogin);
            //echo "OK pour Login : ".$newUtilisateur->getLogin();
        }

        $validNom = filter_input(INPUT_POST, 'nom', FILTER_SANITIZE_STRING);
        if ($validNom != null)
        {
            $newUtilisateur->setNom($validNom);
            //echo "OK pour Nom :".$newUtilisateur->getNom();
        }

        $validPrenom = filter_input(INPUT_POST, 'prenom', FILTER_SANITIZE_STRING);
        if ($validPrenom != null)
        {
            $newUtilisateur->setPrenom($validPrenom);
            //echo "OK pour Prenom :".$newUtilisateur->getPrenom();
        }

        $validEmail = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_STRING);
        if ($validEmail != null)
        {
            $newUtilisateur->setMail($validEmail);
            //echo "OK pour Mail :".$newUtilisateur->getMail();
        }

        $validDate = filter_input(INPUT_POST, 'date', FILTER_SANITIZE_STRING);
        $date = DateTime::createFromFormat('d/m/Y', $validDate);
        $validDateFormat=$date->format('Y/m/d');
        if ($validDateFormat != null)
        {
            $newUtilisateur->setDateNaissance($validDateFormat);
            //echo "OK pour Date de Naissance:".$newUtilisateur->getDateNaissance();
        }

        $validPassword = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING);
        if ($validPassword != null)
        {
            $newUtilisateur->setPassword($validPassword);
            //echo "OK pour Passwd:".$newUtilisateur->getPassword();
        }

        //nb_VM + dateCreation + roleId à générer en plus

        $newUtilisateur->setNbVm("0");
        //echo "OK pour NbVm:".$newUtilisateur->getNbVm();

        $newDateCreation=date("Y/m/d");
        $newUtilisateur->setDateCreation($newDateCreation);
        //echo "OK pour DateCréation:".$newUtilisateur->getDateCreation();

        $newUtilisateur->setRole("2");
        //echo "OK pour Role_id:".$newUtilisateur->getRole()->getId();

        //====Vérification de doublons==== - OK
        if (UtilisateurDAL::isUnique($validLogin,$validEmail) == null)
        {
        //=====Insertion=====/ - OK
            $validInsertUtilisateur = UtilisateurDAL::insertOnDuplicate($newUtilisateur);

            if ($validInsertUtilisateur != null)
            {
                $newLog->setLevel("INFO");
                $newLog->setMsg("Ajout de l'utilisateur reussi dans la base DBVirtDemande ! (id:" . $validInsertUtilisateur .").");
                $validTableLog = Table_logDAL::insertOnDuplicate($newLog);
                //echo "Ajout de l'utilisateur reussi dans la base DBVirtDemande ! (id:" . $validInsertUtilisateur . ")";
                //Création d'un guacamole_user

                $newUserGuacamole=new Guacamole_User();

                //=====Vérification de ce qui est renvoyé par le formulaire
                $validUserName = filter_input(INPUT_POST, 'pseudo', FILTER_SANITIZE_STRING);
                if ($validUserName != null)
                {
                    $newUserGuacamole->setUserName($validUserName);
                    $newLog->setLevel("INFO");
                    $newLog->setMsg("OK pour Username : ".$newUserGuacamole->getUsername());
                    $validTableLog = Table_logDAL::insertOnDuplicate($newLog);
                    //echo "OK pour Username : ".$newUserGuacamole->getUsername();
                }

                $validPassword = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING);

                if ($validPassword != null)
                {
                    $newUserGuacamole->setPasswordHash($validPassword);
                    $newLog->setLevel("INFO");
                    $newLog->setMsg("OK pour PasswdHash:".$newUserGuacamole->getPasswordHash());
                    $validTableLog = Table_logDAL::insertOnDuplicate($newLog);
                    //echo "OK pour PasswdHash:".$newUserGuacamole->getPasswordHash();
                }

                //A rajouter : expired et disabled : mettre à 0

                    $newUserGuacamole->setDisabled(0);
                    $newLog->setLevel("INFO");
                    $newLog->setMsg("OK pour Disabled:".$newUserGuacamole->getDisabled());
                    $validTableLog = Table_logDAL::insertOnDuplicate($newLog);
                    //echo "OK pour Disabled:".$newUserGuacamole->getDisabled();

                    $newUserGuacamole->setExpired(0);
                    //echo "OK pour Expired:".$newUserGuacamole->getExpired();

                    //Les $accessWindowStart, $accessWindowEnd doivent être à null sinon ils ne pourront pas accéder à n'importe quelle heure sur leurs machines
                    //$validFrom=null, $validUntil=null, pareils

                //echo "Valider";

                //====Vérification de doublons====
                if (Guacamole_UserDAL::findByUsername($validUserName) == null)
                {
                //=====Insertion=====/ - OK

                    $validInsertUser = Guacamole_UserDAL::insertOnDuplicate($newUserGuacamole);

                    if ($validInsertUser != null)
                    {
                        $newLog->setLevel("INFO");
                        $newLog->setMsg("Ajout de l'utilisateur reussi dans la base guacamole_db! (id:" . $validInsertUser . ")");
                        $validTableLog = Table_logDAL::insertOnDuplicate($newLog);
                        //echo "Ajout de l'utilisateur reussi dans la base guacamole_db! (id:" . $validInsertUser . ")";
                        //Paramètres des permissions de l'utilisateur
                        $guacamoleUserPermission=new Guacamole_User_Permission();
                        $guacamoleUserPermission->setUser($validInsertUser);
                        $guacamoleUserPermission->setAffectedUser($validInsertUser);
                        $guacamoleUserPermission->setPermission("READ");
                        $valid=Guacamole_User_PermissionDAL::insertOnDuplicate($guacamoleUserPermission);
                        $guacamoleUserPermission->setPermission("UPDATE");
                        $valid=Guacamole_User_PermissionDAL::insertOnDuplicate($guacamoleUserPermission);
                        $guacamoleUserPermission->setPermission("DELETE");
                        $valid=Guacamole_User_PermissionDAL::insertOnDuplicate($guacamoleUserPermission);
                        $guacamoleUserPermission->setPermission("ADMINISTER");
                        $valid=Guacamole_User_PermissionDAL::insertOnDuplicate($guacamoleUserPermission);
                        $newLog->setLevel("INFO");
                        $newLog->setMsg("Ajout des permissions réussis pour l'utilisateur avec l'id:" . $validInsertUser . ")");
                        $validTableLog = Table_logDAL::insertOnDuplicate($newLog);
                        $message="ok";
                    }
                    else
                    {
                        $newLog->setLevel("ERROR");
                        $newLog->setMsg("Insert echec dans la base de données guacamole_db...");
                        $validTableLog = Table_logDAL::insertOnDuplicate($newLog);
                        //echo "insert echec...";
                        //Renvoie à la page précédante
                            echo "<meta http-equiv='refresh' content='1; url=".$_SERVER["HTTP_REFERER"].'&message='.$message. "' />";
                    }
                }
                else
                {
                    $newLog->setLevel("ERROR");
                    $newLog->setMsg("Erreur, l'utilisateur que vous voulez ajouter existe déjà...");
                    $validTableLog = Table_logDAL::insertOnDuplicate($newLog);
                    //echo "Erreur, l'utilisateur que vous voulez ajouter existe déjà...";
                    //Renvoie à la page précédante
                        echo "<meta http-equiv='refresh' content='1; url=".$_SERVER["HTTP_REFERER"].'&message='.$message. "' />";
                }    
            }
            else
            {
                $newLog->setLevel("ERROR");
                $newLog->setMsg("Insert echec de l'utilisateur dans la base de données DBVirtDemande...");
                $validTableLog = Table_logDAL::insertOnDuplicate($newLog);
                //echo "insert echec...";
                //Renvoie à la page précédante
                    echo "<meta http-equiv='refresh' content='1; url=".$_SERVER["HTTP_REFERER"].'&message='.$message. "' />";
            }
        }
        else
        {
            $newLog->setLevel("ERROR");
            $newLog->setMsg("Erreur, l'utilisateur que vous voulez ajouter existe...");
            $validTableLog = Table_logDAL::insertOnDuplicate($newLog);
            //echo "Erreur, l'utilisateur que vous voulez ajouter existe...";
            //Renvoie à la page précédante
                echo "<meta http-equiv='refresh' content='1; url=".$_SERVER["HTTP_REFERER"].'&message='.$message. "' />";
        }
    }
    else
    {
        $newLog->setLevel("ERROR");
        $newLog->setMsg("L'utilisateur ne peut pas s'enregistrer car il n'y a plus de place pour un autre utilisateur.");
        $validTableLog = Table_logDAL::insertOnDuplicate($newLog);
        //Renvoie à la page précédante
            echo "<meta http-equiv='refresh' content='1; url=".$_SERVER["HTTP_REFERER"].'&message='.$message. "' />";
    }
}

//Renvoie à la page précédante
    echo "<meta http-equiv='refresh' content='1; url=".$_SERVER["HTTP_REFERER"].'&message='.$message. "' />";