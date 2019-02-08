<?php
/*
* This file is a class that reprensents the base class for every model object 
* (c) 2012 Yvan Vénumière <yvan.venumierer@gmail.com>
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

/**
 * baseModel is an object that contains the base properties and medthods for a database model class
 *
 */
class baseModel
{

    protected $arrayFields=array();
    public $arrayRelations=array();
    protected $dbh;
    protected $tableName;
    protected $xml;
    protected $gInfos;
    public $rewriting=false;

    /**
     * constructor for the class
     */
    public function __construct($rewriting=false,$forCrud=false)
    {
        $this->rewriting=$rewriting;
        //instanciation of the globalInfos class
        $this->gInfos=new globalInfos($rewriting);
        if($forCrud)
            //loading of the global xml
        {$this->gInfos->setGlobalXmlConfig(simplexml_load_file($this->gInfos->getRootPathFromHere()."config/global_config.xml"));}

        //instanciation of the pdo connection
        //$this->dbh=new PDO($this->gInfos->getSimpleInfo("database_infos"),$this->gInfos->getSimpleInfo("database_user"),$this->gInfos->getSimpleInfo("database_password"),array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
        $this->dbh=$this->gInfos->globalDbh;
    }

    /**
     * function that loads the xml datas related to this table
     *
     */
    protected function defineXml()
    {
        //loading of the datas relatives to this table
        $this->xml=simplexml_load_file($this->gInfos->getRootPathFromHere()."config/".$this->tableName.".xml");
        $this->majWithXmlDatas();
    }


    /**
     * methods that returns the differents fields of of the model
     *
     * @return array returns ans array with the differents fields for the model
     */
    public function getFields($arrayOrder=null)
    {
        if($arrayOrder!=null)
        {
            $datas=array();
            foreach($arrayOrder as $name)
            {
                if(array_key_exists($name, $this->arrayFields))
                {
                    $datas[$name]=$this->arrayFields[$name];
                }
            }
            return $datas;
        }
        return $this->arrayFields;
    }


    /**
     * function the map the xml datas with some variables for the instance
     *
     */
    protected function majWithXmlDatas()
    {

    }

}
