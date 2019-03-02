<?php

namespace AppBundle\Helpers;
use AlectisLab\AcmsBundle\Acms\AcmsGlobalHelper;



class ToolBox
{

    public static function getDbInfos($userName)
    {

        $mdp=substr(md5($userName),2,6);
        $dbName=substr(md5($userName),6,5);
        return array
        (
            "userName"=>$userName,
            "mdp"=>$mdp,
            "dbName"=>$dbName
        );
    }


}
