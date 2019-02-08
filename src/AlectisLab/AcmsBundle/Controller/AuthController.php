<?php

namespace AlectisLab\AcmsBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AlectisLab\AcmsBundle\Controller\MyBaseController;
use AlectisLab\AcmsBundle\Acms\AcmsGlobalHelper;
use AlectisLab\AcmsBundle\Acms\TypesHelper;
use AlectisLab\AcmsBundle\Acms\ItemProxy;
use AlectisLab\AcmsBundle\Acms\AcmsConfig;
use AlectisLab\UtilsBundle\Helpers\Formcheck;
use Symfony\Component\HttpFoundation\Session\Session;

class AuthController extends MyBaseController
{


    /**
     * @Route("/auth", name="service_auth")
     */
    public function ServiceAuthAction(Request $request)
    {

        $checker=new Formcheck();
        $checker->addCheck("inputEmail",$_POST['login'],"mail");
        $checker->addCheck("inputPassword",$_POST['password'],"simple");

        if(!$checker->isValid())
        {
            echo json_encode(array('result'=>"ko",'message'=>"Vérifiez d'avoir bien rempli tous les champs","errors"=>$checker->getResults()));exit;
        }


        if(!$this->container->hasParameter('id_dataproj'))
        {
            echo json_encode(array('result'=>"ko",'message'=>"Aucun admin disponible"));exit;
        }

        $queryAdmin=$this->ormManager->doQuery('admins',"*","WHERE admins.dataproj_iddataproj=:idproj AND admins.admin_mail=:mail AND admins.admin_password=:pass",array(":mail"=>$_POST['login'],":pass"=>md5($_POST['password']),":idproj"=>$this->container->getParameter('id_dataproj')));
        if(count($queryAdmin)==0)
        {
            echo json_encode(array('result'=>"ko",'message'=>"Combinaison Login/mot de passe incorrect"));exit;
        }

        $session = new Session();

        $session->set('admin',$queryAdmin[0]);


        echo json_encode(array('result'=>"ok",'message'=>"Tout est OK"));exit;
    }

    /**
     * @Route("/acms-disconnect", name="acms_disconnect")
     */
    public function acmsDisconnectAction(Request $request)
    {

        $this->disconnect("admin");
        return $this->redirectToRoute('backend');

    }


}
