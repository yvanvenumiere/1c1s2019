<?php

namespace AlectisLab\UtilsBundle\Helpers;




class Formcheck
{
	//bool�an qui indique la validit� de l'ensemble des donn�es du formulaire
	public $autorisation=true;
	public $arrayTypeChecking=array("simple","zipcode","mail","facultatif","nombre","double","zipcode_fr");
	public $arrayResult=array();

	
	public function addCheck($checkName,$valueToCheck,$typeChecking)
	{
		if(!in_array($typeChecking,$this->arrayTypeChecking))//si le champs qu'on veut ajouter est d�j� de la classe et si le degr� de v�rification n'existe pas.
		{
			return false;//on ne peut ajouter de champs
		}
		else//sinon
		{
			$this->arrayResult[$checkName]=array
			(
				"isValid"=>$this->checkOne($typeChecking,$valueToCheck)
			);
				
		}
	}
	
	
	//cette fonction v�rifie un champs selon le type de traitement et de la valeur � tester pass�e en param�tre
	public function checkOne($typeChecking,$value)
	{
		switch($typeChecking)//selon le type de traitement pass� en param�tre,on applique les diff�rentes m�thodes de la classe
		{
			case "simple":
				return $this->verif_simple_entree($value);
				break;
                            
                        case "just_html":
				return $this->verif_just_html($value);
				break;
			case "zipcode":
				return $this->verif_zipcode($value);
				break;
			case "mail":
				return $this->verif_mail($value);
				break;
			case "facultatif":
				return $this->verif_pas_obli($value);
				break;
			case "nombre":
				return $this->isInt($value);
				break;
                        case "double":
				return $this->isDouble($value);
				break;
                        case "zipcode_fr":
				return $this->isZipcodeFr($value);
				break;
			default:
				return $this->verif_simple_entree($valeur_a_tester);
		}
	}
	
	
	public function getResults()
	{
		return $this->arrayResult;
	}
	
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
	
	
	//voil� les diff�rentes fonctions de v�rification du script
	////////////////////////////////////////////////////////FONCTIONS DE VERIFICATION//////////////////////////////////////////////////////////////////
	///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	public function verif_simple_entree($entree)//entr�e simple
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
        
        public function verif_just_html($entree)//entr�e simple
	{
		if(!$entree)
		{
			return false;
		}
		else
		{
			return true;
		}
	}
	
	public function isInt($number)//chiffre
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
        
        public function isZipcodeFr($number)//chiffre
	{
		if(filter_var((int)$number,FILTER_VALIDATE_INT))
		{
                    if(strlen($number)==5 || strlen($number)==4)
                    {
                        return true;
                    }
                    else
                    {
                        return false;
                    }
		}
		else
		{
                    
			return false;
		}
	}
        
        public function isDouble($number)//decimal
	{
		if(filter_var($number,FILTER_VALIDATE_FLOAT))
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	
	public function verif_pas_obli($entree)//entr�e simple facultative
	{
		if(preg_match("#<.+| +>.+| +</.+| +>#i",$entree) OR preg_match("#<.+| +//+>#i",$entree))
		{
			return false;
		}
		else
		{return true;
		}
	}
	public function verif_mail($mail)//adresse e-mail
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
	public function verif_zipcode($entree)//code postal
	{
		if (preg_match("#^[0-9]{5}$#",$entree) || preg_match("#^[0-9]{4}$#",$entree))
		{
			return true;
		}
		else
		{return false;
		}
	}
	////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////FIN DES FONCTIONS DE VERIFICATION//////////////////////////////////////////////////
}
