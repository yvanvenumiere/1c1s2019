<?php

namespace AlectisLab\UtilsBundle\Helpers;
use AlectisLab\UtilsBundle\UtilClass;



class Paginator
{
    private $baseName;
    private $columns;
    private $query;
    private $arrayPrepa;
    private $ormManager;
    public $numTotalResults;
    public $numPages;
    private $numResults;

    public function setParams($_baseName,$_colums,$_query,$_arrayPrepa)
    {
        $this->baseName=$_baseName;
        $this->columns=$_colums;
        $this->query=$_query;

        $this->arrayPrepa=$_arrayPrepa;
    }

    public function setOrmManager($_ormManager)
    {
        $this->ormManager=$_ormManager;
    }

    public function setNumResults($_numResults)
    {
        $this->numResults=$_numResults;
    }

    public function doIt($numTot=false)
    {
        if($numTot==false)
        {$this->numTotalResults=count($this->ormManager->doQuery($this->baseName,$this->columns,$this->query,$this->arrayPrepa));}
        else{$this->numTotalResults=$numTot;}


        if($this->numResults<$this->numTotalResults)
        {

            $this->numPages=ceil($this->numTotalResults/$this->numResults);


        }
        else
        {
            $this->numPages=1;
        }


    }

    public function getDatas($page=1)
    {
        return $this->ormManager->doQuery($this->baseName,$this->columns,$this->query." ".$this->getLimitClause($page),$this->arrayPrepa);
    }



    private function getLimitClause($page=1)
    {


        if($this->numTotalResults<=$this->numResults)
        {
            return "";
        }
        else
        {
            $downLimit;
            $upLimit;
            if($page<=$this->numPages)
            {
                $downLimit=($page-1)*$this->numResults;
                $upLimit=$this->numResults;
            }
            else {
                return "";
            }
            return "LIMIT ".$downLimit.",".$upLimit."";
        }
    }
}