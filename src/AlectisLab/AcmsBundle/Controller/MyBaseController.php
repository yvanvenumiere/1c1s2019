<?php



namespace AlectisLab\AcmsBundle\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use AlectisLab\UtilsBundle\Controller\AlectisBaseController;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;




class MyBaseController extends AlectisBaseController
{

    public function __construct()
    {
        $this->setControl("backend","admin");
        $this->templateUnauthentified='AlectisLabAcmsBundle:Backend:auth.html.twig';

    }


}
