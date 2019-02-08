<?php
/*
* (c) 2013 Yvan Vénumière <yvan.venumierer@gmail.com>
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace AlectisLab\UtilsBundle\Helpers;
			
/**
* imgSaver is class that helps to manage pictures
*
*/
class ImgSaver
	{
		public $type;
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
				
				case "JPG":
				$this->type="jpeg";
				break;
				
				case "JPEG":
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
		 * method that returns the dimensions of the picture
		 *@return array returns an array with the width end the height
		 */
		public function getDimensions()
		{
			$dimensions=getimagesize($this->linkImg);
			return $dimensions;
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
                        imagealphablending( $newImage, false );
                        imagesavealpha( $newImage, true );
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
                        imagealphablending( $newImage, false );
                        imagesavealpha( $newImage, true );
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
                        imagealphablending( $newImage, false );
                        imagesavealpha( $newImage, true );
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
                        imagealphablending( $newImage, false );
                        imagesavealpha( $newImage, true );
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
		 * @param number $newW width of resizing
		 * @param string $nameInArray referenced name of the futur resized picture in the array that contains all the resized pictures
		 *@return boolean returns a boolean that indicates if resizing worked
		 */
		public function resizeCrop($newW,$newH,$nameInArray)
		{
			
			$dimensions=getimagesize($this->linkImg);
                        $xDest=0;$yDest=0;
                        $calculatedW=$newW;
                        $calculatedH=$newH;
                        
			if($dimensions[0]>$dimensions[1])//on est en largeur
			{$calculatedH=($dimensions[1]/$dimensions[0])*$calculatedW;}
			else
			{$calculatedW=$calculatedH/($dimensions[1]/$dimensions[0]);}
                        
                        if($calculatedH<$newH)//si on est trop petit en hauteur
                        {
                            
                            $calculatedH=$newH;
                            $calculatedW=$calculatedH/($dimensions[1]/$dimensions[0]);
                        }
                        else
                        {
                            $calculatedW=$newW;
                            $calculatedH=($dimensions[1]/$dimensions[0])*$calculatedW;
                        }
                        
                        $xDest=($newW/2)-($calculatedW/2);
                        $yDest=($newH/2)-($calculatedH/2);
			$newImage=imagecreatetruecolor($newW,$newH);
                        $blanc = imagecolorallocate($newImage, 255, 255, 255);
                        imagefill($newImage, 0, 0, $blanc);
                        imagealphablending( $newImage, false );
                        imagesavealpha( $newImage, true );
                        //imagecolorallocate($newImage, 252, 251, 249);
			$redim=imagecopyresampled($newImage,$this->image,$xDest,$yDest,0,0,$calculatedW,$calculatedH,$dimensions[0],$dimensions[1]);
			if(!$redim){return false;}
                        //test en direct
                        //header ("Content-type: image/jpg");
                        //imagejpeg($newImage);exit;
                        
			$this->arrayImg[$nameInArray]=$newImage;
			
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
                        imagealphablending( $newImage, false );
                        imagesavealpha( $newImage, true );
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
		public function saveImg($nom,$nameInArray,$addextension=true)
		{
			$retour;
			switch($this->type)
			{
				case "jpeg":
					$nameTosave;
					if($addextension)
					{
						$nameTosave=$nom.".jpg";
					}
					else
					{
						$nameTosave=$nom;
					}
					if(@imagejpeg($this->arrayImg[$nameInArray],$nameTosave))
					{$this->savedImgUrl=$nameTosave;return true;}
					else{return false;}
					
				break;
				
				case "png":
					$nameTosave;
					if($addextension)
					{
						$nameTosave=$nom.".png";
					}
					else
					{
						$nameTosave=$nom;
					}
					if(@imagepng($this->arrayImg[$nameInArray],$nameTosave))
					{$this->savedImgUrl=$nameTosave;return true;}
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