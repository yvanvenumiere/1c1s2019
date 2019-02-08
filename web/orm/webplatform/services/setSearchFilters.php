<?php
session_start();
if(!isset($_SESSION['connected']))
{
	 header('Location: auth.php'); 
}
ini_set ( "display_errors" , "1" );


//we extract the differents parameters
extract($_POST);
//parameters list
/*
 *table:name of the concerned table
 *mode:saving mode
 *id:id of the element if in edit mode
 *params:an array containing the different parameters
 */
//import of differents files
include('../libs/php/globalInfos.php');
$gb=new globalInfos();
$gb->requireModel($table);
include('../libs/php/inclusion_'.$table.'.php');//the file that instanciate the model

//we empty the session datas
$_SESSION['searchFilters'][$table]=$params;

echo json_encode(array("result"=>"ok"));
?>