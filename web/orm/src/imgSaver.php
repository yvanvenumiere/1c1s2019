<?php
/*
* (c) 2013 Yvan Vénumière <yvan.venumierer@gmail.com>
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/
			
/**
* imgSaver is class that helps to manage pictures
*
*/
class imgSaver
	{
		private $type;
		private $linkImg;
		public $image;
		public $savedImgUrl;
		public $arrayImg=array();
		private $arrayNamesForGrid=array();
		
		/**
		* constuctor of the class
		* @param string $_imageType type of image
		* @param string $_linkImg link to the picture
		*/
		public function __construct($_imageType,$_linkImg)
		{
			switch($_imageType)
			{
				case "image/jpeg":
				$this->type="jpeg";
				break;
				
				case "image/pjpeg":
				$this->type="jpeg";
				break;
				
				case "image/png":
				$this->type="png";
				break;
				
				case "image/x-png":
				$this->type="png";
				break;
				
				case "png":
				$this->type="png";
				break;
				
				case "jpg":
				$this->type="jpeg";
				break;
				
				case "jpeg":
				$this->type="jpeg";
				break;
				
				default:
				$this->type=false;
				break;
			}
			$this->linkImg=$_linkImg;
		}
		
		
		/**
		* method that init the object with the infos we passesd in the constructor
		 *@return boolean returns a boolean that indicates if initialisation worked 
		*/
		public function init()
		{
			if(!$this->type){return false;}else{return true;}
		}
		
		/**
		* method that handles the picture
		 *@return boolean returns a boolean that indicates if handling worked 
		*/
		public function handleImage()
		{
			switch($this->type)
			{
				case "jpeg":
				$this->image=imagecreatefromjpeg($this->linkImg);
				return $this->image;
				break;
				case "png":
				$this->image=imagecreatefrompng($this->linkImg);
				return $this->image;
				break;
				default:
				return false;
			};
		}
		
		/**
		* method that returns a clone of the handeled picture
		 *@return boolean returns a boolean that indicates if cloning worked 
		*/
		public function getCloneImage()
		{
			$retour;
			switch($this->type)
			{
				case "jpeg":
					$retour=imagecreatefromjpeg($this->linkImg);
					
				break;
				case "png":
					$retour=imagecreatefrompng($this->linkImg);
				break;
			};
			return $retour;
		}
		
		
		/**
		* method that is one of the reisize function
		* @param number $ratio ratio of resizing
		* @param string $nameInArray referenced name of the futur resized picture in the array that contains all the resized pictures
		*@return boolean returns a boolean that indicates if resizing worked
		*/
		public function resizeHomo($ratio,$nameInArray)
		{
			$dimensions=getimagesize($this->linkImg);
			$newW=$dimensions[0]*$ratio;
			$newH=$dimensions[1]*$ratio;
			$newImage=imagecreatetruecolor($newW,$newH);
			$redim=imagecopyresampled($newImage,$this->image,0,0,0,0,$newW,$newH,$dimensions[0],$dimensions[1]);
			if(!$redim){return false;}
			$this->arrayImg[$nameInArray]=$newImage;
			/*$this->image=null;
			$this->image=$new_image;*/
			return true;
		}
		
		/**
		* method that is one of the reisize function
		* @param number $width width of resizing
		* @param number $height height of resizing
		* @param string $nameInArray referenced name of the futur resized picture in the array that contains all the resized pictures
		*@return boolean returns a boolean that indicates if resizing worked
		*/
		public function resizeFix($width,$height,$nameInArray)
		{
			$dimensions=getimagesize($this->linkImg);
			$newImage=imagecreatetruecolor($width,$height);
			$redim=imagecopyresampled($newImage,$this->image,0,0,0,0,$width,$height,$dimensions[0],$dimensions[1]);
			if(!$redim){return false;}
			$this->arrayImg[$nameInArray]=$newImage;
			/*$this->image=null;
			$this->image=$new_image;*/
			return true;
		}
		
		/**
		* method that is one of the reisize function
		* @param number $newW width of resizing
		* @param string $nameInArray referenced name of the futur resized picture in the array that contains all the resized pictures
		*@return boolean returns a boolean that indicates if resizing worked
		*/
		public function resizeHomoW($newW,$nameInArray)
		{
			$dimensions=getimagesize($this->linkImg);
			$ratio=$newW/$dimensions[0];
			$newH=$dimensions[1]*$ratio;
			$newImage=imagecreatetruecolor($newW,$newH);
			$redim=imagecopyresampled($newImage,$this->image,0,0,0,0,$newW,$newH,$dimensions[0],$dimensions[1]);
			if(!$redim){return false;}
			$this->arrayImg[$nameInArray]=$newImage;
			/*$this->image=null;
			$this->image=$new_image;*/
			return true;
		}
		
		/**
		* method that is one of the reisize function
		* @param number $newH height of resizing
		* @param string $nameInArray referenced name of the futur resized picture in the array that contains all the resized pictures
		*@return boolean returns a boolean that indicates if resizing worked
		*/
		public function resizeHomoH($newH,$nameInArray)
		{
			$dimensions=getimagesize($this->linkImg);
			$ratio=$newH/$dimensions[1];
			$newW=$dimensions[0]*$ratio;
			$newImage=imagecreatetruecolor($newW,$newH);
			$redim=imagecopyresampled($newImage,$this->image,0,0,0,0,$newW,$newH,$dimensions[0],$dimensions[1]);
			if(!$redim){return false;}
			$this->arrayImg[$nameInArray]=$newImage;
			/*$this->image=null;
			$this->image=$new_image;*/
			return true;
		}

		/**
		* method that is one of the reisize function
		* @param string $nameInArray referenced name of the futur resized picture in the array that contains all the resized pictures
		* @return boolean returns a boolean that indicates if resizing worked
		*/
		public function noResize($nameInArray)
		{
			$dimensions=getimagesize($this->linkImg);

			$newImage=imagecreatetruecolor($dimensions[0],$dimensions[1]);
			$redim=imagecopyresampled($newImage,$this->image,0,0,0,0,$dimensions[0],$dimensions[1],$dimensions[0],$dimensions[1]);
			if(!$redim){return false;}
			$this->arrayImg[$nameInArray]=$newImage;
			return true;
		}
		
		
		/**
		* method that saves the picture
		* @param string $nom name of the new picture ( real name )
		* @param string $nameInArray referenced name of the resized picture in the array that contains all the resized pictures
		* @return boolean returns a boolean that indicates if saving worked worked
		*/
		public function saveImg($nom,$nameInArray)
		{
			$retour;
			switch($this->type)
			{
				case "jpeg":
					if(@imagejpeg($this->arrayImg[$nameInArray],$nom.".jpg"))
					{$this->savedImgUrl=$nom.".jpg";return true;}
					else{return false;}
					
				break;
				
				case "png":
					if(@imagepng($this->arrayImg[$nameInArray],$nom.".png"))
					{$this->savedImgUrl=$nom.".png";return true;}
					else{return false;}
				break;
			};
		}
		
		/**
		* method that returns the name of a picture from the saved picture url
		* @param string $nameInArray referenced name of the futur resized picture in the array that contains all the resized pictures
		* @return string returns a string
		*/
		public function getName()
		{
			$array=explode("/",$this->savedImgUrl);
			return $array[count($array)-1];
		}
		
		/// ???
		public function getNamesForGrid()
		{
			return $this->arrayNamesForGrid;
		}
	}
?>