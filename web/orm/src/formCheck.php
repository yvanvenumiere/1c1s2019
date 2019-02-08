<?php
/*
* (c) 2012 Yvan Vénumière <yvan.venumierer@gmail.com>
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/
			
/**
* formCheck is class that helps to valid forms or informations
*
*/
class formCheck
{
	
	public $autorisation=true;
	public $arrayTypeChecking=array("simple","mail","optional","int");
	public $arrayResult=array();
	
	
	/**
	* methods that add a new value to check to the class
	* @param string $checkName name of the value we check
	* @param string $valueToCheck the value we check
	* @param string $typeChecking type of checking
	*/
	public function addCheck($checkName,$valueToCheck,$typeChecking)
	{
		if(!in_array($typeChecking,$this->arrayTypeChecking))//if this typechecking doesn't exists
		{
			return false;//we can add it
		}
		else//else we empty the appropriate array
		{
			$this->arrayResult[$checkName]=array
			(
				"isValid"=>$this->checkOne($typeChecking,$valueToCheck)
			);
			
		}
	}
	
	
	/**
	* methods that checks a value according to a typechecking
	* @param string $typeChecking type of checking
    * @param string $value value to check
    * @return boolean returns a boolean that indicates if the call worked
	*/
	public function checkOne($typeChecking,$value)
	{
		
		//we  call the good function according to the typechecking
		switch($typeChecking)
		{
			case "simple":
			return $this->checkSimple($value);
			break;
			case "mail":
			return $this->checkMail($value);
			break;
			case "optional":
			return $this->checkOptional($value);
			break;
			case "int":
			return $this->checkInt($value);
			break;
			default:
			return $this->checkOptional($valeur_a_tester);
		}
	}
	
	/**
	* methods that returns the results of the current cheking
    * @return array arrayResults an array that contains every results for the current cheking
	*/
	public function getResults()
	{
		return $this->arrayResult;
	}
	
	
	/**
	* methods that indicates if the current checking is valid 
    * @return boolean that indicates if the cheking worked well
	*/
	public function isValid()
	{
		foreach($this->arrayResult as $key=>$value)
		{
			if(!$value['isValid'])
			{
				return false;
			}
		}
		return true;
	}

	
	/**
	* methods that checks if the $entree parameter is good
    * @return boolean that indicates if the cheking worked well
	*/
	public function checkSimple($entree)//entr�e simple
	{
		if(preg_match("#<.+| +>.+| +</.+| +>#i",$entree) OR preg_match("#<.+| +//+>#i",$entree) OR !$entree)
		{
			return false;
		}
		else
		{
			return true;
		}
	}
	/**
	* methods that checks if the $number parameter is good
    * @return boolean that indicates if the cheking worked well
	*/
	public function checkInt($number)
	{
		if(filter_var($number,FILTER_VALIDATE_INT))
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	
	/**
	* methods that checks if the $number parameter is good
    * @return boolean that indicates if the cheking worked well
	*/
	public function checkOptional($entree)
	{
		if(preg_match("#<.+| +>.+| +</.+| +>#i",$entree) OR preg_match("#<.+| +//+>#i",$entree))
		{return false;}
		else
		{return true;}
	}
	
	/**
	* methods that checks if the $mail parameter is good
    * @return boolean that indicates if the cheking worked well
	*/
	public function checkMail($mail)
	{
		if (preg_match("#^[a-z0-9._-]+@[a-z0-9._-]{2,}\.[a-z]{2,4}$#",$mail))
		{
			return true;
		}
		else
		{
			return false;
		}
	}
}