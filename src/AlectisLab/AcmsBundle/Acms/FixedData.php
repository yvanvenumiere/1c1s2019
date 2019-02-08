<?php

namespace AlectisLab\AcmsBundle\Acms;
use AlectisLab\AcmsBundle\Acms\AcmsGlobalHelper;
use AlectisLab\UtilsBundle\Helpers\UtilClass;

class FixedData
{
    private $fdCode;
    private $ormManager;
    private $entity;
    private $projectId;
    public $initialised=false;


    public function __construct($ormManager,$_fdCode,$_projectId)
    {
        $this->ormManager=new $ormManager;
        $this->fdCode=$_fdCode;
        $this->projectId=$_projectId;
        $this->ormManager->requireModel('fixed_datas');
        $this->entity=new \fixed_datas(UtilClass::rewritingOrNot());

        //if($thi)
    }

    public function init()
    {
        if(!$this->entity->initFromDatas(array('fd_code'=>$this->fdCode,"dataproj_iddataproj"=>$this->projectId)))
        {
            $this->initialised=false;

        }
        else
        {
            $this->initialised=true;
        }
        return $this->initialised;
    }

    public function getDatas()
    {
        if($this->initialised)
        {
            $datas=$this->entity->getDatas();
            echo "<pre>";
            foreach($datas as $key=>$value)
            {
                if($key=="fd_content" && ($datas['fd_type_code']=="picture" || $datas['fd_type_code']=="pictures"))
                {
                    $imgs=$this->ormManager->doQuery
                    (
                        'fixed_data_media',
                        "*,GROUP_CONCAT(media_path) as reqPicsAcms",
                        "LEFT JOIN medias ON fixed_data_media.medias_idmedias=medias.idmedias
                        WHERE fixed_data_media.fixed_datas_idfixed_datas=:idfd GROUP BY fixed_datas_idfixed_datas",
                        array(':idfd'=>$this->entity->get('idfixed_datas'))

                    );

                    if(count($imgs)>0)
                    {
                        if(strlen($imgs[0]['reqPicsAcms'])>0)
                        {
                            $datas['fd_content']=$imgs[0]['reqPicsAcms'];
                        }
                    }
                }
            }
            return $datas;
        }
        else
        {
            return false;
        }
    }

    public function set($val)
    {
        if($this->initialised)
        {$this->entity->set('fd_content',$val);}

    }

    public function get($prop)
    {
        if($this->initialised)
        {return $this->entity->get($prop);}
        else{return false;}

    }

    public function validate()
    {
            //vérification de type
            $verifType=TypesHelper::validateFix($this->entity);
            return $verifType;
    }

    public function save()
    {

        $this->entity->set('fd_update_at',time());
        return $this->entity->save();

    }




}