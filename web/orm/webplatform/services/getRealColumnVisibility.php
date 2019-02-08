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
$md=$gb->getModelInstance($table);
$xml=$md->getXmlConfig();
$arrayResult=array();
$need="no";
$index=0;
$incre=0;
foreach($xml as $key=>$child)
{
	if($child['isGridVisible']=='true' || ($child['isPrimaryKey']=='true' && $child['isGridVisible']=='false'))
	{
		if($child['isPrimaryKey']=='true' && $child['isGridVisible']=='false')
		{
			$need="yes";$index=$incre;
		}
		$incre++;
	}
}

echo json_encode(array('need'=>$need,'index'=>$index));exit;

