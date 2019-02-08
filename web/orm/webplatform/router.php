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
	if(!isset($_SESSION['hierarchy']) && !isset($_SESSION['hierarchy']['filter']))
	{
		$_SESSION['hierarchy']=array();
		$_SESSION['hierarchy']["filter"]=array();
	}
	

	foreach($modelChild->arrayRelations as $key=>$value)
	{
		if($value['assoc_table']==$_GET['parentClass'])
		{
			$_SESSION['hierarchy']['filter'][$key]=$_GET['idParent'];
		}
	}
	
	//echo "<pre>";var_dump($_SESSION['hierarchy']);exit;
	//echo "<pre>";var_dump($_SESSION['pushHierarchy']);exit;
	header('Location: index.php?page='.$_GET['childClass']);
}
elseif(isset($_GET['page'])) 
{
	if(isset($_SESSION['hierarchy']["filter"]))
	{
		unset($_SESSION['hierarchy']["filter"]);
	}
	header('Location: index.php?page='.$_GET['page']);
}
elseif(isset($_GET['table'])) 
{
	
	header('Location: index.php?page='.$_GET['table']);
}
//$_SESSION['parentClass']

?>