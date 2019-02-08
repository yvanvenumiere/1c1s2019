<?php 
/*
* This file is part of dbboh package.
* (c) 2012 Yvan Vénumière
<yvan.venumierer@gmail.com>
	*
	* For the full copyright and license information, please view the LICENSE
	* file that was distributed with this source code.
	*/

	/**
	* globalInfos is a class that can be usefull when working with table classes
	*
	*/

	class globalInfos
	{
		
		private $arrayElements=array();
		private $tableNames=array();
		private $globalXmlConfig;
		private $globalSimpleInfos=array();
		public $globalDbh;
		private $autoLoadFiles;
		private $globalXmlHierarchy;
		private $rewriting;
		
		
		/**
		* constructor for the class
		*/
		public function __construct($rewriting=false)
		{
                        $this->rewriting=$rewriting;
			$this->setSimpleInfo("admin_path","orm/webplatform");
			$this->setSimpleInfo("database_name","webplatform");
			$this->setSimpleInfo("database_infos","mysql:host=localhost:8889;dbname=webplatform");
			$this->setSimpleInfo("database_user","webplatform");
			$this->setSimpleInfo("database_password","webplatform");
			
			//$this->globalDbh=new PDO($this->getSimpleInfo("database_infos"), $this->getSimpleInfo("database_user"),$this->getSimpleInfo("database_password"), array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
			
			$this->autoloadFiles=array
			(
				"libs/php/baseModel.php",
				"libs/php/formCheck.php",
				"libs/php/imgSaver.php",
				//"libs/php/inclusion_bididi.php",
				"libs/php/myTools.php",
				"libs/php/dbhHandler.php"
			);
			
			foreach($this->autoloadFiles as $file)
			{
				require_once($this->getRootPathFromHere().$file);
			}
			$this->globalDbh=dbhHandler::getInstance($this->getSimpleInfo("database_infos"), $this->getSimpleInfo("database_user"),$this->getSimpleInfo("database_password"));
		}
                
               
		
		/**
		* methods that allows to do a simple query
		* @param string $name name of the model
	    */
		public function doQuery($from,$columns="*",$supReq="",$qh=false)
		{
			$sqlReq="SELECT ".$columns." FROM ".$from." ".$supReq;
			$prepa=$this->globalDbh->prepare($sqlReq);
			if($qh)
			{$prepa->execute($qh);}
			else 
			{$prepa=$this->globalDbh->query($sqlReq);}
			$prepa->setFetchMode(PDO::FETCH_ASSOC);
			$result=$prepa->fetchAll();
			return $result;
		}
					
		/**
		* methods that include a model by his name
		* @param string $name name of the model
	    */
		public function requireModel($name)
		{
			require_once($this->getRootPathFromHere()."model/".$name.".php");
		}
		
		/**
		* methods that returns a model instance by the name
		* @param string $name name of the model
	    */
		public function getModelInstance($name)
		{
			$reflect = new ReflectionClass($name);
			$object = $reflect->newInstance();
			return $object;
		}
		
		/**
		* methods that returns a model data manager instance by the name
		* @param string $name name of the model
	    */
		public function getModelDmInstance($name)
		{
			$reflect = new ReflectionClass($name."DatasManager");
			$object = $reflect->newInstance();
			return $object;
		}
		
		/**
		* methods that save an xml for a table element
	    * @param xml $xml a xml object that contains the infos
		* @param string $tableName name of the table
		* @return boolean returns a boolean to inform that the affectation worked
	    */
		public function setXml($xml,$tableName)
		{
			if(in_array($tableName,$this->tableNames))
			{
				$this->arrayElements[$tableName]=$xml;
				return true;
			}
			else 
			{
				return false;
			}
		}
		
		/**
		* methods that get an xml for a table element
		* @param string $tableName name of the table
		* @return xml  returns the wanted xml or false if not found
	    */
		public function getXml($tableName)
		{
			if(in_array($tableName,$this->tableNames) && isset($this->arrayElements[$tableName]))
			{
				return $this->arrayElements[$tableName];
			}
			else 
			{
				return false;
			}
		}
		
		/**
		* methods that save the differents table names
	    * @param string $tableName a string that contains all the name of the table
		* @return boolean returns a boolean to inform that the affectation worked
	    */
		public function addTableName($tableName)
		{
			$this->tableNames[]=$tableName;
			return true;
		}
		
		/**
		* methods that save the global xml config element
	    * @param xml $xml a xml object that contains the infos
		* @return boolean returns a boolean to inform that the affectation worked
	    */
		public function setGlobalXmlConfig($xml)
		{
			$this->globalXmlConfig=$xml;
			return true;
		}
		
		/**
		* methods that get the global xml config element
		* @return xml returns the global xml config xml object or false if not found
	    */
		public function getGlobalXmlConfig()
		{
			if($this->globalXmlConfig)
			{
				return $this->globalXmlConfig;
			}
			else 
			{
				return false;
			}
		}
		
		/**
		* methods that save the global xml hierarchy element
	    * @param xml $xml a xml object that contains the infos
		* @return boolean returns a boolean to inform that the affectation worked
	    */
		public function setGlobalXmlHierarchy($xml)
		{
			$this->globalXmlHierarchy=$xml;
			return true;
		}
		
		/**
		* methods that get the global xml config element
		* @return xml returns the global xml config xml object or false if not found
	    */
		public function getGlobalXmlHierarchy()
		{
			if($this->globalXmlHierarchy)
			{
				return $this->globalXmlHierarchy;
			}
			else 
			{
				return false;
			}
		}
		
		/**
		* methods that save a simple infos into the globalSimpleInfos array
	    * @params string $infoLabel a string containing the label for the infos
		* @param string $infoValue value of the info
		* @return boolean returns a boolean to inform that the affectation worked
	    */
		public function setSimpleInfo($infoLabel,$infoValue)
		{
			
			$this->globalSimpleInfos[$infoLabel]=$infoValue;
			return true;
		}
		
		/**
		* methods that get an xml for a table element
		* @param string $infoLabel label for the info
		* @return string returns the value of the info or false if not found
	    */
		public function getSimpleInfo($infoLabel)
		{
			if(isset($this->globalSimpleInfos[$infoLabel]))
			{
				return $this->globalSimpleInfos[$infoLabel];
			}
			else 
			{
				return false;
			}
		}
                

		
		/**
		* methods that returns the root path from the file that called this method
		* @return string the root path from the file that called this method
	    */
		public function getRootPathFromHere()
		{
                    if(isset($_SERVER["HTTP_HOST"]))
                    {
                        $uri=str_replace("/app_dev.php","", $_SERVER["REQUEST_URI"]);
                        $uriTab=explode(".php?",$uri);
                        $uri=$uriTab[0];

                        if (preg_match("#".$uri."#", $_SERVER["SCRIPT_FILENAME"]))
                        {

                            $arr=explode("/",$uri);
                            $relPath="";
                            foreach($arr as $key=>$rep)
                            {
                                    if($key<count($arr)-2)
                                    {
                                            $relPath.="../";
                                    }
                            }
                            return $relPath.$this->getSimpleInfo("admin_path")."/";
                        }
                        else
                        {
                            return $this->getSimpleInfo("admin_path")."/";
                        }
                    }
                    else
                    {
               
                        if($this->rewriting)
                        {
                            return $this->rewriting.$this->getSimpleInfo("admin_path")."/";
                        }
                        else
                        {
                            return $this->getSimpleInfo("admin_path")."/";
                        }
                        
                    }


			
		}
		
		/**
		* methods that returns the hierarchy infos from a table name
		* @param string $table table name
		* @return array the infos on the hierarchy in this case
	    */
		public function getHierarchyInfos($table,$chosedId)
		{
			//var_dump($this->getGlobalXmlHierarchy());
			$xml=$this->getGlobalXmlHierarchy();
			$result=array();
			$result["ariane"]="";
			$result["childsTable"]=array();
			$result["parentsTable"]=array();
			if($table && $xml)
			{
				if($xml->{"".$table.""}->childTables->count()>0)
				{
					for($i=0;$i<$xml->{"".$table.""}->childTables->childTable->count();$i++)
					{
						if((string)$xml->{"".$table.""}->childTables->childTable[$i]["enabled"]=="true")
						{
							$result["childsTable"][]=
							array
							(
								"icon"=>(string)$xml->{"".$table.""}->childTables->childTable[$i]["icon"],
								"prompt"=>(string)$xml->{"".$table.""}->childTables->childTable[$i]["prompt"],
								"tableName"=>(string)$xml->{"".$table.""}->childTables->childTable[$i]["className"],
								"usedCol"=>(string)$xml->{"".$table.""}->childTables->childTable[$i]["usedCol"],
								"fk"=>(string)$xml->{"".$table.""}->childTables->childTable[$i]["fk"]
							);
						}
					}
				}
				
				if($xml->{"".$table.""}->parentTables->count()>0)
				{
					for($u=0;$u<$xml->{"".$table.""}->parentTables->parentTable->count();$u++)
					{
						if((string)$xml->{"".$table.""}->parentTables->parentTable[$u]["enabled"]=="true")
						{
							$result["parentsTable"][]=
							array
							(
								"icon"=>(string)$xml->{"".$table.""}->parentTables->parentTable[$u]["icon"],
								"prompt"=>(string)$xml->{"".$table.""}->parentTables->parentTable[$u]["prompt"],
								"tableName"=>(string)$xml->{"".$table.""}->parentTables->parentTable[$u]["className"],
								"usedCol"=>(string)$xml->{"".$table.""}->parentTables->parentTable[$u]["usedCol"],
								"fk"=>(string)$xml->{"".$table.""}->parentTables->parentTable[$u]["fk"]
							);
						}
					}
				}
				
				if(count($result["parentsTable"])>0 && count($chosedId)>0)
				{
					//echo "<pre>";var_dump($chosedId);exit;
					$result["ariane"]="";
					$infosAriane=$this->getAllParentInfos($table,$xml,$chosedId);
					for($i=count($infosAriane)-1;$i>-1;$i--)
					{
						//echo $infosAriane[$i]["label"];
						if($infosAriane[$i]["link"]!=null)
						{
							$result["ariane"].="<a href=\"router.php?table=".$infosAriane[$i]["table"]."\">".$infosAriane[$i]["label"]."</a> > ";
						}
						else 
						{
							$result["ariane"].=$infosAriane[$i]["label"];
						}
					}
					
					/*$this->requireModel($result["parentsTable"][0]["tableName"]);
					$elemParent=$this->getModelInstance($result["parentsTable"][0]["tableName"]);
					
					if($elemParent->initFromDatas(array($result["parentsTable"][0]["usedCol"]=>$chosedId["filter"][$result["parentsTable"][0]["fk"]])))
					{
						$result["ariane"]="<a href=\"router.php?page=".$result["parentsTable"][0]["tableName"]."\">".$xml->{"".$result["parentsTable"][0]["tableName"].""}["refName"]."</a> > ".$xml->{"".$table.""}["refName"]."";
					}*/
				}
				
			}
			return $result;
		}


		public function getAllParentInfos($currentTable,$xml,$infos)
		{
			$infosParents=null;
			$this->requireModel($currentTable);
			$oneInstance=$this->getModelInstance($currentTable);
			$fields=$oneInstance->getFields();
			foreach($infos["filter"] as $key=>$value)
			{
				if(array_key_exists($key,$fields))
				{
					$infosParents=array($key=>$value);
				}
			}
			$datas[]=array("infosParents"=>$infosParents,"label"=>(string)$xml->{"".$currentTable.""}["refName"],"link"=>null,"table"=>$currentTable);
			
			$tableToSearch=$currentTable;
			
			while($tableToSearch!=null && count($xml->{"".$tableToSearch.""}->parentTables->children())>0)
			{
				$label;$link;$next;
				foreach($xml->{"".$tableToSearch.""}->parentTables->children() as $parentTable)
				{
					if((string)$parentTable["enabled"]=="true")
					{
						
						$infosParents=null;
						$this->requireModel((string)$parentTable["className"]);
						$oneInstance=$this->getModelInstance((string)$parentTable["className"]);
						$fields=$oneInstance->getFields();
						foreach($infos["filter"] as $key=>$value)
						{
							if($key==(string)$parentTable["fk"] && array_key_exists($key,$fields))
							{
							
								$infosParents=array($key=>$value);
								$theClass=(string)$parentTable["className"];
								
								
							}
							
						}
						$label=(string)$xml->{"".(string)$parentTable["className"].""}["refName"];
						$link="router.php?table=".(string)$parentTable["className"];
						$next=(string)$parentTable["className"];
						$datas[]=array("infosParents"=>$infosParents,"label"=>$label,"link"=>$link,"table"=>$next);
						$tableToSearch=$next;
						
					}
					else {
						$tableToSearch=null;
					}
					
				}
				
				
				/*foreach($xml->{"".$tableToSearch.""}->parentTables->children() as $parentTable)
				{
					//echo $parentTable["className"];
					$infosParents=null;
					$this->requireModel((string)$parentTable["className"]);
					$oneInstance=$this->getModelInstance((string)$parentTable["className"]);
					$fields=$oneInstance->getFields();
					foreach($infos["filter"] as $key=>$value)
					{
						if(array_key_exists($key,$fields))
						{
						
							$infosParents=array($key=>$value);
							$theClass=(string)$parentTable["className"];
						}
						
					}
				}
				$label=(string)$xml->{"".(string)$xml->{"".$tableToSearch.""}->parentTables->parentTable[0]["className"].""}["refName"];
				$link="router.php?table=".(string)$xml->{"".$tableToSearch.""}->parentTables->parentTable[0]["className"];
				$datas[]=array("infosParents"=>$infosParents,"label"=>$label,"link"=>$link,"table"=>(string)$xml->{"".$tableToSearch.""}->parentTables->parentTable[0]["className"]);
				$tableToSearch=$xml->{"".$tableToSearch.""}->parentTables->parentTable[0]["className"];*/
			}
			//echo "<pre>";var_dump($datas);exit;
			for($i=0;$i<count($datas);$i++)
			{
				if(isset($datas[$i+1]))
				{
					$oneInstance=$this->getModelInstance($datas[$i+1]["table"]);
					$valueToCheck=null;
					if($datas[$i]["infosParents"])
					{
						foreach($datas[$i]["infosParents"] as $key=>$value)
						{
							$valueToCheck=$value;
						}
						if($oneInstance->initFromDatas(array($oneInstance->getPrimaryKeyField()=>$valueToCheck)))
						{
							if(count(explode("|",(string)$xml->{"".$datas[$i+1]["table"].""}["displayColumn"]))>1)
							{
								$concatDisplay="";
								foreach(explode("|",(string)$xml->{"".$datas[$i+1]["table"].""}["displayColumn"]) as $columnConcat)
								{
									$concatDisplay.=$oneInstance->get($columnConcat)." ";
								}
								$datas[$i+1]["label"].=" (".$concatDisplay.")";
							}
							elseif(strlen((string)$xml->{"".$datas[$i+1]["table"].""}["displayColumn"])>0)
							{
								$datas[$i+1]["label"].=" (".$oneInstance->get((string)$xml->{"".$datas[$i+1]["table"].""}["displayColumn"]).")";
							}
							
						}
					}
					
				}
				
			}
			//echo "<pre>";var_dump($datas);exit;
			return $datas;
		}
		
		public function getGlobalInfoForNodeAndTable($nodeName,$tableName=null)
		{
			if(!$this->getGlobalXmlConfig())
			{
				return false;
			}
			if($tableName)
			{
				//var_dump($this->getGlobalXmlConfig()->class_child_types);exit;
				if($this->getGlobalXmlConfig()->{"".$tableName.""}->{"".$nodeName.""})
				{
					return $this->getGlobalXmlConfig()->{"".$tableName.""}->{"".$nodeName.""};
				}
				else 
				{
					if($this->getGlobalXmlConfig()->generic->{"".$nodeName.""})
					{
						return $this->getGlobalXmlConfig()->generic->{"".$nodeName.""};
					}
					else
					{
						return false;
					}
				}
			}
			else 
			{
				if($this->getGlobalXmlConfig()->generic->{"".$nodeName.""})
				{
					return $this->getGlobalXmlConfig()->generic->{"".$nodeName.""};
				}
				else 
				{
					return false;
				}
			}
			
		}
	}
	
	