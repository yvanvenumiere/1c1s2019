<?php

namespace AlectisLab\AcmsBundle\Acms;
use AlectisLab\AcmsBundle\Acms\AcmsGlobalHelper;

use AlectisLab\UtilsBundle\Helpers\UtilClass;



//cette classe permet de récupérer un objet de données en fonction d'un type d'item
class ItemProxy
{
    public $itId;
    public $ormManager;
    public $wasSaved=false;
    public $entity;
    private $fieldValues=array();
    private $modelLines=false;



    public function __construct($ormManager,$itId)
    {
        //on initialise les deux variables primordiales
        $this->ormManager=$ormManager;$this->itId=$itId;

        //on require ce qu'il faut
        $this->ormManager->requireModel('item');
        $this->ormManager->requireModel('item_values');
        $this->entity=new \item(UtilClass::rewritingOrNot());

        // on récupère les modèles de données
        $this->modelLines=AcmsGlobalHelper::getModelLines($this->ormManager,$this->itId);

        //on remplie d-le tableau des champs
        foreach($this->modelLines as $ml )
        {
            $this->fieldValues[$ml['ml_code']]=NULL;
        }


    }

    public function getModelLines()
    {
        return $this->modelLines;
    }

    public function getFieldsValues()
    {
        return $this->fieldValues;
    }

    public function getDatas()
    {
        $retour=array();
        if($this->entity)
        {
            $retour['entity']=$this->entity->getDatas();
        }
        $retour['fieldValues']=$this->fieldValues;
        return $retour;
    }

    //set la valeur d'un champs
    public function setFieldValue($mlCode,$value)
    {
        $this->fieldValues[$mlCode]=trim($value);
    }

    //récupère la valeur d'un champs
    public function getFieldValue($mlCode)
    {
        if(isset($this->fieldValues[$mlCode]))
        {
            return $this->fieldValues[$mlCode];
        }
        else
        {
            return null;
        }
    }


    //initialise en fonction d'un idItem
    public function initFromValues($arraySearch,$fkHandling=false)
    {
        $clauseWhereKey="";
        $clauseWhereValue="";
        if(count($arraySearch)>0)
        {
            $clauseWhereKey.=" AND ( ";
            $clauseWhereValue.=" AND ( ";
            $iterArray=0;
            foreach($arraySearch as $key=>$value)
            {

                $clauseWhereValue.=($iterArray==0?"":" OR ")." item_values.iv_content='".$value."' ";
                $clauseWhereKey.=($iterArray==0?"":" OR ")." model_line.ml_code='".$key."' ";
                $iterArray++;
            }
            $clauseWhereKey.=" ) ";
            $clauseWhereValue.=" ) ";
            
        }
        $searchIvs=$this->ormManager->doQuery
        (
            'item_values',
            "*",
            "LEFT JOIN model_line ON model_line.idmodel_line=item_values.model_line_idmodel_line
            LEFT JOIN item_types ON item_types.iditem_types=model_line.item_types_iditem_types
            WHERE item_types.iditem_types=:idItemType ".$clauseWhereKey.$clauseWhereValue,
            array(':idItemType'=>$this->itId)

        );
        $resultExtro=array();
        foreach($searchIvs as $result)
        {
            if(!in_array($result['item_iditem'],array_keys($resultExtro)))
            {
                $resultExtro[$result['item_iditem']]=array();
            }
            $resultExtro[$result['item_iditem']][$result['ml_code']]=$result['iv_content'];
        }
        //echo "<pre>";var_dump($arraySearch);exit;
        //$id
        foreach($resultExtro as $key=>$combinaison)
        {
            foreach($arraySearch as $keySearch=>$valueSearch)
            {
                if(isset($combinaison[$keySearch]))
                {
                    //if($combinaison[$keySearch]==$valueSearch)
                }
            }
            //if($combinaison[''])
        }
    }
    //initialise en fonction d'un idItem
    public function initFromId($idItem,$fkHandling=false)
    {
        if(!$this->entity->initFromDatas(array('iditem'=>$idItem)))
        {
           return false;
        }
        else
        {

            //on sait désormais que c'esu un objet qui a déjà été enregistré
            $this->wasSaved=true;

            //on récupères ces différentes valeurs de champs
            $searchIvs=$this->ormManager->doQuery
            (
                'item_values',
                "*",
                "LEFT JOIN model_line ON model_line.idmodel_line=item_values.model_line_idmodel_line

                 WHERE item_values.item_iditem=:idItem",
                array(':idItem'=>$idItem)

            );
            //echo "<pre>";var_dump($searchIvs);exit;

            //on les mets dans le bon tableau
            foreach($searchIvs as $iv)
            {


                $this->fieldValues[$iv['ml_code']]=trim($iv['iv_content']);
                //affichage des liens vers les images au besoin
                if($iv['ml_type_code']=="picture" || $iv['ml_type_code']=="pictures" || $iv['ml_type_code']=="file" || $iv['ml_type_code']=="files")
                {

                    //SET GLOBAL group_concat_max_len = 1000000
                    $imgs=$this->ormManager->doQuery
                    (
                        'item_value_media',
                        "*,GROUP_CONCAT(media_path) as reqPicsAcms",
                        "LEFT JOIN medias ON item_value_media.medias_idmedias=medias.idmedias
                        WHERE item_value_media.item_values_iditem_values=:idItemV GROUP BY item_values_iditem_values",
                        array(':idItemV'=>$iv['iditem_values'])

                    );

                    //var_dump($imgs);exit;

                    if(count($imgs)>0)
                    {
                        if(strlen($imgs[0]['reqPicsAcms'])>0)
                        {
                            $this->fieldValues[$iv['ml_code']]=$imgs[0]['reqPicsAcms'];
                        }
                    }
                }

                if($fkHandling && $iv['ml_type_code']=="fk")
                {
                    //$this->fieldValues[$iv['ml_code']."_fk"]=array();
                    $fkItemProxy=new ItemProxy($this->ormManager,$iv['item_types_iditem_types1']);
                    if($fkItemProxy->initFromId(trim($iv['iv_content'])))
                    {
                        $this->fieldValues[$iv['ml_code']."_fk"]=$fkItemProxy->getDatas();
                    }

                }

                if($iv['ml_type_code']=="fkmul")
                {
                    //$this->fieldValues[$iv['ml_code']."_fk"]=array();
                    $arrayItems=array();
                    $arrayIds=explode('|',$iv['iv_content']);
                    if(count($arrayIds)>0)
                    {
                        if($fkHandling)
                        {
                            $this->fieldValues[$iv['ml_code'] . "_fkmul"] = array();
                        }
                        $this->fieldValues[$iv['ml_code'] . "_fkmulIds"] = array();


                        foreach($arrayIds as $idItem)
                        {
                            if(strlen($idItem)>0)
                            {
                                if($fkHandling)
                                {
                                    $fkItemProxy=new ItemProxy($this->ormManager,$iv['item_types_iditem_types1']);
                                    if($fkItemProxy->initFromId(trim($idItem)))
                                    {
                                        $this->fieldValues[$iv['ml_code']."_fkmul"][]=$fkItemProxy->getDatas();
                                    }
                                }
                                $this->fieldValues[$iv['ml_code'] . "_fkmulIds"][]=trim($idItem);

                            }

                        }
                    }



                }

            }
            //var_dump($this->fieldValues);
            return true;
        }
    }

    //permet de valider les différents champs à enregistrer
    public function validate()
    {

        $errors=array();

        //pour cela on parcours les models
        foreach($this->modelLines as $ml)
        {



            //vérification de type
            $verifType=TypesHelper::validate($ml,$this->fieldValues[$ml['ml_code']]);

            if($verifType['result']==false)
            {
                $errors[$ml['ml_code']]=$ml['ml_name']." : ".$verifType['message'];
            }

            //vérification d'unicité
            if($ml['ml_is_unique'] && strlen($this->fieldValues[$ml['ml_code']])>0)
            {

                $clauseExcludeIfSaved=($this->wasSaved?" AND item_values.item_iditem<>:idItem ":"");
                $arrayParams=array(':ml'=>$ml['idmodel_line'],":iditemtype"=>$this->itId,":value"=>$this->fieldValues[$ml['ml_code']]);
                if($this->wasSaved)
                {
                    $arrayParams[':idItem']=$this->entity->get('iditem');
                }
                $searchExistant=$this->ormManager->doQuery
                (
                    'item_values',
                    "*",
                    "LEFT JOIN model_line ON model_line.idmodel_line=item_values.model_line_idmodel_line WHERE item_values.model_line_idmodel_line=:ml 
                    AND model_line.item_types_iditem_types=:iditemtype 
                    AND item_values.iv_content=:value ".$clauseExcludeIfSaved,
                    $arrayParams

                );

                if(count($searchExistant)>0)
                {
                    $errors[$ml['ml_code']]=$ml['ml_name']." '".$this->fieldValues[$ml['ml_code']]."'  déjà présent en base";
                }
            }


        }


        return array('result'=>(count($errors)>0?false:true),"errors"=>$errors);
    }

    public function getIvToSave($idModelLine)
    {
        $ivs=new \item_values(UtilClass::rewritingOrNot());
        $ivs->initFromDatas(array("item_iditem"=>$this->entity->get('iditem'),"model_line_idmodel_line"=>$idModelLine));
        return $ivs;
    }


    //suppression
    public function delete()
    {
        $bckDatas=$this->getDatas();
        if(!$this->wasSaved)
        {
            return false;
        }

        $dependances=$this->ormManager->doQuery('item_values',"*","LEFT JOIN model_line ON model_line.idmodel_line=item_values.model_line_idmodel_line  WHERE model_line.item_types_iditem_types1=:itemTypeId AND item_values.iv_content=:idItem",array(':itemTypeId'=>$this->itId,":idItem"=>$this->entity->get('iditem')));

        if(count($dependances)>0)
        {
            return false;
        }



        $itemValueMedias=$this->ormManager->doQuery('item_value_media',"*","LEFT JOIN item_values ON item_values.iditem_values=item_value_media.item_values_iditem_values WHERE item_values.item_iditem=:itemId",array(':itemId'=>$this->entity->get('iditem')));
        $this->ormManager->requireModel('item_value_media');
        foreach($itemValueMedias as $ivm)
        {
            $ivmToDelete=new \item_value_media(UtilClass::rewritingOrNot());
            $ivmToDelete->initFromDatas(array('iditem_value_media'=>$ivm['iditem_value_media']));
            $ivmToDelete->delete();
        }

        $itemValues=$this->ormManager->doQuery('item_values',"*","WHERE item_values.item_iditem=:itemId",array(':itemId'=>$this->entity->get('iditem')));
        $this->ormManager->requireModel('item_values');
        foreach($itemValues as $iv)
        {
            $ivToDelete=new \item_values(UtilClass::rewritingOrNot());
            $ivToDelete->initFromDatas(array('iditem_values'=>$iv['iditem_values']));
            $ivToDelete->delete();
        }

        if(!$this->entity->delete())
        {
            return false;
        }
        return $bckDatas;
    }
    //sauvegarde
    public function save()
    {
        $validation=$this->validate();
        if($validation['result']==false)
        {
            return $validation;
        }
        else
        {
            $time=time();
            //si c'est un nouvel objet , on enregistre les infos importantes de  base
            if(!$this->wasSaved)
            {
                $this->entity->set('item_date_creation',$time);
                $this->entity->set('item_types_iditem_types',$this->itId);
            }

            //sinon on continue
            $this->entity->set('item_date_update',$time);
            $this->entity->save();

            if(!$this->entity->save())
            {
                return array('result'=>false,"message"=>"Problème lors de la sauvegarde en base de données");
            }

            //maintznant on va enregistrer les champs, pour cela il faut créer des nouveaux objets et les mettre dans un tableau
            $ivsToSave=array();
            $errorWithIv=false;//avec un bool d'erreur en cas de problème
            foreach($this->modelLines as $key=>$ml)
            {

                //si on a kekchose à enregistrer
                //if($this->fieldValues[$ml['ml_code']]!=NULL)
                //{
                    $ivsToSave[$key]=new \item_values(UtilClass::rewritingOrNot());//on crée notre objet de champs

                    //si c'est déjà enregistré , on récupère la valeur existante dans l'objet
                    if($this->wasSaved)
                    {
                        $ivsToSave[$key]->initFromDatas(array("item_iditem"=>$this->entity->get('iditem'),"model_line_idmodel_line"=>$ml['idmodel_line']));
                    }

                    //et on enregistre les données
                    $ivsToSave[$key]->set('item_iditem',$this->entity->get('iditem'));
                    $ivsToSave[$key]->set('model_line_idmodel_line',$ml['idmodel_line']);
                    $ivsToSave[$key]->set('iv_content',$this->fieldValues[$ml['ml_code']]);

                    //si il y a eu au moins un problème, on renseigne le boolean adéquat
                    if(!$ivsToSave[$key]->save())
                    {
                        $errorWithIv=true;
                    }


                //}
            }

            //si il y a eu au moins un problème, on annule tout
            if($errorWithIv)
            {
                //si c'est un nouvel objet non persisté pour la première fois, on efface tout
                if(!$this->wasSaved)
                {
                    foreach($ivsToSave as $obj)
                    {
                        $obj->delete();
                    }
                    $this->entity->delete();

                }
                else
                {

                }
                //on informe que c'est pas ok
                return array('result'=>false,"message"=>"Problème lors de la sauvegarde en base de données");


            }
            //$this->handleCbActions();
            //CbActions::executeCB($this);
            //si on arrive ici , zafè nou bel
            return array('result'=>true,"message"=>"Elément sauvegardé avec succès");
        }

    }



    public function getCbs()
    {

        return AcmsGlobalHelper::getCbs($this->ormManager,$this->itId);
    }
}