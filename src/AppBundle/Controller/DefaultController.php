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

        $infosCat=CustomQueries::getDataTemplates($this->ormManager);
        //echo "<pre>";var_dump($infosCat);exit;
        return $this->render('AppBundle:Default:accueil.html.twig',array('infosCats'=>$infosCat));


    }

    /**
     * @Route("/produit/{ref}/{id}", name="produit")
     */
    public function produitAction($ref,$id)
    {
        $produit=CustomQueries::getProduit($this->ormManager,$id);

        return $this->render('AppBundle:Default:produit.html.twig',array('infos'=>$produit));
    }









}
