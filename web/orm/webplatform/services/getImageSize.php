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
/*id:id for the entry in the table
 *column:name of the concerned column
 *table:name of the concerned table
 */
//import of differents files
include('../libs/php/globalInfos.php');
$gb=new globalInfos();
$gb->requireModel($table);
//$xml=simplexml_load_file("../config/".$table.".xml");//the related xml // effacer quand c'est bon pour $xml=$oneElement->getXmlConfig();
include('../libs/php/inclusion_'.$table.'.php');//the file that instanciate the model
$xml=$oneElement->getXmlConfig();

$oneElement->initFromDatas(array($datasManager->getPrimaryKey()=>$id));//we get the datas relatives this element in database
if($oneElement->get($column)!="" || $oneElement->get($column)!=null)//if the element exists
{
	$retour=array();//we initialize a variable that will contains the dimensions
	$iInfos=0;//variable just for increment
	
	//we loop the picture field node with the column name
	foreach($xml->{"".$column.""}->pictureField->picture as $key=>$pic)
	{
		
		//we take the first node in the xml for the dimensions 
		if($iInfos==0)
		{
			$retour["path"]=$pic['path'].$oneElement->get($column);
			
		}
		
		//the pictures admin always have the priority
		if($pic['isForAdmin']=='true')
		{
			$retour["path"]=$pic['path'].$oneElement->get($column);
		}
		$iInfos++;
	}
	
	//we get the dimensions of the picture
	$infos=getimagesize('../'.$retour['path']);
	if(!$infos)//if error ...
	{
		echo json_encode(array("result"=>"ko","message"=>"aucun media trouvé"));exit;
	}
	else//we empty 
	{
		$retour["width"]=$infos[0];
		$retour["height"]=$infos[1];
	}
	//we return a response
	$retour["result"]="ok";
	echo json_encode($retour);exit;
	
}
else //fail to find a media
{
	//we return a response
	echo json_encode(array("result"=>"ko","message"=>"aucun media trouvé"));exit;
}


?>