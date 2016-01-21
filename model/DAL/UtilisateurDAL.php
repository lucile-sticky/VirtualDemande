<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of UtilisateurDAL
 *
 * @author Alexis
 */

require_once('BaseSingleton.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/VirtualDemande/model/class/Utilisateur.php');

class UtilisateurDAL 
{
    /*
     * Retourne l'utilisateur correspondant à l'id donné
     * 
     * @param int $id
     * @return Utilisateur
     */
    public static function findById($id)
    {
        $data = BaseSingleton::select('SELECT utilisateur.id as id, '
                        . 'utilisateur.Role_id as role, '
                        . 'utilisateur.nom as nom, '
                        . 'utilisateur.prenom as prenom, '
                        . 'utilisateur.login as login '
                        . 'utilisateur.password as password '
                        . 'utilisateur.mail as mail '
                        . 'utilisateur.date_creation as dateCreation '
                        . 'utilisateur.date_naissance as dateNaissance '
                        . ' FROM utilisateur'
                        . ' WHERE utilisateur.id = ?', array('i', &$id));
        $utilisateur = new Utilisateur();
        if (sizeof($data) > 0)
        {
            $utilisateur->hydrate($data[0]);
        }
        else
        {
            $utilisateur = null;
        }
        return $utilisateur;
    }

    /*
     * Retourne l'ensemble des Utilisateurs qui sont en base
     * 
     * @return array[Utilisateur] Tous les Utilisateurs sont placées dans un Tableau
     */
    public static function findAll()
    {
        $mesUtilisateurs = array();

        $data = BaseSingleton::select('SELECT utilisateur.id as id, '
                        . 'utilisateur.Role_id as role, '
                        . 'utilisateur.nom as nom, '
                        . 'utilisateur.prenom as prenom, '
                        . 'utilisateur.login as login '
                        . 'utilisateur.password as password '
                        . 'utilisateur.mail as mail '
                        . 'utilisateur.date_creation as dateCreation '
                        . 'utilisateur.date_naissance as dateNaissance '
                        . ' FROM utilisateur'
                . ' ORDER BY utilisateur.Role_id ASC, utilisateur.nom ASC, utilisateur.prenom ASC, utilisateur.login ASC');

        foreach ($data as $row)
        {
            $utilisateur = new Utilisateur();
            $utilisateur->hydrate($row);
            $mesUtilisateurs[] = $utilisateur;
        }
        return $mesUtilisateurs;
    }
    
    /*
     * Retourne l'Utilisateur correspondant au couple Role_id/login
     * Ce couple étant unique, il n'y qu'une seul ligne retourner.
     * Il est rechercher sans tenir compte de la casse sur login
     * 
     * @param int roleId, string login
     * @return Utilisateur | null
     */
    public static function findByDN($roleId, $login)
    {
        $data = BaseSingleton::select('SELECT utilisateur.id as id, '
                        . 'utilisateur.Role_id as roleId, '
                        . 'utilisateur.nom as nom, '
                        . 'utilisateur.prenom as prenom, '
                        . 'utilisateur.login as login, '
                        . 'utilisateur.password as password, '
                        . 'utilisateur.mail as mail, '
                        . 'utilisateur.dateCreation as dateCreation, '
                        . 'utilisateur.date_naissance as dateNaissance'
                        . ' FROM utilisateur'
                        . ' WHERE utilisateur.Role_id = ? AND LOWER(utilisateur.login) = LOWER(?)', array('is', &$roleId, &$login));
        $utilisateur = new Utilisateur();

        if (sizeof($data) > 0)
        {
            $utilisateur->hydrate($data[0]);
        }
        return $utilisateur;
    }
    
    /*
     * Insère ou met à jour l'Utilisateur donné en paramètre.
     * Pour cela on vérifie si l'id de la Distrib_Alias transmis est sup ou inf à 0.
     * Si l'id est inf à 0 alors il faut insèrer, sinon update à l'id transmis.
     * 
     * @param Utilisateur utilisateur
     * @return int id
     * L'id de l'objet inséré en base. False si ça a planté
     */

    public static function insertOnDuplicate($utilisateur)
    {

        //Récupère les valeurs de l'objet utilisateur passé en para de la méthode
        $role = $utilisateur->getRole()->getId(); //int
        $nom = $utilisateur->getNom(); //string
        $prenom = $utilisateur->getPrenom(); //string
        $login = $utilisateur->getLogin(); //string
        $password = $utilisateur->getPassword(); //string
        $mail = $utilisateur->getMail(); //string
        $dateCreation = $utilisateur->getDateCreation(); //string
        $dateNaissance = $utilisateur->getDateNaissance(); //string
        $id = $utilisateur->getId(); //int
        if ($id < 0)
        {
            $sql = 'INSERT INTO utilisateur (Role_id, nom, prenom, login, password, mail, date_creation, date_naissance) '
                    . ' VALUES (?,?,?,?,?,?,?,?) ';

            //Prépare les info concernant les type de champs
            $params = array('isss',
                &$role,
                &$nom,
                &$prenom,
                &$login,
                &$password,
                &$mail,
                &$dateCreation,
                &$dateNaissance
            );
        }
        else
        {
            $sql = 'UPDATE utilisateur '
                    . 'SET Role_id = ?, '
                    . 'nom = ?, '
                    . 'prenom = ?, '
                    . 'login = ?, '
                    . 'password = ? '
                    . 'mail = ?, '
                    . 'date_creation = ?, '
                    . 'date_naissance = ?, '
                    . 'WHERE id = ? ';

            //Prépare les info concernant les type de champs
            $params = array('isssi',
                &$role,
                &$nom,
                &$prenom,
                &$login,
                &$password,
                &$mail,
                &$dateCreation,
                &$dateNaissance,
                &$id
            );
        }

        //Exec la requête
        $idInsert = BaseSingleton::insertOrEdit($sql, $params);

        return $idInsert;
    }
    
    /*
     * Supprime l'Utilisateur correspondant à l'id donné en paramètre
     * 
     * @param int $id
     * @return bool
     * True si la ligne a bien été supprimée, False sinon
     */

    public static function delete($id)
    {
        $deleted = BaseSingleton::delete('DELETE FROM utilisateur WHERE id = ?', array('i', &$id));
        return $deleted;
    }
}
