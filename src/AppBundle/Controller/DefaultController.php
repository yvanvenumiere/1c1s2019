<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AlectisLab\UtilsBundle\Controller\AlectisBaseController;
use AlectisLab\UtilsBundle\Helpers\Formcheck;
use AlectisLab\AcmsBundle\Acms\ItemProxy;
use AlectisLab\AcmsBundle\Acms\AcmsGlobalHelper;
use AppBundle\Helpers\CustomQueries;


class DefaultController extends AlectisBaseController
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {


        return $this->render('AppBundle:Default:accueil.html.twig',array());


    }









}
