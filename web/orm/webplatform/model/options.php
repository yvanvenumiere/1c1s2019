<?php 
			
			/*
			 * This file is part of dbboh package.
			 * (c) 2012 Yvan Vénumière <yvan.venumierer@gmail.com>
			 *
			 * For the full copyright and license information, please view the LICENSE
			 * file that was distributed with this source code.
			 */
			
			/**
			 * options is a database object that can persist, delete and create forms for the options table
			 *
			 */
			
			class options extends baseModel
			{
				public $isSavedElement=false;
				public $checker;
				public $errors=array();
				
			   /**
			    * constructor for the class
			    */
				public function __construct($rewriting=false)
				{
					
					//we define this simple information => the table's name
					$this->tableName="options";
					
					parent::__construct($rewriting);
					
					
				$this->arrayFields["idoptions"]=array
				(
					"type"=>"int(11)",
					"canBeNull"=>"NO",
					"key"=>"PRI",
					"default"=>"",
					"extra"=>"auto_increment",
					"formLabel"=>"idoptions"
				);
			
				$this->arrayFields["opt_label"]=array
				(
					"type"=>"varchar(100)",
					"canBeNull"=>"YES",
					"key"=>"",
					"default"=>"",
					"extra"=>"",
					"formLabel"=>"opt_label"
				);
			
				$this->arrayFields["opt_description"]=array
				(
					"type"=>"varchar(300)",
					"canBeNull"=>"YES",
					"key"=>"",
					"default"=>"",
					"extra"=>"",
					"formLabel"=>"opt_description"
				);
			
				$this->arrayFields["opt_code"]=array
				(
					"type"=>"varchar(45)",
					"canBeNull"=>"YES",
					"key"=>"",
					"default"=>"",
					"extra"=>"",
					"formLabel"=>"opt_code"
				);
			
				$this->arrayFields["opt_is_visible"]=array
				(
					"type"=>"tinyint(4)",
					"canBeNull"=>"YES",
					"key"=>"",
					"default"=>"1",
					"extra"=>"",
					"formLabel"=>"opt_is_visible"
				);
			
				$this->arrayFields["opt_is_cool"]=array
				(
					"type"=>"tinyint(4)",
					"canBeNull"=>"YES",
					"key"=>"",
					"default"=>"1",
					"extra"=>"",
					"formLabel"=>"opt_is_cool"
				);
			
					
					
					$this->checker=new formCheck();
					
					//we define the xml datas with this function
					$this->defineXml();
					
					$this->majRelationsForAdmin();
				}
				
				/**
			    * function the map the xml datas with some variables for the instance
			    *
			    */
				protected function majWithXmlDatas()
				{
					//we explore the different fields in the pre constructed array
					foreach($this->arrayFields as $key=>$value)
					{
						//and we affect the diffferents values
						$this->arrayFields[$key]["formLabel"]=$this->xml->{"".$key.""}->label;
						$this->arrayFields[$key]["typeChecking"]=$this->xml->{"".$key.""}->cheking;
						$this->arrayFields[$key]["isVisibleInForm"]=$this->xml->{"".$key.""}["isVisibleInForm"];
						$this->arrayFields[$key]["isPictureField"]=$this->xml->{"".$key.""}->pictureField["enabled"];
						$this->arrayFields[$key]["isFileField"]=$this->xml->{"".$key.""}->fileField["enabled"];
						$this->arrayFields[$key]["isBooleanField"]=$this->xml->{"".$key.""}->booleanField["enabled"];
						if($this->xml->{"".$key.""}->booleanField["enabled"]=="true")
						{
							$this->arrayFields[$key]["true_label"]=$this->xml->{"".$key.""}->booleanField->boolean_true_label;
							$this->arrayFields[$key]["false_label"]=$this->xml->{"".$key.""}->booleanField->boolean_false_label;
						}
						$this->arrayFields[$key]["isDateField"]=$this->xml->{"".$key.""}->dateField["enabled"];
						$this->arrayFields[$key]["isRichTextEditorField"]=$this->xml->{"".$key.""}->richTextEditorField["enabled"];
						$this->arrayFields[$key]["isColorPickerField"]=$this->xml->{"".$key.""}->colorPickerField["enabled"];	
						$this->arrayFields[$key]["isFilterVisible"]=$this->xml->{"".$key.""}["isFilterVisible"];
						
						if($this->xml->{"".$key.""}->displayColumn!="")
						{
							$this->arrayFields[$key]["displayColumn"]=$this->xml->{"".$key.""}->displayColumn;
						}
						
						//if the pictureFied entry is set to true , we do what we got to do
						if($this->xml->{"".$key.""}->pictureField["enabled"]=="true")
						{
							$this->arrayFields[$key]["pictureField"]=array();
							foreach($this->xml->{"".$key.""}->pictureField->picture as $picture)
							{
								$arrayOneType=array 
								(
									"resizeType"=>(string) $picture["resizeType"],
									"width"=>(string) $picture["width"],
									"height"=>(string) $picture["height"],
									"path"=>(string) $picture["path"],
									"formats"=>array()
								);
								foreach($picture->acceptedFormats->format as $format)
								{
									$arrayOneType["formats"][]=(string)  $format;
								}
								$this->arrayFields[$key]["pictureField"][]=$arrayOneType;
							}
						}
						
						//if the fileFied entry is set to true , we do what we got to do
						if($this->xml->{"".$key.""}->fileField["enabled"]=="true")
						{
							$this->arrayFields[$key]["fileField"]=array();
							foreach($this->xml->{"".$key.""}->fileField->aFile as $file)
							{
								$arrayOneType=array 
								(
									"path"=>$file["path"],
									"formats"=>array()
								);
								foreach($file->acceptedFormats->format as $format)
								{
									$arrayOneType["formats"][]=$format;
								}
								$this->arrayFields[$key]["fileField"][]=$arrayOneType;
							}
						}
					}
				}    
				
				/**
			    * method that set all the primary key fields that are not the firsts to MUL to we save the relations
			    *
			    */
				public function majRelationsForAdmin()
				{
					foreach($this->arrayRelations as $key=>$value)
					{
						$this->arrayFields[$key]["key"]="MUL";
					}
				}
				
				/**
			    * method that allows to init the instance with some datas...
			    * @param array $datas  array that contains keys that map to te columns's name in our table and value contains the wanted values
			    */
				public function initWithDatas($datas)
				{
					foreach($datas as $key=>$value)
					{
						if(in_array($key,array_keys($this->arrayFields)))
						{
							$this->arrayFields[$key]["currentValue"]=$value;
						}
					}
				}
				
				
				/**
				* methods that returns the differents fields of of the model
			    * @param string $field  the name of the column of the field
				* @return html returns an html string for the form
			    */
				public function getFormFromField($field,$contextField)
				{
					//if the field is in the the database and is available for form , we can include it in the response
					if(in_array($field,array_keys($this->arrayFields)) && ($this->arrayFields[$field]["isVisibleInForm"]=="true" && $contextField=="ficheContext") || ($this->arrayFields[$field]["isFilterVisible"]=="true" && $contextField=="filterContext"))
					{
						$preValue;//variable that contains the current saved value if it exists
						if($this->arrayFields[$field]["key"]!="MUL")//if it is not a relation
						{
							if($this->arrayFields[$field]["isPictureField"]=="false" && $this->arrayFields[$field]["isFileField"]=="false")//if it is not a picture or a file
							{
								//if it is a small or medium text
								if (preg_match("#varchar#", $this->arrayFields[$field]["type"]))
								{
									if($this->superReplace(array("varchar"=>"","("=>"",")"=>""),$this->arrayFields[$field]["type"])<=150)
									{
										if($this->arrayFields[$field]["isColorPickerField"]=="true")
										{
											return "<label>".$this->arrayFields[$field]["formLabel"]."</label>  <input class=\"formField colorPickerClass ".$contextField."\" id=\"".$field."_s_".$contextField."\" type=\"text\" value=\"".($this->isSavedElement?$this->arrayFields[$field]["currentValue"]:"")."\"/>";
										}
										else
										{
											return "<label>".$this->arrayFields[$field]["formLabel"]."</label>  <input class=\"formField ".$contextField."\" id=\"".$field."_s_".$contextField."\" type=\"text\" value=\"".($this->isSavedElement?$this->arrayFields[$field]["currentValue"]:"")."\"/>";
										}
										
									}
									else
									{							
										return "<label>".$this->arrayFields[$field]["formLabel"]."</label> <textarea class=\"formField ".$contextField."\" id=\"".$field."_s_".$contextField."\">".($this->isSavedElement?$this->arrayFields[$field]["currentValue"]:"")."</textarea>";
									}
								}
								
								//if it is a text
								if(preg_match("#text#", $this->arrayFields[$field]["type"]))
								{
									if($this->arrayFields[$field]["isRichTextEditorField"]=="true")
									{
										return "<label>".$this->arrayFields[$field]["formLabel"]."</label> <textarea class=\"formField ".$contextField." rteClass\" id=\"".$field."_s_".$contextField."\">".($this->isSavedElement?$this->arrayFields[$field]["currentValue"]:"")."</textarea>";
									}
									else 
									{
										return "<label>".$this->arrayFields[$field]["formLabel"]."</label> <textarea class=\"formField ".$contextField."\" id=\"".$field."_s_".$contextField."\">".($this->isSavedElement?$this->arrayFields[$field]["currentValue"]:"")."</textarea>";
									}
								}
								
								//if it is a number
								if(preg_match("#int#", $this->arrayFields[$field]["type"]))
								{
									if($this->arrayFields[$field]["isBooleanField"]=="true")//it can be a boolean
									{
										return "<br/><br/><label class=\"cbLabel\">".$this->arrayFields[$field]["formLabel"]."</label> <br/> <select class=\"formField ".$contextField." boolField\" id=\"".$field."_s_".$contextField."\"><option value=\"0\" ".(($this->isSavedElement && $this->arrayFields[$field]["currentValue"]==0)?"selected=\"selected\"":"").">".$this->arrayFields[$field]["false_label"]."</option><option value=\"1\" ".(($this->isSavedElement && $this->arrayFields[$field]["currentValue"]==1)?"selected=\"selected\"":"").">".$this->arrayFields[$field]["true_label"]."</option></select><br/><br/>";
									}
									
									if($this->arrayFields[$field]["isDateField"]=="true")//it can be a date field
									{
										return "<label>".$this->arrayFields[$field]["formLabel"]."</label>  <input class=\"formField ".$contextField." pickerClass\" id=\"".$field."_s_".$contextField."\" type=\"text\" value=\"".($this->isSavedElement?myTools::getDateFromTimeAndCustomFormat($this->arrayFields[$field]["currentValue"],$this->gInfos->getGlobalInfoForNodeAndTable("global_date_format",$this->tableName)):"")."\"/>";
									}
									return "<label>".$this->arrayFields[$field]["formLabel"]."</label>  <input class=\"formField ".$contextField."\" id=\"".$field."_s_".$contextField."\" type=\"text\" value=\"".($this->isSavedElement?$this->arrayFields[$field]["currentValue"]:"")."\"/>";
								}

								if(preg_match("#decimal#", $this->arrayFields[$field]["type"]))
								{
									return "<label>".$this->arrayFields[$field]["formLabel"]."</label>  <input class=\"formField ".$contextField."\" id=\"".$field."_s_".$contextField."\" type=\"text\" value=\"".($this->isSavedElement?$this->arrayFields[$field]["currentValue"]:"")."\"/>";
								}
								
								if(preg_match("#double#", $this->arrayFields[$field]["type"]))
								{
									return "<label>".$this->arrayFields[$field]["formLabel"]."</label>  <input class=\"formField ".$contextField."\" id=\"".$field."_s_".$contextField."\" type=\"text\" value=\"".($this->isSavedElement?$this->arrayFields[$field]["currentValue"]:"")."\"/>";
								}
							}
							else //if it is a picture or a file
							{
								if($this->arrayFields[$field]["isPictureField"]=="true")//if it is a picture
								{return "<div id=\"browse".$field."\" class=\"uploadField pictureField\">".$this->arrayFields[$field]["formLabel"]." <img src=\"medias/images/dl_small.png\"/> <input class=\"formField ".$contextField."\" id=\"".$field."_s_".$contextField."\" type=\"hidden\" value=\"".($this->isSavedElement?$this->arrayFields[$field]["currentValue"]:"")."\"/></div> ";}
								else//if it is a file
								{return "<div id=\"browse".$field."\" class=\"uploadField fileField\">".$this->arrayFields[$field]["formLabel"]."  <img src=\"medias/images/dl_small.png\"/> <input class=\"formField ".$contextField."\" id=\"".$field."_s_".$contextField."\" type=\"hidden\" value=\"".($this->isSavedElement?$this->arrayFields[$field]["currentValue"]:"")."\"/></div> ";}
							}
							
						}
						else //if it is a relation
						{
							//we extract the informations in the relation table
							$sqlChoicesQuery="SELECT * FROM ".$this->arrayRelations[$field]["assoc_table"]."";
							if($this->xml->{"".$field.""}->displayColumn!="" && $this->xml->{"".$field.""}->displayColumn["sortColumn"]!="")
							{
								$sqlChoicesQuery.=" ORDER BY ".$this->xml->{"".$field.""}->displayColumn["sortColumn"];
							}
							$resultChoices=$this->dbh->query($sqlChoicesQuery);
							$resultChoices->setFetchMode(PDO::FETCH_ASSOC);
							$resultChoices=$resultChoices->fetchAll();
							//and we do a select list
							$select="<label>".$this->arrayFields[$field]["formLabel"]."</label>  <select class=\"formField ".$contextField."\" id=\"".$field."_s_".$contextField."\">";
							$select.="<option value=''>Aucun</option>";
							foreach($resultChoices as $choice)
							{
								
								$selected="";//we select the selected value if it is ever saved
								if($this->isSavedElement && $this->arrayFields[$field]["currentValue"]==$choice[$this->arrayRelations[$field]["assoc_col"]])
								{
									$selected="selected=\"selected\"";
								}
								
								//we choose the good value to display , if we have a columns info for the display, it is perfect, else we display the first relation field
								if(isset($this->arrayFields[$field]["displayColumn"]))
								{
									
									if(count(explode("|",(string) $this->arrayFields[$field]["displayColumn"]))>1)
									{
										$concatDisplay="";
										foreach(explode("|",(string) $this->arrayFields[$field]["displayColumn"]) as $columnConcat)
										{
											$concatDisplay.=$choice[$columnConcat]." ";
										}
										$select.="<option ".$selected." value=\"".$choice[$this->arrayRelations[$field]["assoc_col"]]."\">".$concatDisplay."</option>";
									}
									else 
									{
										$select.="<option ".$selected." value=\"".$choice[$this->arrayRelations[$field]["assoc_col"]]."\">".$choice[ (string) $this->arrayFields[$field]["displayColumn"]]."</option>";
									}
									
								}
								else 
								{
									$select.="<option ".$selected." value=\"".$choice[$this->arrayRelations[$field]["assoc_col"]]."\">".$choice[$this->arrayRelations[$field]["assoc_col"]]."</option>";	
								}
							}
							$select.="</select>";
							return $select;
						}
						
					}
				}

				/**
				* methods that  acts like strreplace
			    * @param string $arrayMasks string that we have to replace
			    * @param string $subject string on which we do the change
				* @return string real value
			    */
				public function superReplace($arrayMasks,$subject)
				{
					$retour=$subject;
					foreach($arrayMasks as $mask=>$replace)
					{
						$retour=str_replace($mask,$replace,$retour);
					}
					return $retour;
				}
				
				/**
				* methods that set the label for a form element
			    * @param string $field string that gives the name of the field
			    * @param string $subject string that gives the label for the form element 
			    */
				public function setFormLabel($field,$label)
				{
					$this->arrayFields[$field]["formLabel"]=$label;
				}
				
				/**
				* methods that set the label for a form element (select list)
			    * @param string $field string that gives the name of the field
			    * @param string $columnName string that gives the name of the column used to display the informations
			    */
				public function setFormMultipleSelectDisplayValue($field,$columnName)
				{
					$this->arrayFields[$field]["displayValue"]=$columnName;
				}
				
				/**
			    * method that allows to init the instance from some datas...
			    * @param array $arrayFilters array that the keys are the column's name and the values match to the values in the database
			    * @return boolean true if the element is found and false if not
			    */
				public function initFromDatas($arrayFilters)
				{
					$theWhere="WHERE ";
					$queryHelper=array();
					$firstTW=true;
					foreach($arrayFilters as $key=>$value)
					{
						if(in_array($key,array_keys($this->arrayFields)))
						{
							$queryHelper[":".$key.""]=$value;
							if($firstTW)
							{
								$firstTW=false;
								$theWhere.=$key."=:".$key."";
							}
							else 
							{
								$theWhere.=" AND ".$key."=:".$key."";
							}
						}
					}
					
					
					$sqlReq="SELECT * FROM options ".$theWhere." LIMIT 1";
					$prepa=$this->dbh->prepare($sqlReq);
			
					$prepa->execute($queryHelper);
					$prepa->setFetchMode(PDO::FETCH_ASSOC);
					$result=$prepa->fetchAll();
					if($result)
					{
						$this->initWithDatas($result[0]);
						$this->isSavedElement=true;
						return true;
					}
					else 
					{
						return false;	
					}
				}
				
				/**
			    * method that returns the value of a field 
			    * @param string $columnName string that reprensents the name of the column in the database
			    * @return the value of the field
			    */
				public function get($columnName)
				{
					if(!in_array($columnName,array_keys($this->arrayFields)))
					{return false;}
					
					
					if(!$this->arrayFields[$columnName]["currentValue"])
					{return false;}
					
					return $this->arrayFields[$columnName]["currentValue"];
					
				}
				
				/**
				 * method that returns the datas for all colums
				 * @return array
				 */
				public function getDatas()
				{
					$result=array();
					
					foreach($this->arrayFields as $key=>$value)
					{
						$result[$key]=$value["currentValue"];
					}
						
					return $result;
						
				}
				
				/**
			    * method that returns the xml confif object

			    * @return the xml confif object
			    */
				public function getXmlConfig()
				{
					return $this->xml;
				}
				
				/**
			    * method that set the value of a field 
			    * @param string $columnName string that reprensents the name of the column in the database
			    * @param $value the value of the field
			    */
				public function set($columnName,$value)
				{
					if(!in_array($columnName,array_keys($this->arrayFields)))
					{return false;}
					
					
					$this->arrayFields[$columnName]["currentValue"]=$value;
					
				}
				
				/**
			    * method that delete the element if it is a saved element
			    * @return boolean true  if the operation succeded , else false
			    */
				public function delete()
				{
					if($this->isSavedElement)
					{
						//definition of the where clause
						$theWhere="WHERE ".$this->getPrimaryKeyField()."=:".$this->getPrimaryKeyField();
						$queryHelper=array(":".$this->getPrimaryKeyField().""=>$this->arrayFields[$this->getPrimaryKeyField()]["currentValue"]);
						$sqlDelete="DELETE FROM options ".$theWhere."";
						$prepa=$this->dbh->prepare($sqlDelete);
			
						if($prepa->execute($queryHelper))
						{
							foreach($this->arrayFields as $key=>$value)
							{
								//we tcheck if it is a picture field
								if((string)$value["isPictureField"]=="true")
								{
									foreach($value["pictureField"] as $keyP=>$valueP)//if yes we delete the medias
									{
										@unlink("../".$valueP["path"].$value["currentValue"]);
									}	
								}
								
								//we tcheck if it is a file field
								if((string)$value["isFileField"]=="true")
								{
									foreach($value["fileField"] as $keyF=>$valueF)//if yes we delete the medias
									{
										@unlink("../".$valueF["path"].$value["currentValue"]);
									}	
								}
								
								$this->executeCallBack("onDelete");
								
							}
							return true;
						}
						else 
						{
							return false;	
						}
						
					}
					else 
					{
						return false;	
					}
				}
				
				/**
			    * method that save a file before save a element in the database 
			    * @return boolean true  if the operation succeded , else false
			    */
				private function saveFile($field)
				{
					$now=time();
					$extension=".".myTools::getExtension($this->arrayFields[$field]["currentValue"]);
					foreach($this->arrayFields[$field]["fileField"] as $key=>$value)
					{
						if(!in_array($extension,$value["formats"]))
						{
							
							return false;
						}
						if(!file_exists("../tmp/".$this->arrayFields[$field]["currentValue"]))
						{
							return true;
						}
						if(copy("../tmp/".$this->arrayFields[$field]["currentValue"], "../".(string) $value["path"].$now.$extension))
						{$this->arrayFields[$field]["currentValue"]=$now.$extension;return true;}
					}
				}
				
				
				/**
			    * method that executes a callback defined in the xml
			    * @param string $callbackType type of call back
			    * @return string field name  if the operation succeded , else false
			    */
				private function executeCallBack($callbackType)
				{
					//we check if there is any callback associated with this action
					if((string)$this->xml->callbacks->{$callbackType}["fileName"]!="" && (string)$this->xml->callbacks->{$callbackType}["className"]!="" && (string)$this->xml->callbacks->{$callbackType}["methodName"]!="")
					{
						//if so , we trigger a method defined in the xml ...
						require($this->gInfos->getRootPathFromHere()."callbacks/".(string)$this->xml->callbacks->{$callbackType}["fileName"]);
						$className = (string)$this->xml->callbacks->{$callbackType}["className"];  
						$myClass=new ReflectionClass($className); 
						$instance = $myClass->newInstance();
						$methodName=(string)$this->xml->callbacks->{$callbackType}["methodName"];
						if($myClass->hasMethod($methodName))
						{
							$trigger=$instance->$methodName($this);
						}
					}
				}
				
				/**
			    * method that adds an error to error array
			    */
				private function addError($arrayName,$value)
				{
					if(!isset($this->errors[$arrayName]))
					{
						$this->errors[$arrayName]=array();
					}
					$this->errors[$arrayName][]=$value;
				}
				
				/**
			    * method that save a picture before save a element in the database 
			    * @param string columnname(fieldname) in the database
			    * @return boolean true  if the operation succeded , else false
			    */
				private function savePictures($field)
				{
					$now=time();//we define a name for the file
					$extension=".".myTools::getExtension($this->arrayFields[$field]["currentValue"]);//we define the extension
					
					//we loop on the differents formats 
					foreach($this->arrayFields[$field]["pictureField"] as $key=>$value)
					{
						
						if(!in_array($extension,$value["formats"]))
						{
							return false;
						}
						//we do the good transformation looking at the xml
						switch ($value["resizeType"]) 
						{
							case "resizeHomo":
								//if the file doesn't exists in a directory, it is not a big deal
								if(!file_exists("../tmp/".$this->arrayFields[$field]["currentValue"]))
								{
									return true;
								}
								
								$imgHandler=new imgSaver(str_replace(".","",$extension),"../tmp/".$this->arrayFields[$field]["currentValue"]);
								if(!$imgHandler->init() || !$imgHandler->handleImage())
								{
									return false;
								}
								$imgHandler->resizeHomo((int) $value["ratio"],"pictureSave");
								if(!$imgHandler->saveImg("../".(string) $value["path"].$now,"pictureSave"))
								{
									return false;
								}
								
							break;
								
							case "resizeFix":
								//if the file doesn't exists in a directory, it is not a big deal
								if(!file_exists("../tmp/".$this->arrayFields[$field]["currentValue"]))
								{
									return true;
								}
								$imgHandler=new imgSaver(str_replace(".","",$extension),"../tmp/".$this->arrayFields[$field]["currentValue"]);
								if(!$imgHandler->init() || !$imgHandler->handleImage())
								{
									
									return false;
								}
								$imgHandler->resizeFix((int) $value["width"],(int) $value["height"],"pictureSave");
								if(!$imgHandler->saveImg("../".(string) $value["path"].$now,"pictureSave"))
								{
									return false;
								}
								
							break;
								
							case "resizeHomoW":
								//if the file doesn't exists in a directory, it is not a big deal
								if(!file_exists("../tmp/".$this->arrayFields[$field]["currentValue"]))
								{
									return true;
								}
								$imgHandler=new imgSaver(str_replace(".","",$extension),"../tmp/".$this->arrayFields[$field]["currentValue"]);
								if(!$imgHandler->init() || !$imgHandler->handleImage())
								{
									return false;
								}
								$imgHandler->resizeHomoW((int) $value["width"],"pictureSave");
								if(!$imgHandler->saveImg("../".(string) $value["path"].$now,"pictureSave"))
								{
									return false;
								}
							break;
								
							case "resizeHomoH":
								//if the file doesn't exists in a directory, it is not a big deal
								if(!file_exists("../tmp/".$this->arrayFields[$field]["currentValue"]))
								{
									return true;
								}
								$imgHandler=new imgSaver(str_replace(".","",$extension),"../tmp/".$this->arrayFields[$field]["currentValue"]);
								if(!$imgHandler->init() || !$imgHandler->handleImage())
								{
									return false;
								}
								$imgHandler->resizeHomoH((int) $value["height"],"pictureSave");
								if(!$imgHandler->saveImg("../".(string) $value["path"].$now,"pictureSave"))
								{
									return false;
								}
								
							break;
							
							default:
								//if the file doesn't exists in a directory, it is not a big deal
								if(!file_exists("../tmp/".$this->arrayFields[$field]["currentValue"]))
								{
									return true;
								}
								$imgHandler=new imgSaver(str_replace(".","",$extension),"../tmp/".$this->arrayFields[$field]["currentValue"]);
								if(!$imgHandler->init() || !$imgHandler->handleImage())
								{
									return false;
								}
								$imgHandler->noResize("pictureSave");
								if(!$imgHandler->saveImg("../".(string) $value["path"].$now,"pictureSave"))
								{
									return false;
								}
								
								
							break;
						}	
					}
					$this->arrayFields[$field]["currentValue"]=$now.$extension;
					return true;
					
				}
				
				
				/**
			    * method that save an element in the database
			    * @param array $arrayFieldNames array of specific values to save in the database
			    * @return boolean true  if the operation succeded , else false
			    */
				public function save($arrayFieldNames=null)
				{
					if(!$this->checkFields())
					{
						return false;
					}
					if($this->isSavedElement)
					{
						return $this->update();
					}
					
					//variable that contains the differents fields to save
					$toSave=array();
					
					$queryHelper=array();
					
					//if the $arrayFieldNames param exists
					if($arrayFieldNames)
					{
						foreach($arrayFieldNames as $fieldName)
						{
							if(in_array($fieldName,array_keys($this->arrayFields)) && isset($this->arrayFields[$fieldName]["currentValue"]))
							{
								//we tcheck if there is picturefield
								if(isset($this->arrayFields[$fieldName]["pictureField"]) && $this->arrayFields[$fieldName]["currentValue"]!="")
								{
									if(!$this->savePictures($fieldName))
									{
										$this->addError("pictureSaving",array($fieldName=>"fichier non enregistré"));
										return false;
									}
								}
								
								//we tcheck if there is filefield
								if(isset($this->arrayFields[$fieldName]["fileField"]) && $this->arrayFields[$fieldName]["currentValue"]!="")
								{
									if(!$this->saveFile($fieldName))
									{
										$this->addError("fileSaving",array($fieldName=>"fichier non enregistré"));
										return false;
									}
								}
								$toSave[]=$fieldName;
								$toSaveBinding[]=":".$fieldName;
								$queryHelper[":".$fieldName.""]=$this->arrayFields[$fieldName]["currentValue"];
							}
						}
					}
					else 
					{
						foreach($this->arrayFields as $key=>$value)
						{
							if($key!=$this->getPrimaryKeyField() && isset($this->arrayFields[$key]["currentValue"]))
							{
								//we tcheck if there is picturefield
								if(isset($this->arrayFields[$key]["pictureField"]) && $this->arrayFields[$key]["currentValue"]!="")
								{
									if(!$this->savePictures($key))
									{
										$this->addError("pictureSaving",array($key=>"fichier non enregistré"));
										return false;
									}
								}
								
								//we tcheck if there is filefield
								if(isset($this->arrayFields[$key]["fileField"]) && $this->arrayFields[$key]["currentValue"]!="")
								{
									if(!$this->saveFile($key))
									{
										$this->addError("fileSaving",array($key=>"fichier non enregistré"));
										return false;
									}
								}
								$toSave[]=$key;
								$toSaveBinding[]=":".$key;
								$queryHelper[":".$key.""]=$this->arrayFields[$key]["currentValue"];
							}
						}
					}
					
					//now we can insert all the thing in the database
					$sqlInsert="INSERT INTO options(".implode(",",$toSave).") values(".implode(",",$toSaveBinding).")";
					$insertion=$this->dbh->prepare($sqlInsert);
					if($insertion->execute($queryHelper))
					{
						$this->isSavedElement=true;// we can set this boolean correctly
						
						$this->executeCallBack("onSave");
						//we browse the different fields, end try to return the real primary key field
						foreach($this->arrayFields as $key=>$value)
						{
							if($this->getPrimaryKeyField() && $this->getPrimaryKeyField()==$key)
							{
								$this->arrayFields[$key]["currentValue"]=$this->dbh->lastInsertId();
								
								return $this->arrayFields[$key]["currentValue"];
							}
						}
						return true;
					}
					else 
					{
						$this->addError("saving","problème lors de l'insertion en base de données");
						return false;	
					}
					
				}
				
				/**
			    * method that check all the fields 
			    * @return boolean true  if the operation succeded , else false
			    */
				public function checkFields()
				{
					/*foreach($this->arrayFields as $field=>$value)
					{
						if($value["isVisibleInForm"]==true && in_array((string) $value["typeChecking"],$this->checker->arrayTypeChecking))
						{
							$this->checker->addCheck($field,$value["currentValue"],(string) $value["typeChecking"]);
						}
						
						
					}
					if($this->checker->isValid())
					{
						return true;
					}
					else 
					{
						$this->errors["fields"]=$this->checker->arrayResult;
						return false;	
					}*/
					
					
					$bddCheck=array();
					$bddValid=true;
					foreach($this->arrayFields as $field=>$value)
					{
							if(isset($value["currentValue"]))
							{
								if($value["isVisibleInForm"]==true && in_array((string) $value["typeChecking"],$this->checker->arrayTypeChecking))
								{
									$this->checker->addCheck($field,$value["currentValue"],(string) $value["typeChecking"]);
								}
								
								if($value["canBeNull"]=="NO" && ($value["currentValue"]==NULL || $value["currentValue"]=="") && $field!=$this->getPrimaryKeyField())
								{
									$bddCheck[$field]=array("isValid"=>false);
									$bddValid=false;
								}
							}
							
					}
					
					if($this->checker->isValid() && $bddValid==true)
					{
						return true;
					}
					else 
					{
						$this->errors["fields"]=array_merge($this->checker->arrayResult,$bddCheck);
						return false;	
					}
				}
				
				/**
			    * method that updates an element in the database
			    * @param array $arrayFieldNames array of specific values to save in the database
			    * @return boolean true  if the operation succeded , else false
			    */
				public function update($arrayFieldNames=null)
				{
					if(!$this->checkFields())
					{
						return false;
					}
					
					if($this->isSavedElement)
					{
						//variable that contains the differents fields to save
						$toSave=array();
					
						$queryHelper=array();
						//if the $arrayFieldNames param exists
						if($arrayFieldNames)
						{
							foreach($arrayFieldNames as $fieldName)
							{
								if(in_array($fieldName,array_keys($this->arrayFields)))
								{
									if(isset($this->arrayFields[$fieldName]["pictureField"]) && $this->arrayFields[$fieldName]["currentValue"]!="")
									{
										if(!$this->savePictures($fieldName)){return false;}
									}
									$toSave[]="".$fieldName."=:".$fieldName;
									$queryHelper[":".$fieldName.""]=$this->arrayFields[$fieldName]["currentValue"];
								}
							}
						}
						else 
						{
							foreach($this->arrayFields as $key=>$value)
							{
								if($key!=$this->getPrimaryKeyField())
								{
									//we tcheck if there is picturefield
									if(isset($this->arrayFields[$key]["pictureField"]) && $this->arrayFields[$key]["currentValue"]!="")
									{
										if(!$this->savePictures($key)){return false;}
									}
									$toSave[]="".$key."=:".$key;
									
									$queryHelper[":".$key.""]=$this->arrayFields[$key]["currentValue"];
								}
							}
						}
						
						$queryHelper[":".$this->getPrimaryKeyField().""]=$this->arrayFields[$this->getPrimaryKeyField()]["currentValue"];
						
						$sqlUpdate="UPDATE options SET ".implode(",",$toSave)." WHERE ".$this->getPrimaryKeyField()."=:".$this->getPrimaryKeyField()."";
						$update=$this->dbh->prepare($sqlUpdate);
						if($update->execute($queryHelper))
						{
							$this->executeCallBack("onUpdate");
							return true;
						}
						else 
						{
							return false;
						}
						
						
					}
					else 
					{
						return false;	
					}
				}

				/**
			    * method that return the real primary key field for the associated table for this model, the first field in a certain way
			    * @return string field name  if the operation succeded , else false
			    */
				public function getPrimaryKeyField()
				{
					foreach($this->arrayFields as $key=>$value)
					{
						if($value["key"]=="PRI" && $value["extra"]=="auto_increment")
						{
							//$this->arrayField[$key]["currentValue"]=$this->dbh->lastInsertId();
							return $key;
						}
					}
					return false;
				}
			}
			
			
			/**
			 * optionsDatasManager is a database object that list datas from the options table
			 *
			 */
			
			class optionsDatasManager extends baseModel
			{
				
				public $arrayElements=array();
				public $reqLimit;
				public $currentPaginationNumber;
				public $totalNumElements;
				public $totalNumPages;
				private $filterArray=array();
				
				
				/**
			    * constructor for the class
			    */
				public function __construct($rewriting=false)
				{
					//we define this simple information => the table's name
					$this->tableName="options";
					
					parent::__construct($rewriting);
					
					
				$this->arrayFields["idoptions"]=array
				(
					"type"=>"int(11)",
					"canBeNull"=>"NO",
					"key"=>"PRI",
					"default"=>"",
					"extra"=>"auto_increment",
					"formLabel"=>"idoptions"
				);
			
				$this->arrayFields["opt_label"]=array
				(
					"type"=>"varchar(100)",
					"canBeNull"=>"YES",
					"key"=>"",
					"default"=>"",
					"extra"=>"",
					"formLabel"=>"opt_label"
				);
			
				$this->arrayFields["opt_description"]=array
				(
					"type"=>"varchar(300)",
					"canBeNull"=>"YES",
					"key"=>"",
					"default"=>"",
					"extra"=>"",
					"formLabel"=>"opt_description"
				);
			
				$this->arrayFields["opt_code"]=array
				(
					"type"=>"varchar(45)",
					"canBeNull"=>"YES",
					"key"=>"",
					"default"=>"",
					"extra"=>"",
					"formLabel"=>"opt_code"
				);
			
				$this->arrayFields["opt_is_visible"]=array
				(
					"type"=>"tinyint(4)",
					"canBeNull"=>"YES",
					"key"=>"",
					"default"=>"1",
					"extra"=>"",
					"formLabel"=>"opt_is_visible"
				);
			
				$this->arrayFields["opt_is_cool"]=array
				(
					"type"=>"tinyint(4)",
					"canBeNull"=>"YES",
					"key"=>"",
					"default"=>"1",
					"extra"=>"",
					"formLabel"=>"opt_is_cool"
				);
			
					
					
					//we define the xml datas with this function
					$this->defineXml();
					
				}
				
				public function getPrimaryKey()
				{
					foreach($this->xml->children() as $key=>$value)
					{
						if($value["isPrimaryKey"]=="true")
						{
							return $key;
						}
					}
				}
				
				protected function majWithXmlDatas()
				{
					parent::majWithXmlDatas();
					$this->reqLimit= (int) $this->xml["reqLimit"];
					$this->currentPaginationNumber=1;
					//$this->refreshNumElements();
				}
				
				public function refreshNumElements()
				{
					
					$sqlCount="SELECT COUNT(*) AS numElements FROM options ".$this->getWhereClause();
					
					$prepa=$this->dbh->query($sqlCount);
					$prepa->setFetchMode(PDO::FETCH_ASSOC);
					$result=$prepa->fetchAll();
					$this->totalNumElements=$result[0]["numElements"];
					
					if($this->totalNumElements<=$this->reqLimit)
					{
						$this->totalNumPages=1;
					}
					else 
					{
						$this->totalNumPages=ceil($this->totalNumElements/$this->reqLimit);
					}
				}
				
				public function getDatas($page)
				{
					$sqlReq="SELECT * FROM options ".$this->getWhereClause()." ".$this->getLimitClause($page)."";
					$prepa=$this->dbh->query($sqlReq);
					$prepa->setFetchMode(PDO::FETCH_ASSOC);
					$result=$prepa->fetchAll();
					return $result;
				}
				
				public function getAllDatas($columns="*",$supReq="",$qh=false)
				{
					$sqlReq="SELECT ".$columns." FROM options ".$supReq;
					$prepa=$this->dbh->prepare($sqlReq);
					if($qh)
					{$prepa->execute($qh);}
					else 
					{$prepa=$this->dbh->query($sqlReq);}
					$prepa->setFetchMode(PDO::FETCH_ASSOC);
					$result=$prepa->fetchAll();
					return $result;
				}
							
				
				
				
				public function setFilterArray($array)
				{
					$this->filterArray=null;
					$this->filterArray=$array;
				}
				
				private function getWhereClause()
				{
					if(count($this->filterArray)>0)
					{
						$clause="WHERE";
						$increWC=0;
						foreach($this->filterArray as $key=>$value)
						{
							if(in_array($key,array_keys($this->arrayFields)) && $value!="" && $value!=null)
							{
								if($increWC==0)
								{$clause.=" ".$key." = '".$value."' ";}
								else 
								{$clause.=" AND ".$key." = '".$value."' ";}
								$increWC++;
							}
						}
						if($increWC>0)
						{return $clause;}
						else{return "";}
					}
					else 
					{
						return "";
					}
					
				}
				
				private function getLimitClause($page=1)
				{
					if($this->totalNumElements<=$this->reqLimit)
					{
						return "";
					}
					else 
					{
						$downLimit;
						$upLimit;
						if($page<=$this->totalNumPages)
						{
							$downLimit=($page-1)*$this->reqLimit;
							$upLimit=$this->reqLimit;
						}
						else {
							return "";
						}
						return "LIMIT ".$downLimit.",".$upLimit."";
					}
				}
				
				
			}
			