<?php

namespace AppBundle\Controller;

use AlectisLab\UtilsBundle\Helpers\UtilClass;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AlectisLab\UtilsBundle\Controller\AlectisBaseController;
use AlectisLab\UtilsBundle\Helpers\Formcheck;
use AlectisLab\AcmsBundle\Acms\ItemProxy;
use AlectisLab\AcmsBundle\Acms\AcmsGlobalHelper;
use AppBundle\Helpers\CustomQueries;


class AjaxFrontController extends AlectisBaseController
{


    /**
     * @Route("/create-website", name="create_website")
     */
    public function cwsAction(Request $request)
    {
        $checker=new Formcheck();
        $checker->addCheck("formule",$_POST['formule'],"simple");
        $checker->addCheck("emailTry",$_POST['emailTry'],"mail");

        $checker->addCheck("conditionsTry",$_POST['conditionsTry'],"simple");

        if($_POST['conditionsTry']!="true")
        {
            $checker->arrayResult["conditionsTry"]=array("isValid"=>false);
        }
        if(!$checker->isValid())
        {
            echo json_encode(array('result'=>"ko",'message'=>"Vérifiez d'avoir bien rempli tous les champs","errors"=>$checker->getResults()));exit;
        }


    }

    /**
     * @Route("/question", name="question")
     */
    public function questionAction(Request $request)
    {
        $checker=new Formcheck();
        $checker->addCheck("email",$_POST['email'],"mail");
        $checker->addCheck("message",$_POST['message'],"simple");



        if(!$checker->isValid())
        {
            echo json_encode(array('result'=>"ko",'message'=>"Vérifiez d'avoir bien rempli tous les champs","errors"=>$checker->getResults()));exit;
        }

        $this->ormManager->requireModel('leads');
        $lead=new \leads(UtilClass::rewritingOrNot());
        if(!$lead->initFromDatas(array("lead_mail"=>$_POST['email'])))
        {
            $lead->set('lead_mail',$_POST['email']);
            $lead->set('lead_date_save',time());
            $lead->set('lead_utm_source',$_SERVER['HTTP_REFERER']);
            $lead->save();
        }






        echo json_encode(array('result'=>"ok",'message'=>"Message reçu, nous vous répondrons dans les plus brefs délais"));exit;

    }









}
