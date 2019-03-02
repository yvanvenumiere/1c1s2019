<?php

namespace AppBundle\Controller;

use AlectisLab\UtilsBundle\Helpers\UtilClass;
use Monolog\Handler\Curl\Util;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AlectisLab\UtilsBundle\Controller\AlectisBaseController;
use AlectisLab\UtilsBundle\Helpers\Formcheck;
use AlectisLab\AcmsBundle\Acms\ItemProxy;
use AlectisLab\AcmsBundle\Acms\AcmsGlobalHelper;
use AppBundle\Helpers\CustomQueries;
use AppBundle\Helpers\ToolBox;



class AjaxFrontController extends AlectisBaseController
{


    /**
     * @Route("/create-website", name="create_website")
     */
    public function cwsAction(Request $request)
    {
        echo "<pre>";var_dump(ToolBox::getDbInfos("dbi1"));exit;
        $checker=new Formcheck();
        $checker->addCheck("formule",$_POST['formule'],"simple");
        $checker->addCheck("emailTry",$_POST['emailTry'],"mail");
        $checker->addCheck("idTemplate",$_POST['idTemplate'],"nombre");
        $checker->addCheck("conditionsTry",$_POST['conditionsTry'],"simple");

        if($_POST['conditionsTry']!="true")
        {
            $checker->arrayResult["conditionsTry"]=array("isValid"=>false);
        }
        if(!$checker->isValid())
        {
            echo json_encode(array('result'=>"ko",'message'=>"Vérifiez d'avoir bien rempli tous les champs","errors"=>$checker->getResults()));exit;
        }

        $this->ormManager->requireModel('users');
        $this->ormManager->requireModel('profiles');
        $this->ormManager->requireModel('w_templates');


        $profil=new \profiles(UtilClass::rewritingOrNot());
        $profil->initFromDatas(array("profil_code"=>"CLIENT"));

        $user=new \users(UtilClass::rewritingOrNot());
        if($user->initFromDatas(array('user_mail'=>$_POST['emailTry'])))
        {
            echo json_encode(array('result'=>"ko",'message'=>"Cet adresse email est déjà utilisée pour un compte, si c'est le votre connectez vous et créer un nouveau site depuis votre espace membre"));exit;

        }

        $template=new \w_templates(UtilClass::rewritingOrNot());
        if(!$template->initFromDatas(array("idw_templates"=>$_POST['idTemplate'])))
        {
            echo json_encode(array('result'=>"ko",'message'=>"Création de votre site web impossible pour le moment"));exit;

        }

        $this->createLead($_POST['emailTry']);

        $expMail=explode("@",$_POST['emailTry']);


        $generatedMdp=$expMail[0]."-".strlen($expMail[1])."@".substr(time(),rand(1,4),rand(1,4));
        $user->set('user_mail',$_POST['emailTry']);
        $user->set('user_mdp',md5($generatedMdp));
        $user->set('user_date_creation',time());
        $user->set('profiles_idprofil',$profil->get('idprofil'));
        if(!$user->save())
        {
            echo json_encode(array('result'=>"ko",'message'=>"Création de votre site web impossible pour le moment"));exit;
        }



        $this->ormManager->requireModel('website');
        $website=new \website(UtilClass::rewritingOrNot());
        $website->set('website_date_creation',time());
        $website->set('users_idusers',$user->get('idusers'));
        $website->set('w_templates_idw_templates',$template->get('idw_templates'));



        //$user->

        echo json_encode(array('result'=>"ok",'message'=>"Votre site a bien été créé"));exit;



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

        $this->createLead($_POST['email']);






        echo json_encode(array('result'=>"ok",'message'=>"Message reçu, nous vous répondrons dans les plus brefs délais"));exit;

    }

    private function createLead($email)
    {
        $this->ormManager->requireModel('leads');
        $lead=new \leads(UtilClass::rewritingOrNot());
        if(!$lead->initFromDatas(array("lead_mail"=>$email)))
        {
            $lead->set('lead_mail',$email);
            $lead->set('lead_date_save',time());
            $lead->set('lead_utm_source',$_SERVER['HTTP_REFERER']);
            $lead->save();
        }
    }









}
