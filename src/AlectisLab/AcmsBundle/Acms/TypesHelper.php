<?php

namespace AlectisLab\AcmsBundle\Acms;


//helper pour les types de champs
class TypesHelper
{



    //liste des types
    public static function getListTypesCodes()
    {
        return array
        (
            "text"=>"VARCHAR",
            "double_or_int"=>"SIGNED",
            "email"=>"VARCHAR",
            "int"=>"SIGNED",
            "file"=>"VARCHAR",
            "files"=>"VARCHAR",
            "picture"=>"VARCHAR",
            "pictures"=>"VARCHAR",
            "text_html"=>"VARCHAR",
            "date"=>"SIGNED",
            "bool"=>"SIGNED",
            "color"=>"VARCHAR",
            "fk"=>"VARCHAR",
            "fkmul"=>"VARCHAR",
            "gps"=>"VARCHAR",
            "choice_list"=>"VARCHAR"//,
            //"choice_list_mul"=>"VARCHAR"
        );
    }

    public static function sqlCast($type)
    {
        $types=self::getListTypesCodes();
        foreach($types as $aType=>$sqlType)
        {
            if($aType==$type)
            {
                return $sqlType;
            }
        }
    }

    //function qui valide un type en fonction d'un code et d'une valeur
    public function validateFix($entity)
    {
        if(($entity->get('fd_content')==null || strlen($entity->get('fd_content'))==0) && $entity->get('fd_mandatory')==1)
        {
            return array('result'=>false,'message'=>self::getMessages()['mandatory']);
        }

        if(strlen($entity->get('fd_content'))>(int) $entity->get('fd_maxlength') && $entity->get('fd_maxlength')!=NULL)
        {

            return array('result'=>false,'message'=>self::getMessages()['max-length-str']);
        }

        //type text
        if($entity->get('fd_type_code')=="text" || $entity->get('fd_type_code')=="file" || $entity->get('fd_type_code')=="picture")
        {

            if(preg_match("#<.+| +>.+| +</.+| +>#i",$entity->get('fd_content')) OR preg_match("#<.+| +//+>#i",$entity->get('fd_content')))
            {
                //$result=false;
                //$messages[$modelLine['ml_code']]=self::getMessages()['wrong-format'];
                return array('result'=>false,'message'=>self::getMessages()['wrong-format']);
            }
        }

        //type email
        if($entity->get('fd_type_code')=="email")
        {
            if (!filter_var($entity->get('fd_content'),FILTER_VALIDATE_EMAIL))
            {
                if($entity->get('fd_content')==null || strlen($entity->get('fd_content'))==0)
                {}
                else
                {
                    //$result=false;
                    //$messages[$modelLine['ml_code']]=self::getMessages()['wrong-format'];
                    return array('result'=>false,'message'=>self::getMessages()['wrong-format']);
                }
            }
        }

        //type double_or_int
        if($entity->get('fd_type_code')=="double_or_int")
        {

            if (!filter_var($entity->get('fd_content'),FILTER_VALIDATE_FLOAT) && !filter_var($entity->get('fd_content'),FILTER_VALIDATE_INT))
            {
                if($entity->get('fd_content')==null || strlen($entity->get('fd_content'))==0)
                {}
                else
                {
                    return array('result'=>false,'message'=>self::getMessages()['wrong-format']);
                }
            }
        }

        //type int
        if($entity->get('fd_type_code')=="int" || $entity->get('fd_type_code')=="date")
        {

            if (!filter_var($entity->get('fd_content'),FILTER_VALIDATE_INT))
            {
                if($entity->get('fd_content')==null || strlen($entity->get('fd_content'))==0)
                {}
                else
                {
                    return array('result'=>false,'message'=>self::getMessages()['wrong-format']);
                }
            }
        }
        return array('result'=>true);


    }
    //function qui valide un type en fonction d'un model et d'une valeur
    public static function validate($modelLine,$value)
    {
        $result=true;
        $messages=array();
        if(($value==null || strlen($value)==0) && $modelLine['ml_mandatory']==1)
        {
            return array('result'=>false,'message'=>self::getMessages()['mandatory']);
        }


        if(strlen($value)>(int) $modelLine['ml_maxlength'] && $modelLine['ml_maxlength']!=NULL)
        {

            return array('result'=>false,'message'=>self::getMessages()['max-length-str']);
        }

        //type text
        if($modelLine['ml_type_code']=="text" || $modelLine['ml_type_code']=="file" || $modelLine['ml_type_code']=="picture")
        {

            if(preg_match("#<.+| +>.+| +</.+| +>#i",$value) OR preg_match("#<.+| +//+>#i",$value))
            {
                //$result=false;
                //$messages[$modelLine['ml_code']]=self::getMessages()['wrong-format'];
                return array('result'=>false,'message'=>self::getMessages()['wrong-format']);
            }
        }

        //type email
        if($modelLine['ml_type_code']=="email")
        {
            if (!filter_var($value,FILTER_VALIDATE_EMAIL))
            {
                if($value==null || strlen($value)==0)
                {}
                else
                {
                    //$result=false;
                    //$messages[$modelLine['ml_code']]=self::getMessages()['wrong-format'];
                    return array('result'=>false,'message'=>self::getMessages()['wrong-format']);
                }
            }
        }

        //type double_or_int
        if($modelLine['ml_type_code']=="double_or_int")
        {

            if (!filter_var($value,FILTER_VALIDATE_FLOAT) && !filter_var($value,FILTER_VALIDATE_INT))
            {
                if($value==null || strlen($value)==0)
                {}
                else
                {
                    return array('result'=>false,'message'=>self::getMessages()['wrong-format']);
                }
            }
        }

        //type int
        if($modelLine['ml_type_code']=="int" || $modelLine['ml_type_code']=="date")
        {

            if (!filter_var($value,FILTER_VALIDATE_INT))
            {
                if($value==null || strlen($value)==0)
                {}
                else
                {
                    return array('result'=>false,'message'=>self::getMessages()['wrong-format']);
                }
            }
        }
        return array('result'=>true);

    }

    //fonction qui retourne les messages
    public static function getMessages($lang="FR")
    {
        $messages=array
        (
            "FR"=>array
            (
                "wrong-format"=>"Format invalide",
                "max-length-str"=>"Nombre de caractère maximum atteint",
                "mandatory"=>"Champs obligatoire",

            )
        );
        return $messages[$lang];
    }




}
