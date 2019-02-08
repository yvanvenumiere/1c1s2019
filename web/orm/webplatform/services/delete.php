<?php
ini_set ( "display_errors" , "1" );
session_start();
if(!isset($_SESSION['connected']))
{
	 header('Location: auth.php'); 
}
//we extract the differents parameters
extract($_POST);
//parameters list
/*table:name of the table,
 *id:value for the first primary key for this table , we have to delete the entry using this data
 */
//import of differents files
include('../libs/php/globalInfos.php');
$gb=new globalInfos();
$gb->requireModel($table);
include('../libs/php/inclusion_'.$table.'.php');//the file that instanciate the model

$oneElement->initFromDatas(array($datasManager->getPrimaryKey()=>$id));//we init the model instance with the datas we have
if($oneElement->delete())//if we succeed to delete the entry
{
	echo json_encode(array("result"=>"ok","message"=>"L'élément a bien été effacé"));
}
else {
	echo json_encode(array("result"=>"ko","message"=>"L'élément n'a pu être effacé"));
}


?>