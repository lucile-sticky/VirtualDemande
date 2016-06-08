<?php

//import
require_once($_SERVER['DOCUMENT_ROOT'] . '/VirtualDemande/model/DAL/RamDAL.php');

$data = filter_input(INPUT_POST, 'visible', FILTER_SANITIZE_STRING, FILTER_REQUIRE_ARRAY);

/* Pour test :
 * $data = array(true,false,true,false); 
 */

$id=1;

foreach ($data as $row)
{
    //echo $row;
    $newRam=RamDAL::findById($id);
    while($newRam==null)
    {
        $id=$id+1;
        $newRam=RamDAL::findById($id);
    }
    //echo "  NOM :".$newRam->getValeur();
    $newRam->setVisible($row);
    //echo "           Visible après :".$newRam->getVisible();
    $validUpdate = RamDAL::insertOnDuplicate($newRam);
    $id=$id+1;
}

//Renvoie à la page précédante
    //echo "<meta http-equiv='refresh' content='1; url=".$_SERVER["HTTP_REFERER"]. "' />";