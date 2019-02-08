<?php

namespace AppBundle\Services;

use AlectisLab\UtilsBundle\Helpers\UtilClass;
use Symfony\Component\HttpFoundation\Session\Session;
use AlectisLab\AcmsBundle\Acms\AcmsGlobalHelper;




class DatasDispatcher
{
    private $container;
    private $ormManager;

    public function __construct($container)
    {
        $this->container=$container;
        $projectDir=$this->container->get('kernel')->getProjectDir();
        require_once $projectDir."/web/orm/".$this->container->getParameter('database_name')."/libs/php/globalInfos.php";
        $this->ormManager=new \globalInfos(UtilClass::rewritingOrNot());
        //set global datas accessible in twig here
      





    }
}
