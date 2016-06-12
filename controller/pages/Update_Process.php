<?php

//import
require_once($_SERVER['DOCUMENT_ROOT'] . '/VirtualDemande/model/DAL/CpuDAL.php');

/* Pour test :
 * $data = array(true,false,true,false); 
 */

//Définition du message renvoyé
$message="error";

//Checker de où il vient

$validPage = filter_input(INPUT_POST, 'page', FILTER_SANITIZE_STRING);

if($validPage == "forms_administration.php")
{
    
    //Passer à 0 les distribs pour qu'elles ne soient pas visibles
    $lesCpu= CpuDAL::findAll();

    foreach ($lesCpu as $row)
    {
        $newCpu=$row;
        $newCpu->setVisible(false);
        $validUpdate = CpuDAL::insertOnDuplicate($newCpu);
    }
    
    //Récupération de la valeur passée
    $data = filter_input(INPUT_POST, 'idsCpu', FILTER_SANITIZE_STRING, FILTER_REQUIRE_ARRAY);
    
    $id=1;
    
    foreach ($data as $row)
    {
        $newCpu=CpuDAL::findById($row);
        $newCpu->setVisible(true);
        $validUpdate = CpuDAL::insertOnDuplicate($newCpu);
    }

    $message="ok";
}

//Renvoie à la page précédante
   echo "<meta http-equiv='refresh' content='1; url=".$_SERVER["HTTP_REFERER"].'&message='.$message. "' />";