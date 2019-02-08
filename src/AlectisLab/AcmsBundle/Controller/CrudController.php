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
use AlectisLab\UtilsBundle\Twig\CommonExtension;
use AlectisLab\UtilsBundle\Helpers\UtilClass;
use AlectisLab\AcmsBundle\Acms\FixedData;
use AlectisLab\AcmsBundle\Acms\CbActions;
use Symfony\Component\HttpFoundation\Session\Session;

class CrudController extends MyBaseController
{
    /**
     * @Route("/testcrud", name="testtrud")
     */
    public function indexAction(Request $request)
    {

        $search=AcmsGlobalHelper::search($this->ormManager,"permaculture");
        echo "<pre>";var_dump($search);exit;
        $items=AcmsGlobalHelper::getItems($this->ormManager,1,1,false,array("col"=>"nom-dauteur","compar"=>"=","value"=>94));
        echo "<pre>";var_dump($items);

        /*$data=new FixedData($this->ormManager,"test-picture-f",1);
        $data->init();
        //var_dump($data->getDatas());
        $this->container->get('profiler')->disable();
        return $this->render('AlectisLabAcmsBundle:Backend:test.html.twig',array("datas"=>$data->getDatas()));*/
        //$data->set('ezrzer@sfds.com');
        //var_dump($data->save());
        exit;
        //dump($this->ormManager);
        // replace this example code with whatever you need
        //$fields=AcmsGlobalHelper::getFields($this->ormManager,1);
        //echo AcmsConfig::getPathMedia();exit;
        //echo "<pre>";var_dump(AcmsGlobalHelper::getItems($this->ormManager,1,2));exit;
        $dvdProxy=new ItemProxy($this->ormManager,1);

        //var_dump($dvdProxy->initFromId(9));

        echo $dvdProxy->getFieldValue('prix');

        $dvdProxy->setFieldValue("nom","bidul");
        $dvdProxy->setFieldValue("prix",71);
        $dvdProxy->setFieldValue("email","sdftestff@dddtita2.cm");

        //echo "<pre>";/*var_dump($dvdProxy->validate());*/
        var_dump($dvdProxy->save());


        //foreach($fields as )

        //dump($fields);

        //dump(TypesHelper::validate($fields[0],false));
        exit;
    }

    //Point d'entrée du backend qui redirige vers une page d'administration si tout est OK

    /**
     * @Route("/backend", name="backend")
     */
    public function backendAction(Request $request)
    {
        if(!$this->container->hasParameter('id_dataproj'))//si pas d'idi projet configuré=>kassos
        {
            echo "problème";exit;//TODO redirection appropriée
        }
        $menuElems=AcmsGlobalHelper::getItemTypesForBackendMenu($this->ormManager,$this->container->getParameter('id_dataproj'));
        if(count($menuElems)>0)//on prend le premier type d'objets administrables et on va dessus
        {
            $extTwig=new CommonExtension();
            return $this->redirectToRoute('backend_itemtype', array('slugname' => $extTwig->slugFilter($menuElems[0]['it_name']),'itemTypeId'=>$menuElems[0]['iditem_types']));
        }
        else
        {
            return $this->redirectToRoute('backend_fixed_datas');
        }


    }


    //page d'administration d'un itemType
    /**
     * @Route("/backend/itemtype/{slugname}/{itemTypeId}", name="backend_itemtype")
     */
    public function backendItemTypeAction($slugname,$itemTypeId)
    {

        $session=new Session();
        $session->getFlashBag()->clear();
        //on récupère les infos de l'itemType
        $itemTypeInfos=AcmsGlobalHelper::getItemTypeInfos($this->ormManager,$itemTypeId);
        if(!$itemTypeInfos || count($itemTypeInfos)==0)//si aucune==>kassos
        {
            echo "problème";exit;//TODO redirection appropriée
        }

        return $this->render('AlectisLabAcmsBundle:Backend:crud-item-type2.html.twig',array('itemTypeId'=>$itemTypeId,'itemTypeInfo'=>$itemTypeInfos[0]));
    }



    //page d'affichage de la liste d'items d'un itemtype
    /**
     * @Route("/backend/listitems/{itemTypeId}/{page}/{sort}/{order}/{col}/{value}", name="backend_item_list")
     */
    public function backendItemListAction($itemTypeId,$page,$sort=false,$order=false,$col=false,$value=false)
    {
        $sorting=(($sort!=false && $order!=false)?array($sort,strtoupper($order)):false);
        $filtering=(($col!=false && $value!=false)?array("col"=>$col,"compar"=>"=","value"=>$value):false);
        //on récupère les items
        $items=AcmsGlobalHelper::getItems($this->ormManager,$itemTypeId,$page,$sorting,$filtering);


        if(count($items['result'])==0 && $page>1)//utile quand on supprime un item d'une page ou il n'en reste qu'un
        {
            $page=$page-1;
            $items=AcmsGlobalHelper::getItems($this->ormManager,$itemTypeId,$page,$sorting);
        }

        $itemTypeInfos=AcmsGlobalHelper::getItemTypeInfos($this->ormManager,$itemTypeId);
        $itemTypeChild=AcmsGlobalHelper::getItemTypesChild($this->ormManager,$itemTypeId);


        //gestion des breadcrumbs
        $session=new Session();
        //url customisée del la ressource
        $customUrl=$_SERVER['REQUEST_URI']."|".$itemTypeId;
        //urls customisées stockées actuellement
        $flashs=$session->getFlashBag()->peek('urlList');


        //on teste l'existence et on stocke
        if(!in_array($customUrl,$flashs) && $page<2 && ($sort==false || $sort=="item_date_creation"))
        {
            $this->addFlash(
                'urlList',
                $customUrl
            );
        }
        else//on fait bien attention à ne pas en rajouter qui y sont déjà
        {
            $session->getFlashBag()->clear();
            foreach($flashs as $fl)
            {
                $this->addFlash(
                    'urlList',
                    $fl
                );
                if($customUrl==$fl)
                {
                    break;
                }
            }
        }

        //on génère le tableau breadcrumb
        $breadcrumb=AcmsGlobalHelper::getBreadCrumbFromUrls($this->ormManager,$session->getFlashBag()->peek('urlList'));



        return $this->render('AlectisLabAcmsBundle:Backend:crudlist.html.twig',array('breadcrumb'=>$breadcrumb,'itemTypeChild'=>$itemTypeChild,'itemTypeInfo'=>$itemTypeInfos[0],'itemTypeId'=>$itemTypeId,'items'=>$items['result'],'numItemsTot'=>$items['numItems'],'numPages'=>$items['numPages'],'currentPage'=>$page,'modelLines'=>$items['modelLines'],"sort"=>$sort,"order"=>$order,"filteringRaw"=>$filtering,"filtering"=>($filtering!=false?json_encode($filtering):false)));
    }

    //données fixes
    /**
     * @Route("/backend/fixed-datas", name="backend_fixed_datas")
     */
    public function backendFixedDatasAction(Request $request)
    {

        $datas=AcmsGlobalHelper::getFixedDatas($this->ormManager,$this->container->getParameter('id_dataproj'));
        //echo "<pre>";var_dump($datas);exit;
        return $this->render('AlectisLabAcmsBundle:Backend:fixed-datas.html.twig',array('items'=>$datas,'iddp'=>$this->container->getParameter('id_dataproj')));
    }



    //page de visualisation d'un nouvel item
    /**
     * @Route("/backend/viewitem/{itemTypeId}/{itemId}", name="view_item")
     */
    public function viewItemAction($itemTypeId,$itemId)
    {
        //on crée l'itemProxy
        $itemProxy=new ItemProxy($this->ormManager,$itemTypeId);
        //on récupère les modelLines
        $modelLines=$itemProxy->getModelLines();
        //on récupère les infos de l'itemType
        $itemTypeInfos=AcmsGlobalHelper::getItemTypeInfos($this->ormManager,$itemTypeId);

        //on initialise l'itemProxy
        if(!$itemProxy->initFromId($itemId,true))
        {
            echo "problème";exit;//TODO redirection appropriée
        }

        //echo "<pre>";var_dump($itemProxy->getDatas());exit;


        return $this->render('AlectisLabAcmsBundle:Backend:view-item.html.twig',array('itemTypeInfo'=>$itemTypeInfos[0],'itemTypeId'=>$itemTypeId,'modelLines'=>$modelLines,'item'=>$itemProxy->getDatas()));
    }
    //page d'édition d'un nouvel item
    /**
     * @Route("/backend/edititem/{itemTypeId}/{itemId}", name="edit_item")
     */
    public function editItemAction($itemTypeId,$itemId)
    {
        //on crée l'itemProxy
        $itemProxy=new ItemProxy($this->ormManager,$itemTypeId);
        //on récupère les modelLines
        $modelLines=$itemProxy->getModelLines();

        //on récupère les infos de l'itemType
        $itemTypeInfos=AcmsGlobalHelper::getItemTypeInfos($this->ormManager,$itemTypeId);

        //on initialise l'itemProxy
        if(!$itemProxy->initFromId($itemId))
        {
            echo "problème";exit;//TODO redirection appropriée
        }
        //echo "<pre>";var_dump($itemProxy->getDatas());exit;


        return $this->render('AlectisLabAcmsBundle:Backend:edit-add-item.html.twig',array('filtering'=>false,'itemTypeInfo'=>$itemTypeInfos[0],'itemTypeId'=>$itemTypeId,'modelLines'=>$modelLines,'item'=>$itemProxy->getDatas()));

    }


    //page de création d'un nouvel item selon un itemTypeId
    /**
     * @Route("/backend/newitem/{itemTypeId}/{col}/{value}", name="new_item")
     */
    public function newItemAction($itemTypeId,$col=false,$value=false)
    {
        //on récupère les infos de l'itemType
        $itemTypeInfos=AcmsGlobalHelper::getItemTypeInfos($this->ormManager,$itemTypeId);
        //on récupère les modelLines pour le formulaire
        $modelLines=AcmsGlobalHelper::getModelLines($this->ormManager,$itemTypeId);
        //echo "<pre>";var_dump($modelLines);exit;
        //echo "<pre>";var_dump($modelLines);exit;
        $filtering=(($col!=false && $value!=false)?array("col"=>$col,"compar"=>"=","value"=>$value):false);

        return $this->render('AlectisLabAcmsBundle:Backend:edit-add-item.html.twig',array('filtering'=>$filtering,'item'=>0,'modelLines'=>$modelLines,'itemTypeId'=>$itemTypeId,'itemTypeInfo'=>$itemTypeInfos[0]));
    }

    //page de suppression d'un  item
    /**
     * @Route("/backend/deleteitem/{itemTypeId}/{itemId}", name="delete_item")
     */
    public function deleteItemAction($itemTypeId,$itemId)
    {
        $itemProxy=new ItemProxy($this->ormManager,$itemTypeId);
        //on initialise l'itemProxy
        if(!$itemProxy->initFromId($itemId))
        {
            echo json_encode(array("result"=>"ko","message"=>"Suppression impossible pour le moment"));exit;
        }

        $suppression=$itemProxy->delete();
        if(!$suppression)
        {
            echo json_encode(array("result"=>"ko","message"=>"Suppression impossible pour le moment"));exit;
        }
        CbActions::executeCB("delete",$suppression,$this->ormManager);
        echo json_encode(array("result"=>"ok","message"=>"Suppression effectuée avec succès"));exit;

    }


    //action de sauvegarde d'un item
    /**
     * @Route("/backend/save-fd/{fdCode}/{dataProjId}", name="save_fd")
     */
    public function saveFDAction($fdCode,$dataProjId)
    {
        $data=new FixedData($this->ormManager,$fdCode,$dataProjId);
        if(!$data->init())
        {echo json_encode(array('result'=>"ko",'message'=>"Problème à l'enregistrement"));exit;}
        if(gettype($_POST['value'])!="array")//si ce n'est pas un array , insertion simple
        {
            $data->set($_POST['value']);

        }
        else
        {
            $this->ormManager->requireModel('medias');
            $this->ormManager->requireModel('fixed_data_media');
            $tmpFiles=array();//on crée un array temporaire qui contiendra la ou les données de type media à enregistrer
            $deletedFiles=array();
            $fixesDatasMedias=array();
            foreach($_POST['value'] as $file)
            {
                if($file['status']=="upload successful")//on ne garde que ceux qui ont le statut OK
                {
                    $tmpFiles[]=$file;
                }
                if($file['status']=="deleted")//on efface ceux qui ont le statut deleted
                {
                    $deletedFiles[]=$file;
                }

            }

            if($data->get('fd_type_code')=="picture" || $data->get('fd_type_code')=="file")
            {
                $picture=array_pop($tmpFiles);//on récupère la bonne image ( dernière donc ) dans une variable
                $media=new \medias(UtilClass::rewritingOrNot());
                //et on récupère le media qui est sensé être déjà enregistré quelque part
                if($media->initFromDatas(array('media_qquuid'=>$picture['uuid'])))
                {
                    //si on le trouve, on commence l'enregistrement du fixed_data_media
                    $ivdToSave=new \fixed_data_media(UtilClass::rewritingOrNot());
                    $ivdToSave->initFromDatas(array('medias_idmedias'=>$media->get('idmedias')));//on verifie quand même qu'il n'y a pas déjà un fixed_data_media pour ce media , si oui , on le garde
                    $ivdToSave->set('medias_idmedias',$media->get('idmedias'));
                    $ivdToSave->set('fixed_datas_idfixed_datas',$data->get('idfixed_datas'));



                    $fixesDatasMedias[]=$ivdToSave;

                }
            }

            if($data->get('fd_type_code')=="pictures" || $data->get('fd_type_code')=="files")
            {
                $pictures=$tmpFiles;//on récupère les images
                foreach($pictures as $pic)
                {
                    $media=new \medias(UtilClass::rewritingOrNot());
                    //et on récupère le media qui est sensé être déjà enregistré quelque part
                    if($media->initFromDatas(array('media_qquuid'=>$pic['uuid'])))
                    {
                        //si on le trouve, on commence l'enregistrement du fixed_data_media
                        $ivdToSave=new \fixed_data_media(UtilClass::rewritingOrNot());
                        $ivdToSave->initFromDatas(array('medias_idmedias'=>$media->get('idmedias')));//on verifie quand même qu'il n'y a pas déjà un fixed_data_media pour ce media , si oui , on le garde
                        $ivdToSave->set('medias_idmedias',$media->get('idmedias'));
                        $ivdToSave->set('fixed_datas_idfixed_datas',$data->get('idfixed_datas'));



                        $fixesDatasMedias[]=$ivdToSave;

                    }
                }

            }

            //on crée l'instance de media



            $ivdRefs="";//on mettra dans cette variable les id du fixed_data_media...
            foreach($fixesDatasMedias as $key=>$fdmts)
            {
                if($fdmts->save())//si la sauvegarde est OK, on mets l'id en ref dans l'itemValue au cas ou
                {
                    $ivdRefs.=$fdmts->get('idfixed_data_media').($key<count($fixesDatasMedias)-1?",":"");
                }
            }

            if(strlen($ivdRefs)>0)
            {
                $ivdsToDelete=$this->ormManager->doQuery("fixed_data_media","*","WHERE fixed_data_media.idfixed_data_media NOT IN(".$ivdRefs.") AND fixed_data_media.fixed_datas_idfixed_datas=:idfd",array(':idfd'=>$data->get('idfixed_datas')));
                foreach($ivdsToDelete as $toDelete)//on fait le nécessaire
                {
                    $objToDelete=new \fixed_data_media(UtilClass::rewritingOrNot());

                    if($objToDelete->initFromDatas(array('idfixed_data_media'=>$toDelete['idfixed_data_media'])))
                    {
                        $objToDelete->delete();
                    }
                }
            }


            //on parcours le tableau des deletedFiles pour les effacer
            foreach($deletedFiles as $mediasToDelete)
            {
                //$iv=$itemProxy->getIvToSave($key);//on récupère le itemValue sauvegardé pour le champs concernée

                    $queryFixed=$this->ormManager->doQuery("fixed_data_media","*","LEFT JOIN medias ON medias.idmedias=fixed_data_media.medias_idmedias WHERE medias.media_qquuid=:uuid",array(':uuid'=>$mediasToDelete['uuid']));
                    if(count($queryFixed)>0)
                    {
                        foreach($queryFixed as $fdmToDelete)
                        {
                            $objFdmToDelete=new \fixed_data_media(UtilClass::rewritingOrNot());

                            if($objFdmToDelete->initFromDatas(array('idfixed_data_media'=>$fdmToDelete['idfixed_data_media'])))
                            {
                                $objFdmToDelete->delete();
                            }
                        }
                    }

            }
        }


        $validation=$data->validate();

        if($validation['result']==false)
        {
            echo json_encode(array('result'=>"ko",'message'=>$validation['message']));exit;
        }


        if(!$data->save())
        {
            echo json_encode(array('result'=>"ko",'message'=>"Sauvegarde impossible pour le moment"));exit;
        }


        echo json_encode(array('result'=>"ok",'message'=>"Element sauvegardé avec succès"));exit;
    }

    //action de sauvegarde d'un item
    /**
     * @Route("/backend/saveitem/{itemTypeId}/{itemId}", name="save_item")
     */
    public function saveItemAction($itemTypeId,$itemId=0)
    {

        //instatiation d'un itemProxy
        $itemProxy=new ItemProxy($this->ormManager,$itemTypeId);
        if($itemId!=0)
        {
            if(!$itemProxy->initFromId($itemId))//initialisation de l'itemProxy
            {
                echo json_encode(array('result'=>"ko",'message'=>"Problème à l'enregistrement"));exit;
            }
        }

        //on prépare un array avec les item_values_medias des item_values qui sont des medias
        $itemValueMedias=array();
        $deletedFiles=array();
        //echo "<pre>";var_dump($_POST);exit;

        //on assigne les nouvelles valeurs
        foreach($_POST as $key=>$value)
        {

            if(gettype($value)!="array")//si ce n'est pas un array , insertion simple
            {

                $itemProxy->setFieldValue($key,$value);
            }
            else//sinon
            {


                $mlTypeCode=false;//on prépare une variable qui identifie le type de données
                $mlId=false;//on prépare une variable qui identifie le model de données pour ce champs
                foreach($itemProxy->getModelLines() as $model)//on parcourt donc les modellines
                {
                    if($model['ml_code']==$key)//quand on trouve , on assigne les deux variables
                    {
                        $mlTypeCode=$model['ml_type_code'];
                        $mlId=$model['idmodel_line'];
                    }
                }
                $this->ormManager->requireModel('medias');
                $this->ormManager->requireModel('item_value_media');
                $tmpFiles=array();//on crée un array temporaire qui contiendra la ou les données de type media à enregistrer

                foreach($value as $file)
                {
                    if($file['status']=="upload successful")//on ne garde que ceux qui ont le statut OK
                    {
                        $tmpFiles[]=$file;
                    }
                    if($file['status']=="deleted")//on efface ceux qui ont le statut deleted
                    {
                        $deletedFiles[$mlId][]=$file;
                    }

                }

                if($mlTypeCode=="picture" || $mlTypeCode=="file")//si c'est un typeCode de type picture=>un seule image
                {

                    $picture=array_pop($tmpFiles);//on récupère la bonne image ( dernière donc ) dans une variable
                    //on crée l'instance de media

                    $media=new \medias(UtilClass::rewritingOrNot());
                    //et on récupère le media qui est sensé être déjà enregistré quelque part
                    if($media->initFromDatas(array('media_qquuid'=>$picture['uuid'])))
                    {
                        //si on le trouve, on commence l'enregistrement de l'item_value_media
                        $ivmToSave=new \item_value_media(UtilClass::rewritingOrNot());
                        $ivmToSave->initFromDatas(array('medias_idmedias'=>$media->get('idmedias')));//on verifie quand même qu'il n'y a pas déjà un itemValueMedia pour ce media , si oui , on le garde
                        $ivmToSave->set('medias_idmedias',$media->get('idmedias'));
                        //et on le place dans un tableau pour la suite
                        $itemValueMedias[$mlId]=array($ivmToSave);
                    }
                }


                if($mlTypeCode=="pictures" || $mlTypeCode=="files")//si c'est un typeCode de type picture=>un seule image
                {
                    $itemValueMedias[$mlId]=array();
                    //on parcours la liste des fichiers
                    foreach($tmpFiles as $file)
                    {
                        $media=new \medias(UtilClass::rewritingOrNot());
                        //et on récupère le media qui est sensé être déjà enregistré quelque part
                        if($media->initFromDatas(array('media_qquuid'=>$file['uuid'])))
                        {
                            $ivmToSave=new \item_value_media(UtilClass::rewritingOrNot());
                            $ivmToSave->initFromDatas(array('medias_idmedias'=>$media->get('idmedias')));//on verifie quand même qu'il n'y a pas déjà un itemValueMedia pour ce media , si oui , on le garde
                            $ivmToSave->set('medias_idmedias',$media->get('idmedias'));
                            //et on le place dans un tableau pour la suite
                            $itemValueMedias[$mlId][]=$ivmToSave;
                        }
                    }
                }
            }

        }



        //validation
        $validation=$itemProxy->validate();
        if($validation['result']==false)
        {
            echo json_encode(array('result'=>"ko",'message'=>"Vérifiez d'avoir bien rempli tous les champs","errors"=>$validation['errors']));exit;
        }

        //sauvegarde
        $sauvegarde=$itemProxy->save();

        if($sauvegarde['result']==false)
        {
            echo json_encode(array('result'=>"ko",'message'=>$sauvegarde['message']));exit;
        }

        //on parcourt le tableau des itemValuesMedias pour enregistrer les ids d'item values qui sont desormais OK
        //var_dump($itemValueMedias);exit;

        foreach($itemValueMedias as $key=>$ivmsToSave)//un key=un champs
        {
            $iv=$itemProxy->getIvToSave($key);//on récupère le itemValue sauvegardé pour le champs concernée

            $ivRefs="";//on mettra dans cette variable les id d'item_value_media...
            foreach($ivmsToSave as $key=>$ivmts)
            {
                $ivmts->set('item_values_iditem_values',$iv->get('iditem_values'));//on set
                if($ivmts->save())//si la sauvegarde est OK, on mets l'id en ref dans l'itemValue au cas ou
                {
                    $ivRefs.=$ivmts->get('iditem_value_media').($key<count($ivmsToSave)-1?",":"");
                }
            }

            //on cherche les itemValueMedia de cet itemValue qui ne sont pas dans ivRef ( donc à supprimer)
            if(strlen($ivRefs)>0)
            {
                $ivmsToDelete=$this->ormManager->doQuery("item_value_media","*","WHERE item_value_media.iditem_value_media NOT IN(".$ivRefs.") AND item_value_media.item_values_iditem_values=:itemValue",array(':itemValue'=>$iv->get('iditem_values')));
                foreach($ivmsToDelete as $toDelete)//on fait le nécessaire
                {
                    $objToDelete=new \item_value_media(UtilClass::rewritingOrNot());

                    if($objToDelete->initFromDatas(array('iditem_value_media'=>$toDelete['iditem_value_media'])))
                    {
                        $objToDelete->delete();
                    }
                }
            }
        }

        //on parcours le tableau des deletedFiles pour les effacer
        //var_dump($deletedFiles);exit;
        foreach($deletedFiles as $key=>$mediasToDelete)
        {
            $iv=$itemProxy->getIvToSave($key);//on récupère le itemValue sauvegardé pour le champs concernée
            foreach($mediasToDelete as $mtd)
            {
                $queryItemValueMedia=$this->ormManager->doQuery("item_value_media","*","LEFT JOIN medias ON medias.idmedias=item_value_media.medias_idmedias WHERE medias.media_qquuid=:uuid",array(':uuid'=>$mtd['uuid']));
                if(count($queryItemValueMedia)>0)
                {
                    foreach($queryItemValueMedia as $ivmToDelete)
                    {
                        $objIvmToDelete=new \item_value_media(UtilClass::rewritingOrNot());

                        if($objIvmToDelete->initFromDatas(array('iditem_value_media'=>$ivmToDelete['iditem_value_media'])))
                        {
                            $objIvmToDelete->delete();
                        }
                    }
                }
            }
            $iv->set('iv_content','');
            $iv->save();
        }

        CbActions::executeCB("save",$itemProxy);

        echo json_encode(array('result'=>"ok",'message'=>"Element sauvegardé avec succès"));exit;

    }


    /**
     * @Route("/backend/upload_file_for_preview", name="upload_file_for_preview")
     */
    public function uploadFileForPreviewAction(Request $request)
    {

    }

    /**
     * @Route("/backend/upload_img_for_preview/{type}", name="upload_img_for_preview")
     */
    public function uploadImgForPreviewAction($type="image")
    {
        //var_dump($_POST);
        if($_FILES["qqfile"]["error"]!=0)
        {
            echo json_encode(array("success"=>false,"error"=>"Assurez vous d'avoir bien sélectionné un fichier"));exit;
        }
        else
        {
            $tmpName=rand().rand().time().$_FILES["qqfile"]["name"];
            $tmpName=UtilClass::cryptImageName($tmpName);
            $arrName=explode(".",$tmpName);
            $extension=$arrName[1];
            if($type=="image")
            {
                $mimeTypesImages=array('image/jpeg','image/pjpeg','image/pjpeg','image/png');
                if(!in_array(mime_content_type($_FILES["qqfile"]["tmp_name"]),$mimeTypesImages))
                {
                    echo json_encode(array("success"=>false,"error"=>"Assurez vous d'avoir bien sélectionné un image ( jpg ou png )"));exit;
                }
            }

            $mediaName='media'.time();
            $mediaPath=$this->container->getParameter('upload_dir').$tmpName;

            if(move_uploaded_file($_FILES["qqfile"]["tmp_name"],$mediaPath))
            {
                $this->ormManager->requireModel('medias');
                $amedia=new \medias(UtilClass::rewritingOrNot());
                $amedia->set('media_name',$mediaName);
                $amedia->set('media_path',$tmpName);
                $amedia->set('media_qquuid',$_POST['qquuid']);
                $amedia->set('media_date_creation',time());
                $amedia->save();
            }

            echo json_encode(array("success"=>true,"fileId"=>$amedia->get('idmedias')));exit;
        }

    }

    /**
     * @Route("/backend/existing_img_for_preview2/{fdCode}/{projId}", name="existing_img_for_preview2")
     */
    public function existingImgForPreview2Action($fdCode=0,$projId=0)
    {
        //requete sur l'itemValue
        $data=new FixedData($this->ormManager,$fdCode,$projId);
        if(!$data->init())
        {exit;}

        $fdms=$this->ormManager->doQuery("fixed_data_media","*","LEFT JOIN medias ON medias.idmedias=fixed_data_media.medias_idmedias WHERE fixed_data_media.fixed_datas_idfixed_datas=:idfd ",array(':idfd'=>$data->get('idfixed_datas')));


        $arrayRetour=array();
        foreach($fdms as $med)
        {
            $arrayRetour[]=array
            (
                "name"=>"Fichier ".UtilClass::getExtension($med['media_path'])."",
                "uuid"=>$med['media_qquuid'],
                "thumbnailUrl"=>"/".$this->container->getParameter('upload_dir').$med['media_path']
            );
        }
        echo json_encode($arrayRetour);exit;


    }


    /**
     * @Route("/backend/existing_img_for_preview/{mlCode}/{itemId}", name="existing_img_for_preview")
     */
    public function existingImgForPreviewAction($mlCode=0,$itemId=0)
    {
        //requete sur l'itemValue
        $queryItemValue=$this->ormManager->doQuery("item_values",'item_values.*,model_line.idmodel_line',"LEFT JOIN model_line ON item_values.model_line_idmodel_line=model_line.idmodel_line WHERE model_line.ml_code=:mlcode AND item_values.item_iditem=:idItem",array(':mlcode'=>$mlCode,':idItem'=>$itemId));
        if(count($queryItemValue)==0){exit;}

        $itemValueMedias=$this->ormManager->doQuery("item_value_media","*","LEFT JOIN medias ON medias.idmedias=item_value_media.medias_idmedias WHERE item_value_media.item_values_iditem_values=:idItemValue",array(':idItemValue'=>$queryItemValue[0]['iditem_values']));

        $arrayRetour=array();
        foreach($itemValueMedias as $med)
        {
            $arrayRetour[]=array
            (
                "name"=>"Fichier ".UtilClass::getExtension($med['media_path'])."",
                "uuid"=>$med['media_qquuid'],
                "thumbnailUrl"=>"/".$this->container->getParameter('upload_dir').$med['media_path']
            );
        }
        echo json_encode($arrayRetour);exit;


    }

    /**
     * @Route("/backend/delete_img_for_preview/{qquuid}", name="delete_img_for_preview")
     */
    public function deleteImgForPreviewAction($qquuid=0)
    {
        if(strlen($qquuid)>0)
        {

            $this->ormManager->requireModel('medias');
            $amedia=new \medias(UtilClass::rewritingOrNot());
            if($amedia->initFromDatas(array("media_qquuid"=>$qquuid)))//on cherche le media via son uuid
            {

                //si on le trouve , on cherche un éventuel item_value_media
                $this->ormManager->requireModel('item_value_media');
                $ivm=new \item_value_media(UtilClass::rewritingOrNot());
                if(!$ivm->initFromDatas(array("medias_idmedias"=>$amedia->get('idmedias'))))//si aucun item_value_media associé => on supprime
                {
                    @unlink($this->container->getParameter('upload_dir').$amedia->get('media_path'));
                    $amedia->delete();
                }

                //si on le trouve , on cherche un éventuel fixed_data_media
                $this->ormManager->requireModel('fixed_data_media');
                $fdm=new \fixed_data_media(UtilClass::rewritingOrNot());
                if(!$fdm->initFromDatas(array("medias_idmedias"=>$amedia->get('idmedias'))))//si aucun item_value_media associé => on supprime
                {
                    @unlink($this->container->getParameter('upload_dir').$amedia->get('media_path'));
                    $amedia->delete();
                }


            }



        }

        exit;

    }



}
