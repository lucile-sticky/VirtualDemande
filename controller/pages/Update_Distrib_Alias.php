<?php

//import
require_once($_SERVER['DOCUMENT_ROOT'] . '/VirtualDemande/model/DAL/Distrib_AliasDAL.php');

/* Pour test :
 * $data = array(true,false,true,false); 
 */

//$data   = filter_input(INPUT_POST, 'visible', FILTER_SANITIZE_STRING, FILTER_REQUIRE_ARRAY);

$id=1;

foreach ($data as $row)
{
    //echo $row;
    $newDistribAlias=Distrib_AliasDAL::findById($id);
    while($newDistribAlias==null)
    {
        $id=$id+1;
        $newDistribAlias=Distrib_AliasDAL::findById($id);
    }
    //echo "  NOM :".$newDistribAlias->getValeur();
    $newDistribAlias->setVisible($row);
    //echo "           Visible après :".$newDistribAlias->getVisible();
    $validUpdate = Distrib_AliasDAL::insertOnDuplicate($newDistribAlias);
    $id=$id+1;
}

//Renvoie à la page précédante
    //echo "<meta http-equiv='refresh' content='1; url=".$_SERVER["HTTP_REFERER"]. "' />";