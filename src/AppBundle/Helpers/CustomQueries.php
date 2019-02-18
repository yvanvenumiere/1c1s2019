<?php

namespace AppBundle\Helpers;
use AlectisLab\AcmsBundle\Acms\AcmsGlobalHelper;
use Symfony\Component\HttpFoundation\Session\Session;



class CustomQueries
{

    public static function getDataTemplates($ormManager)
    {
        $infos=$ormManager->doQuery('tags_templates',"*","LEFT JOIN w_templates ON w_templates.idw_templates=tags_templates.w_templates_idw_templates LEFT JOIN wtags ON tags_templates.wtags_idwtags=wtags.idwtags");
        $templates=array();
        $tags=array();
        foreach($infos as $template)
        {
            if(!in_array($template['wt_bundle_name'],array_keys($templates)))
            {
                $templates[$template['wt_bundle_name']]=$template;
                $templates[$template['wt_bundle_name']]['tagsCodes']=" ".$template['wtag_code'];
            }
            else
            {
                $templates[$template['wt_bundle_name']]['tagsCodes'].=" ".$template['wtag_code'];
            }


            if(!in_array($template['wtag_code'],$tags))
            {
                $tags[]=array($template['wtag_code'],$template['wtag_name']);
            }
        }
        return array('tags'=>$tags,"templates"=>$templates);

    }

    public static function getProduit($ormManager,$id)
    {
        $infos=$ormManager->doQuery("w_templates","*","WHERE w_templates.idw_templates=:id",array(':id'=>$id));

        if(count($infos)>0)
        {
            $retour=$infos[0];
            $features=$ormManager->doQuery('features',"*","WHERE features.w_templates_idw_templates=:id ORDER BY features.feature_order ASC",array(':id'=>$id));

            $retour['features']=$features;
            $medias=$ormManager->doQuery('media_template',"*","LEFT JOIN medias ON medias.idmedias=media_template.medias_idmedias WHERE media_template.w_templates_idw_templates=:id",array(':id'=>$id));

            $retour['medias']=$medias;
            $prices=$ormManager->doQuery('prices',"*","WHERE prices.w_templates_idw_templates=:id",array(':id'=>$id));

            //var_dump($prices);exit;
            //$options=$ormManager->doQuery('options_cat',"*","LEFT JOIN cat_prices ON cat_prices.idcat_prices=options_cat.cat_prices_idcat_prices LEFT JOIN prices ON prices.cat_prices_idcat_prices=cat_prices.idcat_prices WHERE prices.w_templates_idw_templates=:id");

            $pcTab=array('GRATOS','STARTER','PREMIUM','PRO','VIP');
            $optTab=array("ADPUB","FOOTER_COPYRIGHT","CUSTOM_DOMAIN","SUPPORT_MAIL_24","SUPPORT_PREMIUM","PRESTA_LIKE");

            /*foreach($options as $opt)
            {

            }*/

            //echo "<pre>";var_dump($options);exit;
            $retour['prix']=$prices;

            return $retour;
        }
        else
        {
            return false;
        }

    }


}
