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
            $prices=$ormManager->doQuery('prices',"*","LEFT JOIN cat_prices ON cat_prices.idcat_prices=prices.cat_prices_idcat_prices WHERE prices.w_templates_idw_templates=:id",array(':id'=>$id));

            //var_dump($prices);exit;
            //$options=$ormManager->doQuery('options_cat',"*","LEFT JOIN cat_prices ON cat_prices.idcat_prices=options_cat.cat_prices_idcat_prices LEFT JOIN prices ON prices.cat_prices_idcat_prices=cat_prices.idcat_prices WHERE prices.w_templates_idw_templates=:id");

            $options=$ormManager->doQuery('options_cat',"*","LEFT JOIN cat_prices ON cat_prices.idcat_prices=options_cat.cat_prices_idcat_prices LEFT JOIN prices ON prices.cat_prices_idcat_prices=cat_prices.idcat_prices LEFT JOIN options ON options.idoptions=options_cat.options_idoptions WHERE prices.w_templates_idw_templates=:id AND options.opt_is_visible=1",array(':id'=>$id));
            //echo "<pre>";var_dump($options);exit;
            //$pcTab=array('GRATOS','STARTER','PREMIUM','PRO','VIP');
            //$optTab=array("ADPUB","FOOTER_COPYRIGHT","CUSTOM_DOMAIN","SUPPORT_MAIL_24","SUPPORT_PREMIUM","PRESTA_LIKE");

            $arrayPacks=array();
            $corresPacksLabels=array();
            $corresCoolOpt=array();
            foreach($options as $opt)
            {
                if(!in_array($opt['cat_price_code'],array_keys($arrayPacks)))
                {
                   $arrayPacks[$opt['cat_price_code']]=array();
                   $corresPacksLabels[$opt['cat_price_code']]=$opt['cat_price_label'];
                }
                $arrayPacks[$opt['cat_price_code']][$opt['opt_code']]=array
                (
                    "enabled"=>$opt['optcat_is_enabled'],
                    "label"=>$opt['opt_label']
                );
            }

            $arrayPrices=array();
            foreach($prices as $prix)
            {
                if(!in_array($prix['cat_price_code'],array_keys($arrayPrices)))
                {
                    $arrayPrices[$prix['cat_price_code']]=$prix['price_value'];
                }
            }


            $retour['prix']=$arrayPrices;
            $retour['packs']=$arrayPacks;
            $retour['corresPacksLabels']=$corresPacksLabels;


            return $retour;
        }
        else
        {
            return false;
        }

    }


}
