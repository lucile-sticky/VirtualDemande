<?php

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
require_once($_SERVER['DOCUMENT_ROOT'] . '/VirtualDemande/model/DAL/Utilisateur_has_GroupeDAL.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/VirtualDemande/model/DAL/Groupe_has_MachineDAL.php');

class GroupeDAL 
{
    /*
     * Retourne le groupe par défaut
     * 
     * @return Groupe
     */
    public static function findByDefault()
    {
        $id=1;
        $data = BaseSingleton::select('SELECT groupe.id as id, '
                        . 'groupe.nom as nom, '
                        . 'groupe.date_creation as date_creation, '
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
     * Retourne le groupe correspondant à l'id donné
     * 
     * @param int $id
     * @return Groupe
     */
    public static function findById($id)
    {
        $data = BaseSingleton::select('SELECT groupe.id as id, '
                        . 'groupe.nom as nom, '
                        . 'groupe.date_creation as date_creation, '
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
     * Retourne l'ensemble des groupes où un utilisateur donné en paramètre est
     * 
     * @param userId
     * return array[Groupe] Tous les Groupes sont placés dans un Tableau
     */
    
    public static function findByUser($utilisateurId)
    {
        $mesgroupes = array();
        $mesgroupehasutilisateurs=Utilisateur_has_GroupeDAL::findByUtilisateur($utilisateurId);
        
        //Retourne le bon nombre d'enregistrements...
               
        foreach ($mesgroupehasutilisateurs as $row)
        {
            $GroupeId=$row->getGroupe()->getId(); 
            //echo "Id du groupe".$GroupeId;
            $groupe = self::findById($GroupeId);
            $mesgroupes[] = $groupe;
        }
        return $mesgroupes;
        
    }
    
    /*
     * Renvoie liste des groupes de l’utilisateur où une VM donnée n’est pas. 
     * 
     * @param int $userId, int $machineId
     * return array[Groupe] Tous les Groupes sont placés dans un Tableau
     */
    
    public static function findLessMachine($utilisateurId, $machineId)
    {
        $mesGroupesLessMachine = array();
        
        $mesGroupesByUtilisateur=self::findByUser($utilisateurId);

        foreach ($mesGroupesByUtilisateur as $row)
        {
            $groupeId=$row->getId();
            $statut=  Groupe_has_MachineDAL::isInGroupe($groupeId, $machineId);
            
            if($statut==false)
            {
                $groupe = self::findById($groupeId);
                $mesGroupesLessMachine[] = $groupe;
            }
        }

        return $mesGroupesLessMachine;
    }
    
    /*
     * Renvoie liste des groupes où 'l'utilisateur donné n’est pas. 
     * 
     * @param int $userId
     * return array[Groupe] Tous les Groupes sont placés dans un Tableau
     */
    
    public static function findLessUser($utilisateurId)
    {
        $mesGroupesLessUser = array();
        
        $mesGroupes=self::findAll();

        foreach ($mesGroupes as $row)
        {
            $groupeId=$row->getId();
            $statut= Utilisateur_has_GroupeDAL::isInByUser($utilisateurId,$groupeId);
            
            if($statut==false)
            {
                $groupe = self::findById($groupeId);
                $mesGroupesLessUser[] = $groupe;
            }
        }

        return $mesGroupesLessUser;
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
                        . 'groupe.date_creation as date_creation, '
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
     * Retourne le Groupe correspondant à l'attribut nom
     * Cet ensemble étant unique, il n'y qu'une seule ligne retournée.
     * Il est recherché sans tenir compte de la casse sur nom
     * 
     * @param string nom
     * @return Groupe | null
     */
    public static function findByNom($nom)
    {
        $data = BaseSingleton::select('SELECT groupe.id as id, '
                        . 'groupe.nom as nom, '
                        . 'groupe.date_creation as date_creation, '
                        . 'groupe.description as description '
                        . ' FROM groupe'
                        . ' WHERE groupe.nom = ?', array('s', &$nom));
        $groupe = new Groupe();
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
            $sql = 'INSERT INTO groupe (nom, date_creation, description) '
                    . ' VALUES (?,?,?) ';

            //Prépare les info concernant les type de champs
            $params = array('sss',
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
            $params = array('sssi',
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
