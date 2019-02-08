<?php

namespace AlectisLab\AcmsBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use AppBundle\Controller\MyBaseController;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('AlectisLabAcmsBundle:Default:index.html.twig');
    }
}
