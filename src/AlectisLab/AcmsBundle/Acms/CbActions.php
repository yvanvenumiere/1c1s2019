<?php

namespace AlectisLab\AcmsBundle\Acms;
use AlectisLab\AcmsBundle\Acms\AcmsGlobalHelper;
use AlectisLab\UtilsBundle\Helpers\ImgSaver;

//helper pour les types de champs
class CbActions
{






    //fonction qui retourne les messages
    public static function executeCB($action,$itemProxy,$ormManager=false)
    {
        if($action=="save")
        {
            $cbs=$itemProxy->getCbs();
        }
        else
        {
            if($ormManager )
            {
                //var_dump($itemProxy);exit;
                $cbs=AcmsGlobalHelper::getCbs($ormManager,$itemProxy['entity']['item_types_iditem_types']);

            }
            else
            {
                return false;
            }

        }

        foreach($cbs as $cb)
        {
            if($action==$cb['cb_mode'])
            {
                self::executeSpecificFunction($itemProxy,$cb['cba_name']);
            }

        }

    }

    public static function executeSpecificFunction($itemProxy,$name)
    {
        switch($name)
        {
            case "testCb":
                //var_dump($itemProxy->getDatas());exit;
                //sleep(1);
                //echo "huhu";exit;
                //$itemProxy->setFieldValue('email',"vedfgdfgfdgnunu@gmail.com");
                //$itemProxy->save();
                //echo $itemProxy->getFieldValue('email');
                //var_dump($itemProxy->save());
            break;

            case "testCb2":

                break;

            case "saveJi":
                if($itemProxy->getFieldValue('redim-ji')==1)
                {
                    ini_set('max_execution_time', 300);
                    ini_set('memory_limit','1024M');
                    $datas=$itemProxy->getDatas();
                    $images=explode(',',$datas['fieldValues']['images-ji']);

                    foreach($images as $key=>$img)
                    {
                        $redimMaker=new imgSaver("jpg","sdqsdsqsqdeer7776/indgstrdcvv76/".$img);
                        if($redimMaker->init())
                        {

                            $redimMaker->handleImage();
                            $redimMaker->resizeHomoW($datas['fieldValues']['largeur-resize-ji'],"pic");
                            $nom="img_0_0_".($key);
                            $redimMaker->saveImg("medias/exports/".$nom, "pic",true);
                        }
                    }

                    $itemProxy->setFieldValue('redim-ji',0);
                    $itemProxy->save();
                };

                break;

            default:

            break;
        };
    }




}
