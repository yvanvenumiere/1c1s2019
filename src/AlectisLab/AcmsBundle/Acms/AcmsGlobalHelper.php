<?php

namespace AlectisLab\AcmsBundle\Acms;
use AlectisLab\UtilsBundle\Helpers\Paginator;
use AlectisLab\AcmsBundle\Acms\TypesHelper;
//use AlectisLab\UtilsBundle\Helpers\Formcheck;


//cette classe regroupe des méthode de requetes souvent génériques à toute l'application
class AcmsGlobalHelper
{


    public static $SEPARATOR_MUL="|";
    //retourne les champs liés à un type d'item
    public static function getFields($ormManager,$itemtypeId)
    {
        $fields=$ormManager->doQuery('model_line',"*","WHERE model_line.item_types_iditem_types=:it ORDER BY ml_order ASC",array(":it"=>$itemtypeId));
        return $fields;
    }

    //retourne les infos d'un itemType
    public static function getItemTypeInfos($ormManager,$itemTypeId)
    {
        $infos=$ormManager->doQuery('item_types',"*","WHERE item_types.iditem_types=:id",array(':id'=>$itemTypeId));
        return $infos;
    }



    //retourne le breadcrumb
    public static function getBreadCrumbFromUrls($ormManager,$arrayUrl)
    {

        $retour=array();
        foreach($arrayUrl as $url)
        {
            $parts=explode('|',$url);

            $itemInfos=AcmsGlobalHelper::getItemTypeInfos($ormManager,$parts[1]);
            //var_dump($itemInfos);
            $retour[]=array('name'=>ucfirst($itemInfos[0]['it_name']),"url"=>$parts[0]);
        }
        return $retour;
    }

    //retourne les model_line d'un itemType
    public static function getModelLines($ormManager,$itemTypeId,$arrayMlCode=false)
    {
        //var_dump($arrayMlCode);exit;

        if($arrayMlCode)
        {
            $clauseFields=" AND ( ";
            $incre=0;
            foreach ($arrayMlCode as $item)
            {
                $clauseFields.=($incre==0?" ":" OR ")." model_line.ml_code='$item' ";
                $incre++;
            }
            $clauseFields.=" ) ";
        }
        else
        {
            $clauseFields="";
        }

        $mls=$ormManager->doQuery('model_line',"*","WHERE model_line.item_types_iditem_types=:itemType ".$clauseFields." ORDER BY ml_order ASC",array(":itemType"=>$itemTypeId),false);
        //echo "<pre>";var_dump($mls);exit;
        for($i=0;$i<count($mls);$i++)
        {
            if($mls[$i]['ml_type_code']=="fk" || $mls[$i]['ml_type_code']=="fkmul")
            {
                $possibleValues=self::getItems($ormManager,$mls[$i]['item_types_iditem_types1'],0);
                if(count($possibleValues['result'])>0)
                {
                    $mls[$i]['possibleValues']=$possibleValues['result'];
                }

            }

            if($mls[$i]['ml_type_code']=="choice_list")
            {
                $possibleValues=explode("|",$mls[$i]['ml_choice_list']);
                if(count($possibleValues)>0)
                {
                    $mls[$i]['listValues']=$possibleValues;
                }

            }
        }
        return $mls;
    }

    //retourne les valeurs de champs liées à un item
    public static function getItemValues($ormManager,$idItem)
    {
        $ivs=$ormManager->doQuery('item_values',"*","WHERE item_values.item_iditem=:idItem",array(":idItem"=>$idItem));
        return $ivs;
    }

    public static function search($ormManager,$search)
    {
        //$formCheck=new
        //$search=mysql_real_escape_string($search);
        $ivs=$ormManager->doQuery('item_values',"*","LEFT JOIN item ON item.iditem=item_values.item_iditem LEFT JOIN item_types ON item.item_types_iditem_types=item_types.iditem_types WHERE item_values.iv_content LIKE :search",array(':search'=>"%$search%"));
        for($i=0;$i<count($ivs);$i++)
        {
            $chaineResult="";
            $pos=strpos($ivs[$i]['iv_content'],$search);
            $totLen=strlen($ivs[$i]['iv_content']);
            if($pos)
            {
                if($pos>20)
                {
                    $chaineResult=substr($ivs[$i]['iv_content'],$pos-15,$pos+100)." ...";
                }
                else
                {
                    $chaineResult=substr($ivs[$i]['iv_content'],$pos,$pos+100)." ...";
                }
            }
            else
            {
                $chaineResult=substr($ivs[$i]['iv_content'],$pos,$pos+100)." ...";
            }
            $ivs[$i]['pos']=$pos;
            $ivs[$i]['resultStr']=$chaineResult;
        }
        return $ivs;
    }

    //retourne les callback
    public static function getCbs($ormManager,$iditt)
    {
        $cbs=$ormManager->doQuery('cb_actions',"*","WHERE cb_actions.item_types_iditem_types=:itt",array(':itt'=>$iditt));
        return $cbs;
    }



    public function getItemsByCode($ormManager,$itemtypeCode,$page=0/*si on veut tous les résultats*/,$sortObject=false,$itemFilterObject=false)
    {
        $itemType=$ormManager->doQuery("item_types","it_code,iditem_types","WHERE item_types.it_code=:itcode",array(":itcode"=>$itemtypeCode));
        if(count($itemType)==0)
        {
            return false;
        }

        return self::getItems($ormManager,$itemType[0]['iditem_types'],$page,$sortObject,$itemFilterObject);
    }

    public function getItemsFiltered($collection,$itemFilterObject)
    {
        $tmpArray=array();

        //echo "<pre>";var_dump($collection);exit;
        foreach($collection['result'] as $item)
        {

            $boolAdd=true;
            foreach($itemFilterObject as $cond)
            {

                if(isset($cond['isMul']) && $cond['isMul']==true)
                {
                    $values=explode(self::$SEPARATOR_MUL,$item['fields'][$cond['col']]);
                    if(!in_array($cond['value'],$values))
                    {
                        $boolAdd=false;
                    }
                }
                else
                {
                    if($item['fields'][$cond['col']]!=$cond['value'])
                    {
                        $boolAdd=false;
                    }
                }



            }


            if($boolAdd)
            {
                $tmpArray[]=$item;
            }


        }
        $collection['result']=$tmpArray;
        $collection['numItems']=count($tmpArray);


        return $collection;
    }


    //retourne les itemps liés à un type
    public static function getItems($ormManager,$itemtypeId,$page=0/*si on veut tous les résultats*/,$sortObject=false,$itemFilterObject=false)
    {

        //on compte d'abord la totalité des items
        $countItems=$ormManager->doQuery
        (
            'item',
            "count(iditem),item.item_types_iditem_types,item_types.iditem_types,item_types.it_pagination",
            "LEFT JOIN item_types ON item_types.iditem_types=item.item_types_iditem_types WHERE item.item_types_iditem_types=:itemType ",array(":itemType"=>$itemtypeId
        ));

        //on récupère la pagination enregistrée en base
        $pagination=(int) $countItems[0]['it_pagination'];
        //on récupère le nombre d'items
        $countItems=(int) $countItems[0]['count(iditem)'];


        $theModelLines=self::getModelLines($ormManager,$itemtypeId);


        $nul=array('result'=>array(),'numItems'=>0,'pagination'=>$pagination,'numPages'=>1);
        $nul['modelLines']=$theModelLines;


        //si aucun , inutile d'aller plus loin
        if($countItems==0){return $nul;}
        if(count($theModelLines)==0){return $nul;}



        //requête pour récupérer les items
        $query="LEFT JOIN model_line ON model_line.idmodel_line=item_values.model_line_idmodel_line 
             LEFT JOIN item_types ON item_types.iditem_types=model_line.item_types_iditem_types
             LEFT JOIN item ON item_values.item_iditem=item.iditem 
             WHERE model_line.item_types_iditem_types=:itemType";
        $query2="LEFT JOIN model_line ON model_line.idmodel_line=item_values.model_line_idmodel_line 
             LEFT JOIN item_types ON item_types.iditem_types=model_line.item_types_iditem_types
             LEFT JOIN item ON item_values.item_iditem=item.iditem 
             LEFT JOIN item_value_media ON item_value_media.item_values_iditem_values=item_values.iditem_values
             LEFT JOIN medias ON item_value_media.medias_idmedias=medias.idmedias
             WHERE model_line.item_types_iditem_types=:itemType";
        //tableau pour binder les paramètres de la requête
        $arrayPrepa=array(":itemType"=>$itemtypeId);




        //configuration des clauses sql de base
        $orderBy="";
        $whereFirstQuery="";
        $clauseItems="";


        //filtres disponibles
        $filtresDispos=array('item_date_creation'=>"int",'item_date_update'=>"int");//filtres de base

        // si pas de sortObject défini, on met d'office item_date_dreation
        if(!$sortObject)
        {
            $sortObject=array('item_date_creation','DESC');
        }

        //si filtre on désactive le sort ... mouais ...
        if($itemFilterObject)
        {
            $sortObject=false;
            if($itemFilterObject['compar']=="%LIKE%")
            {
                $whereFirstQuery=" AND model_line.ml_code='".$itemFilterObject['col']."' AND item_values.iv_content LIKE '%".$itemFilterObject['value']."%' ";

            }
            else
            {

                /*$mlConcerned=false;
                foreach($theModelLines as $ml)
                {
                    if($ml['ml_code']==$itemFilterObject['col'])
                    {
                        $mlConcerned=$ml;
                    }
                }
                if($mlConcerned)
                {
                    if($mlConcerned['ml_type_code']=="fkmul")
                    {
                        $whereFirstQuery=" AND model_line.ml_code='".$itemFilterObject['col']."' AND item_values.iv_content ".$itemFilterObject['compar']." ".$itemFilterObject['value']." ";

                    }
                    else
                    {
                        $whereFirstQuery=" AND model_line.ml_code='".$itemFilterObject['col']."' AND item_values.iv_content ".$itemFilterObject['compar']." ".$itemFilterObject['value']." ";

                    }

                }*/
                $whereFirstQuery=" AND model_line.ml_code='".$itemFilterObject['col']."' AND item_values.iv_content ".$itemFilterObject['compar']." ".$itemFilterObject['value']." ";

            }
        }

        //si on a un sortObject
        if($sortObject)
        {
            //on parcour les modelLines pour mettre à jour le tableau des filtres disponibles
            foreach($theModelLines as $ml)
            {
                $filtresDispos[$ml['ml_code']]=$ml['ml_type_code'];
            }

            $sortObjectOk=false;//on initialise à false
            //si le sortObject choisi est OK
            if(in_array($sortObject[0],array_keys($filtresDispos)) && ($sortObject[1]=="ASC" || $sortObject[1]=="DESC"))
            {
                $sortObjectOk=true;
                if($sortObject[0]=="item_date_creation" || $sortObject[0]=="item_date_update")//sortObject simple
                {
                    $orderBy=" ORDER BY item.".$sortObject[0]." ".$sortObject[1];
                    $whereFirstQuery=" AND model_line.ml_code='".$theModelLines[0]['ml_code']."'";
                }
                else//sortObject compliqué
                {

                    $typeSql=TypesHelper::sqlCast($filtresDispos[$sortObject[0]]);//on récupère les types de données

                    if($typeSql=="SIGNED")//on vérifie qu'en sql c'est du SIGNED ou pas et on applique le critère casté ou pas
                    {
                        $sqlOrderCriteria="CAST(item_values.iv_content AS SIGNED)";
                    }
                    else
                    {
                        $sqlOrderCriteria="item_values.iv_content";
                    }
                    //on définit le orderBy
                    $orderBy="  ORDER BY
                                CASE WHEN ml_code='".$sortObject[0]."' THEN ".$sqlOrderCriteria."
                                END ".$sortObject[1];

                    $whereFirstQuery=" AND model_line.ml_code='".$sortObject[0]."'";
                }
            }
        }


        //si on demande une page en particulier
        if($page>0)
        {
            //echo $query." ".$whereFirstQuery." ".$orderBy;exit;

            //on fait une requête paginée pour récupérer les ids d'items,
            $paginator=new Paginator();
            $paginator->setOrmManager($ormManager);
            //echo $query." ".$whereFirstQuery." ".$orderBy;
            $paginator->setParams("item_values","*",$query." ".$whereFirstQuery." ".$orderBy,array(":itemType"=>$itemtypeId));//normalement le orderBy est inutile , là on compte juste
            $paginator->setNumResults($pagination);
            //$theCount=($sortObject!=false?$countItems:$countItems/count($theModelLines));
            $paginator->doIt($countItems);
            //si la page demandée est supérieure au nombre de pages, cassos
            if($page>$paginator->numPages)
            {
                return $nul;
            }

            //on récupère maintenant les ids d'item concernés
            $idItems=$paginator->getDatas($page);

            $idItemsSql="(";
            for($i=0;$i<count($idItems);$i++)
            {
                $idItemsSql.=$idItems[$i]['iditem'].($i<count($idItems)-1?",":")");
            }

            //on stocke la clause d'iditems pour la ré utiliser encuite sur  la query principale
            $clauseItems=" AND item_values.item_iditem IN ".$idItemsSql;


        }






        //ce hack permet d'avoir les images dans une clé reqPicsAcms
        $colums="*,GROUP_CONCAT(media_path) as reqPicsAcms";
        $req=$ormManager->doQuery("item_values",$colums,$query2." ".$clauseItems." GROUP BY item_values.iditem_values ".$orderBy,$arrayPrepa,true);


        //on repasse une moulinnete pour bien mettre les ordres
        if($sortObject && $sortObjectOk==true)
        {
            $tmpArrayReq1=array();
            $tmpArrayReq2=array();
            foreach($req as $item)
            {
                if($item['ml_code']==$sortObject[0])
                {
                    $tmpArrayReq1[]=$item;
                }
                else
                {
                    $tmpArrayReq2[]=$item;
                }
            }
            $req=array_merge($tmpArrayReq1,$tmpArrayReq2);

        }


        //echo "<pre>";var_dump($req);
        //on peut alors préparer le nécessaire pour faire un pseudo groupBy iditem
        $items=array();//tableau qui contiendra les items
        $ids=array();//tableau des ids déjà récupérés
        $increItem=0;//...
        $mainFields=array('iditem','item_date_creation','item_date_update','iditem_types','it_name','it_code');//champs qu'on récupère pour un item

        foreach($req as $item)
        {

            if(!in_array($item['item_iditem'],array_keys($ids)))
            {
                $ids[$item['item_iditem']]=$increItem;
                $items[$increItem]=array();
                foreach($mainFields as $mf)
                {
                    $items[$increItem][$mf]=$item[$mf];

                }
                $items[$increItem]['fields']=array();
                //si reqPicsAcms, c'est la liste des images de la colonne de type ml_code=>picture ou pictures
                if(strlen($item['reqPicsAcms'])>0)
                {
                    $items[$increItem]['fields'][$item['ml_code']]=$item['reqPicsAcms'];
                }
                else
                {
                    $items[$increItem]['fields'][$item['ml_code']]=$item['iv_content'];
                }

                $increItem++;

            }
            else
            {
                if(strlen($item['reqPicsAcms'])>0)
                {

                    //$items[$increItem]['fields'][$item['ml_code']]=$item['reqPicsAcms'];
                    $items[$ids[$item['item_iditem']]]['fields'][$item['ml_code']]=$item['reqPicsAcms'];
                }
                else
                {
                    $items[$ids[$item['item_iditem']]]['fields'][$item['ml_code']]=$item['iv_content'];
                }

            }
        }
        $retour=array('result'=>$items,'numItems'=>$countItems,'pagination'=>$pagination,'numPages'=>(isset($paginator)?$paginator->numPages:1));
        $retour['modelLines']=$theModelLines;
        //echo "<pre>";var_dump($items);
        return $retour;


    }

    //retournes les itemtypes d'un projet pour le menu du backend
    public static function getItemTypesChild($ormManager,$itId)
    {
        $theItemTypes=$ormManager->doQuery('model_line',"*","LEFT JOIN item_types ON model_line.item_types_iditem_types=item_types.iditem_types WHERE model_line.item_types_iditem_types1=:itId",array(":itId"=>$itId));
        return $theItemTypes;
    }

    //retournes les itemtypes d'un projet pour le menu du backend
    public static function getItemTypesForBackendMenu($ormManager,$idDataproj)
    {
        $theItemTypes=$ormManager->doQuery('item_types',"*","WHERE item_types.dataproj_iddataproj=:idDp AND item_types.it_menu_visibility=1",array(":idDp"=>$idDataproj));
        return $theItemTypes;
    }

    //retournes les fixeddatas d'un projet
    public static function getFixedDatas($ormManager,$idDataproj,$codeDatasGroup=false)
    {
        $mainQuery="LEFT JOIN fixed_datas_group ON fixed_datas_group.idfixed_datas_group=fixed_datas.fixed_datas_group_idfixed_datas_group ";
        if($codeDatasGroup)
        {
            $datas=$ormManager->doQuery('fixed_datas',"*",$mainQuery." WHERE fixed_datas.dataproj_iddataproj=:idDp AND fixed_datas_group.fdg_code=:codegroup",array(":idDp"=>$idDataproj,":codegroup"=>$codeDatasGroup));
            $retour=array();
            foreach($datas as $fd)
            {
                if($fd['fd_type_code']=="file" || $fd['fd_type_code']=="files" || $fd['fd_type_code']=="picture" || $fd['fd_type_code']=="pictures")
                {
                    $file=$ormManager->doQuery('fixed_data_media',"*","LEFT JOIN medias ON medias.idmedias=fixed_data_media.medias_idmedias WHERE fixed_data_media.fixed_datas_idfixed_datas=:fd",array(':fd'=>$fd['idfixed_datas']));

                    $retour[$fd['fd_code']]=$file;
                    //$datas[0][$datas[0]['fd_type_code']]=$ormManager->doQuery('fixed_data_media',"*","LEFT JOIN medias ON medias.idmedias=fixed_data_media.medias_idmedias WHERE fixed_data_media.fixed_datas_idfixed_datas=:fd",array(':fd'=>$datas[0]['idfixed_datas']));
                }
                else
                {
                    $retour[$fd['fd_code']]=$fd['fd_content'];
                }


            }
            return $retour;
        }
        else
        {
            $datas=$ormManager->doQuery('fixed_datas',"*",$mainQuery." WHERE fixed_datas.dataproj_iddataproj=:idDp",array(":idDp"=>$idDataproj));
        }



        $arrayGroups=array();
        $arrayNoGroups=array();
        foreach($datas as $dat)
        {
            if(strlen($dat['idfixed_datas_group'])>0)
            {
                if(!in_array($dat['idfixed_datas_group'],array_keys($arrayGroups)))
                {
                    $arrayGroups[$dat['idfixed_datas_group']]=array("name"=>$dat['fd_name'],"datas"=>array());
                }
                $arrayGroups[$dat['idfixed_datas_group']]['datas'][]=$dat;
            }
            else
            {
                $arrayNoGroups[]=$dat;
            }


        }

        return array("grouped"=>$arrayGroups,"ungrouped"=>$arrayNoGroups);
    }

    //retournes les fixeddatas d'un projet
    public static function getFixedDatasByCode($ormManager,$idDataproj,$codeDatas)
    {

            $datas=$ormManager->doQuery('fixed_datas',"*"," WHERE fixed_datas.dataproj_iddataproj=:idDp AND fixed_datas.fd_code=:code",array(":idDp"=>$idDataproj,":code"=>$codeDatas));
            if(count($datas)>0)
            {
                if($datas[0]['fd_type_code']=="file" || $datas[0]['fd_type_code']=="files" || $datas[0]['fd_type_code']=="picture" || $datas[0]['fd_type_code']=="pictures")
                {
                    $datas[0][$datas[0]['fd_type_code']]=$ormManager->doQuery('fixed_data_media',"*","LEFT JOIN medias ON medias.idmedias=fixed_data_media.medias_idmedias WHERE fixed_data_media.fixed_datas_idfixed_datas=:fd",array(':fd'=>$datas[0]['idfixed_datas']));
                }
            }
            return $datas;


    }

    //retournes les infos globals d'un projet
    public static function getGlobalInfosForProject($ormManager,$idDataproj)
    {
        $infos=$ormManager->doQuery('dataproj',"*","WHERE dataproj.iddataproj=:idDp",array(":idDp"=>$idDataproj));
        if(count($infos)>0)
        {return $infos[0];}
        else{return false;}
    }

    public static function getRightActionBdd($typeTable,$idConcerned)
    {

    }


}
