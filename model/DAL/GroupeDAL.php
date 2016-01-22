<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of GroupeDAL
 *
 * @author Alexis
 * @author Aurelie
 */

/*
 * IMPORT
 */
require_once('BaseSingleton.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/VirtualDemande/model/class/Groupe.php');

class GroupeDAL 
{
    /*
     * Retourne le groupe correspondant à l'id donné
     * 
     * @param int $id
     * @return Groupe
     */
    public static function findById($id)
    {
        $data = BaseSingleton::select('SELECT groupe.id as id, '
                        . 'groupe.nom as nom, '
                        . 'groupe.date_creation as dateCreation, '
                        . 'groupe.description  as description  '
                        . ' FROM groupe'
                        . ' WHERE groupe.id = ?', array('i', &$id));
        $groupe= new Groupe();
        
        if (sizeof($data) > 0)            
        {
            $groupe->hydrate($data[0]);
        }
        else
        {
            $groupe = null;
        }
        return $groupe;
    }

    /*
     * Retourne l'ensemble des Groupes qui sont en base
     * 
     * @return array[Groupe] Tous les Groupes sont placés dans un Tableau
     */
    public static function findAll()
    {
        $mesGroupes = array();

        $data = BaseSingleton::select('SELECT groupe.id as id, '
                        . 'groupe.nom as nom, '
                        . 'groupe.date_creation as dateCreation, '
                        . 'groupe.description  as description  '
                        . ' FROM groupe'
                . ' ORDER BY groupe.nom ASC');

        foreach ($data as $row)
        {
            $groupe = new Groupe();
            $groupe->hydrate($row);
            $mesGroupes[] = $groupe;
        }

        return $mesGroupes;
    }

    /*
     * Insère ou met à jour le Groupe donné en paramètre.
     * Pour cela on vérifie si l'id du Groupe transmis est sup ou inf à 0.
     * Si l'id est inf à 0 alors il faut insérer, sinon update à l'id transmis.
     * 
     * @param Groupe groupe
     * @return int id
     * L'id de l'objet inséré en base. False si ça a planté
     */

    public static function insertOnDuplicate($groupe)
    {
        //Récupère les valeurs de l'objet groupe passées en para de la méthode
        $nom = $groupe->getNom(); //string
        $dateCreation = $groupe->getDateCreation(); //string
        $description = $groupe->getDescription(); //string
        $id = $groupe->getId(); //int
        if ($id < 0)
        {
            $sql = 'INSERT INTO groupe (nom, dateCreation, description) '
                    . ' VALUES (?,?,?) ';

            //Prépare les info concernant les type de champs
            $params = array('isss',
                &$nom,
                &$dateCreation,
                &$description
            );
        }
        else
        {
            $sql = 'UPDATE groupe '
                    . 'SET nom = ?, '
                    . 'date_creation = ?, '
                    . 'description = ? '
                    . 'WHERE id = ? ';

            //Prépare les info concernant les type de champs
            $params = array('isssi',
                &$nom,
                &$dateCreation,
                &$description,
                &$id
            );
        }

        //Exec la requête
        $idInsert = BaseSingleton::insertOrEdit($sql, $params);

        return $idInsert;
    }
    
    /*
     * Supprime le Groupe correspondant à l'id donné en paramètre
     * 
     * @param int $id
     * @return bool
     * True si la ligne a bien été supprimée, False sinon
     */

    public static function delete($id)
    {
        $deleted = BaseSingleton::delete('DELETE FROM groupe WHERE id = ?', array('i', &$id));
        return $deleted;
    }
}
