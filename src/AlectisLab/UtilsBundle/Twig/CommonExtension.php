<?php


namespace AlectisLab\UtilsBundle\Twig;



class CommonExtension extends \Twig_Extension

{

    public function getFilters()

    {

        return array(

            'slug' => new \Twig_Filter_Method($this, 'slugFilter'),
            'apercu' => new \Twig_Filter_Method($this, 'apercuFilter')

        );

    }



    public function slugFilter($text)

    {

        // replace non letter or digits by -

        //$text=strtr ( $text, 'àáâãäçèéêëìíîïñòóôõöùúûüýÿÀÁÂÃÄÇÈÉÊËÌÍÎÏÑÒÓÔÕÖÙÚÛÜÝ', 'aaaaaceeeeiiiinooooouuuuyyAAAAACEEEEIIIINOOOOOUUUUY' );

        $text=str_replace("é","e",$text);

        $text=str_replace("è","e",$text);

        $text=str_replace("ë","e",$text);

        $text = preg_replace('~[^\\pL\d]+~u', '-', $text);



        // trim

        $text = trim($text, '-');



        // transliterate

        if (function_exists('iconv'))

        {

            $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);

        }



        // lowercase

        $text = strtolower($text);



        // remove unwanted characters

        $text = preg_replace('~[^-\w]+~', '', $text);



        if (empty($text))

        {

            return 'n-a';

        }



        return $text;

    }

    public function apercuFilter($text,$length)
    {
        if(strlen($text)>$length)
        {
            return strip_tags(substr($text,0,$length)." ...");
            //return $text;
        }
        else
        {
            return $text;
        }
    }



    public function getName()

    {

        return 'common_extension';

    }

}