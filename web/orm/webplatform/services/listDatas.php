<?php
session_start();
if(!isset($_SESSION['connected']))
{
	 header('Location: auth.php'); 
}
ini_set ( "display_errors" , "1" );
//echo "<pre>";var_dump($_SESSION);exit;
//we extract the differents parameters
extract($_GET);
//parameters list
/*
 *table:name of the concerned table
 *page:index of the page (used to limit the query)
 */
//import of differents files
include('../libs/php/globalInfos.php');
$gb=new globalInfos();
$gb->requireModel($table);
include('../libs/php/inclusion_'.$table.'.php');//the file that instanciate the model
$xml=$oneElement->getXmlConfig();

$dbh=$gb->globalDbh;
$gb->setGlobalXmlConfig(simplexml_load_file("../config/global_config.xml"));
$myTools=new myTools();


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

//we will return the response as a xml
$xmlRetour=new XMLWriter();
$xmlRetour->openMemory();
$xmlRetour->startDocument("1.0", "UTF-8");
$xmlRetour->startElement("table");
$xmlRetour->startElement("metadata");
		$picturesThumnailColumn=array();//array that contains the infos on the field that are pictures field in fact
		
		//we loop on the xml config file to generate the metadatas relatives to the field
		foreach($xml->children() as $key=>$value)
		{
			if($value['isGridVisible']=="true" || $value['isPrimaryKey']=="true")//if it is visible in the datagrid, we make a column
			{
				$xmlRetour->startElement('column');
					$xmlRetour->writeAttribute('name',$key);
					$xmlRetour->writeAttribute('label',$value->label);
					$xmlRetour->writeAttribute('editable','false');
					$boolHtml=false;//variable that stock a boolean that informs us if we use a html rendering for the current column
					if((string) $value->pictureField['enabled']== "true")// if the pictureField is active
					{
						//we loop on the different nodes
						foreach($value->pictureField->children() as $keyPictureConfig=>$pictureConfig)
						{
							if((string) $pictureConfig['isForAdmin']== "true")// if we define that we display the pictures in the xml
							{$boolHtml=true;$picturesThumnailColumn[$key]=(string) $pictureConfig['path'];}//we empty the array that contains the infos on the field that are pictures field 
						}
					}
					if((string) $value->pictureField['enabledForRelation']=="true")// if the pictureField is active
					{
						//we loop on the different nodes
						foreach($value->pictureField->children() as $keyPictureConfig=>$pictureConfig)
						{
							if((string) $pictureConfig['isForAdmin']== "true")// if we define that we display the pictures in the xml
							{$boolHtml=true;$picturesThumnailColumn[(string)$value->displayColumn]=(string) $pictureConfig['path'];
							}//we empty the array that contains the infos on the field that are pictures field
						}
					}
					
					if(!$boolHtml)//whether it's html or not , we set the good datatype
					{$xmlRetour->writeAttribute('datatype','string');}
					else{$xmlRetour->writeAttribute('datatype','html');}
					
					
					
				$xmlRetour->endElement();
			}
			
			
		}
		
		//we generate the metadatas relatives to the actions
		$xmlRetour->startElement('column');
			$xmlRetour->writeAttribute('name','action');
			$xmlRetour->writeAttribute('label','');
			$xmlRetour->writeAttribute('datatype','html');
			$xmlRetour->writeAttribute('editable','false');
		$xmlRetour->endElement();
	$xmlRetour->endElement();
	//var_dump($picturesThumnailColumn);
	//now we generate the real datas
	$xmlRetour->startElement("data");
	    //we put the datas in this variable
	    
		$datas=$datasManager->getDatas($page);
		//we loop on the datas
		foreach($datas as $key=>$value)
		{
			$xmlRetour->startElement("row");//and start a row
			$xmlRetour->writeAttribute("id",$key);
				foreach($value as $columnName=>$columnValue)
				{
					$xmlRetour->startElement("column");
					$xmlRetour->writeAttribute("name",$columnName);
					if(in_array($columnName, array_keys($picturesThumnailColumn)))//if the column name match with a picture field...we display an html picture
					{
						$xmlRetour->writeCData("<img src='".$picturesThumnailColumn[$columnName].$columnValue."'/>");
					}
					else //else we display text
					{
						//id there is a display column node in the xml
						if((string)$xml->{"".$columnName.""}->displayColumn!=null && (string)$xml->{"".$columnName.""}->displayColumn!="" )
						{
							//we extract the informations in the relation table
							$sqlChoicesQuery="SELECT * FROM ".$oneElement->arrayRelations[$columnName]["assoc_table"]." WHERE ".$oneElement->arrayRelations[$columnName]["assoc_col"]." ='".$columnValue."'";
							$resultChoices=$dbh->query($sqlChoicesQuery);
							$resultChoices->setFetchMode(PDO::FETCH_ASSOC);
							$resultChoices=$resultChoices->fetchAll();
							if(count(explode("|",(string)$xml->{"".$columnName.""}->displayColumn))>1)
							{
								$concatDisplay="";
								foreach(explode("|",(string)$xml->{"".$columnName.""}->displayColumn) as $columnConcat)
								{
									$concatDisplay.=$resultChoices[0][$columnConcat]." ";
								}
								$xmlRetour->text($concatDisplay);
							}
							else 
							{
								if(array_key_exists((string)$xml->{"".$columnName.""}->displayColumn,$picturesThumnailColumn))
								{
									$xmlRetour->writeCData("<img src='".$picturesThumnailColumn[(string)$xml->{"".$columnName.""}->displayColumn].$resultChoices[0][(string)$xml->{"".$columnName.""}->displayColumn]."'/>");
								}
								else 
								{
									$xmlRetour->text($resultChoices[0][(string)$xml->{"".$columnName.""}->displayColumn]);
								}
							}
							
						}
						else if((string)$xml->{"".$columnName.""}->dateField['enabled']=="true")
						{
							$xmlRetour->text(myTools::getDateFromTimeAndCustomFormat($columnValue,$gb->getGlobalInfoForNodeAndTable("global_date_format",$table)));
							
						}
						else if((string)$xml->{"".$columnName.""}->booleanField['enabled']=="true")
						{
							if($columnValue==0)
							{
								$xmlRetour->text((string)$xml->{"".$columnName.""}->booleanField->boolean_false_label);
							}
							if($columnValue==1)
							{
								$xmlRetour->text((string)$xml->{"".$columnName.""}->booleanField->boolean_true_label);
							}							
						}
						else 
						{
							//we return the value 
							$xmlRetour->text($columnValue);
						}
						
					}
					
					$xmlRetour->endElement();//and the column
				}
			$xmlRetour->endElement();//end the row
		}
		
	$xmlRetour->endElement();//end the datas
	
$xmlRetour->endElement();
$xmlRetour->endDocument();
Header("content-type: application/xml");
echo $xmlRetour->outputMemory();
?>