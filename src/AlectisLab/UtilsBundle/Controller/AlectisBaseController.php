<?php

namespace AlectisLab\UtilsBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use AlectisLab\UtilsBundle\Helpers\ImgSaver;
use Symfony\Component\HttpFoundation\Session\Session;
use AlectisLab\UtilsBundle\Helpers\UtilClass;
use Symfony\Component\HttpFoundation\Request;



class AlectisBaseController extends Controller
{

    public $ormManager;
    protected $roleConnected=array();
    protected $roles=array();
    protected $templateUnauthentified=false;


    //fonction exécutée avant chaque appel de méthode de type controller
    public function preExecute()
    {

        $projectDir=$this->get('kernel')->getProjectDir();
        //on défini les roles
        //$this->setControl("backend","admin");

        //on défini l'orm
        if(is_dir($projectDir."/web/orm/".$this->getParameter('database_name')))
        {
            require_once $projectDir."/web/orm/".$this->getParameter('database_name')."/libs/php/globalInfos.php";
            $this->ormManager=new \globalInfos(UtilClass::rewritingOrNot());
        }


        // on récupère la session
        $session = new Session();



        //on regarde si il y a une session et un défini le roleConnected
        foreach($this->roles as $key=>$role)
        {
            //echo $key;
            if($session->has($key))
            {
                $this->roleConnected[$key]=$session->get($key);
                $session->set("admin_name",$this->roleConnected[$key]['admin_prenom']." ".$this->roleConnected[$key]['admin_nom']);
                //var_dump($session->get($key));
            }

        }



        //on parcour les différents roles
        foreach($this->roles as $role=>$motifs)
        {

            //on parcourt les motifs associés aux roles
            foreach($motifs as $motif)
            {
                if (preg_match("#".$motif."#", $_SERVER['REQUEST_URI']))//si on a un motif
                {
                    if(!in_array($role,array_keys($this->roleConnected)))//et que le role connected ne contient pas le  role associé au motif=>cassos
                    {
                        if($this->templateUnauthentified)
                        {

                            $template=$this->renderView($this->templateUnauthentified);
                            echo $template;
                        }
                        exit;

                    }
                }
            }
        }
        //maintenance
        /*if($_SERVER['REMOTE_ADDR']!="83.203.233.247")
        {
            $template=$this->renderView('AlectisLabAcmsBundle:Backend:index.html.twig');
            echo $template;exit;
        }*/
    }




    public function disconnect($role)
    {
        $session = new Session();
        $session->remove($role);
    }


    protected function setControl($motif,$role)
    {
        if(!isset($this->roles[$role]))
        {
            $this->roles[$role]=array($motif);
        }
        else
        {
            $this->roles[$role][]=$motif;
        }
    }




    public function customDump($var)
    {
        if($this->container->get('kernel')->getEnvironment()=="dev")
        {
            dump($var);
        }
    }



    /**
     * @Route("/al_getimage/{name}/{resizetype}/{format}", name="al_get_image",defaults={"name"="_","resizetype"="_","format"="_"})
     */
    public function getImageAction($name,$resizetype,$format)
    {


        $imgPath=$this->container->getParameter('cropped_dir').$resizetype."_".$format."_".$name;
        if(file_exists($imgPath))
        {

            header('Location: /'.$imgPath.'');
            exit;


        }

        $imgExtension;
        $splitExtension=explode(".",$name);
        $imgExtension=$splitExtension[count($splitExtension)-1];


        $link=$this->container->getParameter('img_dir')."/";
        $domainImage="";
        if(!file_exists($link.$name))
        {
            /*$dTest="http://smartagri:8888/";
            var_dump(file_exists($dTest.$link.$name));exit;
            if(!file_exists($dTest.$link.$name))
            {
                $domainImage="http://smart-agri.fr/";
            }
            else
            {
                $domainImage="http://smartagri:8888/";
            }*/


            /*if(strlen($this->container->getParameter('port_or_not'))>0)
            {
                $domainImage="http://smartagri:8888/";
            }
            else
            {
                $domainImage="http://smart-agri.fr/";
            }*/


        }



        //echo $domainImage.$link.$name;exit;

        $imageManager=new ImgSaver($imgExtension, $domainImage.$link.$name);
        if(!$imageManager->init() || !$imageManager->handleImage())
        {

            //$this->blanckImage(100, 100);
            exit;
        }

        switch ($resizetype)
        {
            case "resizeHomo":
                $imageManager->resizeHomo($format,"myPic");
                break;

            case "resizeFix":
                $arrFormat=explode("X",$format);
                $imageManager->resizeFix($arrFormat[0],$arrFormat[1],"myPic");
                break;

            case "resizeHomoH":

                $imageManager->resizeHomoH($format,"myPic");
                break;

            case "resizeHomoW":

                $imageManager->resizeHomoW($format,"myPic");
                break;



            case "crop":

                $arrFormat=explode("X",$format);
                $imageManager->resizeCrop($arrFormat[0],$arrFormat[1],"myPic");
                break;

            default:
                break;
        }
        $imageManager->saveImg($this->container->getParameter('cropped_dir')."/".$resizetype."_".$format."_".$name, "myPic",false);
        $imgPath=$this->container->getParameter('cropped_dir').$resizetype."_".$format."_".$name;
        if(file_exists($imgPath))
        {

            header('Location: /'.$imgPath.'');
            exit;
        }
        $imageManager->saveImg($this->container->getParameter('cropped_dir').$resizetype."_".$format."_".$name, "myPic",false);
        exit;
    }


    /**
     * @Route("/acms-seo-manager", name="acms_seo_manager")
     */
    public function agsAction(Request $request)
    {

        echo "seo manager";exit;
    }
}
