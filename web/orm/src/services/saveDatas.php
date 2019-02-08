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

//if mode == edit  we initialize the instance with the good datas
if($mode=="edit")
{
	$oneElement->initFromDatas(array($datasManager->getPrimaryKey()=>$id));
}

//we apply the different parameters to our object
foreach($params as $key=>$value)
{
	$oneElement->set($key,trim($value));
}

if(isset($_SESSION['hierarchy']["filter"]) && count($_SESSION['hierarchy']["filter"])>0)
{
	
	foreach($_SESSION['hierarchy']["filter"] as $filter=>$value)
	{
		$oneElement->set($filter,trim($value));
	}
	
}

//if we save...
if($oneElement->save())
{
	echo json_encode(array("result"=>"ok","message"=>"L'élément a bien été sauvegardé"));
}
else //we send the errors in the response
{
	echo json_encode(array("result"=>"ko","message"=>"L'élément n'a pu être sauvegardé","errors"=>$oneElement->errors));
}


?>