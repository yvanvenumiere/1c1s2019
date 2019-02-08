<?php
ini_set ( "display_errors" , "1" );
session_start();
if(!isset($_SESSION['connected']))
{
	 header('Location: auth.php'); 
}
//we extract the get parameters
//extract($_GET);
if(isset($_GET['parentClass']) && isset($_GET['childClass']) && isset($_GET['idParent']))
{
	include('libs/php/globalInfos.php');//global info class, very usefull
	$gb=new globalInfos();
	$gb->requireModel($_GET['childClass']);
	$modelChild=$gb->getModelInstance($_GET['childClass']);
	$_SESSION['pushHierarchy']=array();
	$_SESSION['pushHierarchy']["filter"]=array();
	$_SESSION['pushHierarchy']['parentClass']=$_GET['parentClass'];
	$_SESSION['pushHierarchy']['childClass']=$_GET['childClass'];
	//$_SESSION['pushHierarchy']['ariane']="<a href="
	//$_SESSION['pushHierarchyFilter']
	foreach($modelChild->arrayRelations as $key=>$value)
	{
		if($value['assoc_table']==$_GET['parentClass'])
		{
			$_SESSION['pushHierarchy']['filter'][$key]=$_GET['idParent'];
		}
	}
	
	
	//echo "<pre>";var_dump($_SESSION['pushHierarchy']);exit;
	header('Location: index.php?page='.$_GET['childClass']);
}
//$_SESSION['parentClass']

?>