<?php
/*
* This file is a class that contains utils methods
* (c) 2012 Yvan Vénumière <yvan.venumierer@gmail.com>
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

/**
* myTools is an object that contains utils methods
*
*/
class myTools
{
	/**
	* constructor for the class
	*/
	public function __construct()
	{}
	
	/**
	* methods that returns the extension of a file
    * @param string $file  the name of the file
	* @return string returns a string with the extension of the file
    */
	public static function getExtension($file)
	{
		$arr=explode(".",$file);
		return $arr[count($arr)-1];
	}
	
	/**
	* methods that returns the extension of a file
    * @param xml $xml the xml file we work on
	* @param string $wantedNode name of the node we want to target
    * @param string $configName name of configuration node we want to search
	* @return string returns a string with the information
    */
	public static function getConfigFromGlobalXml($xml,$wantedNode,$configName)
	{
		if(isset($xml->{"".$wantedNode.""}->{"".$configName.""}))
		{
			return (string) $xml->{"".$wantedNode.""}->{"".$configName.""};
		}
		else
	    {
			return (string) $xml->generic->{"".$configName.""};
		}
	}
	
	/**
	* methods that returns a formated date
    * @param int $timestamp an unix timestamp
	* @param string $format a date format that we define for the crud manager
	* @return string returns a formated date
    */
	public static function getDateFromTimeAndCustomFormat($timestamp,$format)
	{
		$newFormat=str_replace("dd", "d", $format);
		$newFormat=str_replace("mm", "m", $newFormat);
		$newFormat=str_replace("yy", "Y", $newFormat);
		return date($newFormat,$timestamp);
	}
	

}
