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
 *
 */
//import of differents files
include('../libs/php/globalInfos.php');
$gb=new globalInfos();
$gb->requireModel($table);
include('../libs/php/inclusion_'.$table.'.php');//the file that instanciate the model

$filterArray=array();

if(isset($_SESSION['searchFilters'][$table]) && count($_SESSION['searchFilters'][$table])>0)
{
	$filterArray=$_SESSION['searchFilters'][$table];
}

if(isset($_SESSION['hierarchy']["filter"]) && count($_SESSION['hierarchy']["filter"])>0)
{

	if(isset($filterArray) && count($filterArray)>0)
	{
		foreach($_SESSION['hierarchy']["filter"] as $filter=>$value)
		{
			if(array_key_exists($filter, $datasManager->getFields()))
			{$filterArray[$filter]=$value;}
		}
	}
	else 
	{
		$filterArray=array();
		foreach($_SESSION['hierarchy']["filter"] as $filter=>$value)
		{
			if(array_key_exists($filter, $datasManager->getFields()))
			{$filterArray[$filter]=$value;}
		}
	}
}

if(count($filterArray)>0)
{
	$datasManager->setFilterArray($filterArray);
}

$datasManager->refreshNumElements();

//we return the titalNumPages property of the instance
echo json_encode(array("result"=>$datasManager->totalNumPages));

?>