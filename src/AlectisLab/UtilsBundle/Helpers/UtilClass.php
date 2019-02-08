<?php

namespace AlectisLab\UtilsBundle\Helpers;


class UtilClass
{
    public static function trueUrl()
    {
        $pageURL = 'http';
        if (ISSET($_SERVER["HTTPS"])) {
            $pageURL .= "s";
        }
        $pageURL .= "://";
        if ($_SERVER["SERVER_PORT"] != "80")
        {
            $pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
        }
        else
        {
            $pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
        }
        return $pageURL;
    }

    public static function rewritingOrNot()
    {
        if(isset($_SERVER["HTTP_HOST"]))
        {
            $uri=str_replace("/app_dev.php","", $_SERVER["REQUEST_URI"]);

            if(strlen($_SERVER["REQUEST_URI"])==0 || $_SERVER["REQUEST_URI"]=="/")
            {
                return false;
            }
            else
            {
                return true;
            }
        }
        else
        {
            return "web/";
        }
    }

    public static function customEcho($content,$exit=false)
    {
        echo "<pre>";
        var_dump($content);
        if($exit){exit;}
    }

    public static function mimiTypeToExtension($mimeType)
    {
        switch($mimeType)
        {
            case "image/jpeg":
                return ".jpg";
                break;

            case "image/pjpeg":
                return ".jpg";
                break;

            case "image/png":
                return ".png";
                break;

            case "image/x-png":
                return ".png";
                break;


            default:
                return false;
                break;
        }
    }

    public static function firstEmailPart($email)
    {
        $tab=explode('@',$email);
        if(count($tab)==2)
        {
            return $tab[0];
        }
        else {return false;}
    }

    public static function cryptImageName($img)
    {
        $extensionV=explode(".", $img);
        $extension=".".$extensionV[count($extensionV)-1];
        return md5($img.time()).$extension;


    }

    public static function getExtension($file)
    {
        $extensionV=explode(".", $file);
        $extension=".".$extensionV[count($extensionV)-1];
        return $extension;


    }




}
