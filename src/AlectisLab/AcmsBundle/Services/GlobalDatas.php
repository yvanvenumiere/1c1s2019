<?php

namespace AlectisLab\AcmsBundle\Services;

use AlectisLab\UtilsBundle\Helpers\UtilClass;
use AlectisLab\AcmsBundle\Acms\AcmsGlobalHelper;
use Symfony\Component\HttpFoundation\Session\Session;




class GlobalDatas
{
    public $backendMenu;
    private $container;
    private $ormManager;

    public function __construct($container)
    {
        $this->container=$container;
        $projectDir=$this->container->get('kernel')->getProjectDir();
        require_once $projectDir."/web/orm/".$this->container->getParameter('database_name')."/libs/php/globalInfos.php";
        $this->ormManager=new \globalInfos(UtilClass::rewritingOrNot());


        $this->backendMenu=AcmsGlobalHelper::getItemTypesForBackendMenu($this->ormManager,$this->container->getParameter('id_dataproj'));
        $this->infoProj=AcmsGlobalHelper::getGlobalInfosForProject($this->ormManager,$this->container->getParameter('id_dataproj'));
        $this->gmapKey=$this->container->getParameter('gmap_api');
        $session = new Session();
        if($session->has('admin'))
        {
            $this->infoUserConnected=$session->get('admin');

        }
        //$this->infoUserConnected=$this->roleConnected;
        //echo "<pre>";var_dump($this->infoUserConnected);exit;
    }
}
