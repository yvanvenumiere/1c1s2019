//DETECT A TOUCH DEVICE
var touchOrNot=new touchDeviceManager();
/////////////////////////////function
function touchDeviceManager()
{
	this.isTouchDevice;
	
	this.isSmartPhone;
	this.isTablet;
	
	this.isIphone;
	this.isAndroid;
		
		
	if ((navigator.userAgent.match(/iPhone/i)) || (navigator.userAgent.match(/Android/i)) || (navigator.userAgent.match(/Ipad/i)))
	{this.isTouchDevice=true;}
	
	if ((navigator.userAgent.match(/iPhone/i)) )
	{this.isIphone=true;}
	
	if ((navigator.userAgent.match(/Android/i)) )
	{this.isAndroid=true;}
	
}

//regex
stringCheck=function(typeVerif,string)
{
	var regMail=new RegExp("^[a-zA-Z0-9\-_]+[a-zA-Z0-9\.\-_]*@[a-zA-Z0-9\-_]+\.[a-zA-Z\.\-_]{1,}[a-zA-Z\-_]+","i");
	var regJs=new RegExp("<.+| +>.+| +</.+| +>","i");
	var retour;
	switch(typeVerif)
	{
		case "mail":
		retour=regMail.test(string);
		break;
		
		case "normal":
		presence=regJs.test(string);
		if(presence){retour=false;}else{retour=true;}
		break;
		
		case "obligatoire":
		presence=regJs.test(string);
		if(presence){retour=false;}else{retour=true;}
		if(string==""){retour=false;}else{retour=true;}
		break;
		
		default:retour=true;
	}
	return retour;
};


//ALLOW TO CONSOLE EVERYWHERE----
if (!window.console) console = {};
console.log = console.log || function(){};
console.warn = console.warn || function(){};
console.error = console.error || function(){};
console.info = console.info || function(){};
//FUNCTION THAT ALWAYS RETURN A NUMBER
function getInt(_entree,_fallBack)
{
	foo=isNaN(_entree);
	if(foo)
	{return _fallBack;}
	else
	{return _entree;}
}
//FUNCTION THAT CHECKS IF IT IS  A NUMBER
function isNumber(_entree)
{
	foo=isNaN(_entree);
	if(foo)
	{return false;}
	else
	{return true;}
}

//FUNCTION THAT RETURNS A JQUERY OBJECT 
function getJObject(_selectorType,_string)
{
	return $(_selectorType+_string);
}

//FUNCTION THAT CREATE A DOM ELEMENT VERY FAST
function speedElem(_type,_arrayAttributes,_arrayCssProperties)
{
	var retour=document.createElement(_type);
	
	for(i=0;i<_arrayAttributes.length;i++)
	{
		retour.setAttribute(_arrayAttributes[i][0],_arrayAttributes[i][1]);
		
	}
	for(i=0;i<_arrayCssProperties.length;i++)
	{
		$(retour).css(_arrayCssProperties[i][0],_arrayCssProperties[i][1]);
		
	}
	return retour;
}
//IN_ARRAY
function inArray(_array,_value)
{
	var l=_array.length;
	for(var i = 0; i < l; i++) 
	{
		if(_array[i] == _value) {return true;}
	}
	return false;
}


//FUNCTION THAT DROP A LIST OF ELEMENT IN AN ARRAY
function getArrayWithoutIndexes(_array,_arrayIndex,_destruction)
{
	var retour=new Array();
	for(var i=0;i<_array.length;i++)
	{
		if(!inArray(_arrayIndex,i)){retour.push(_array[i]);}
		else
		{_array[i]=null;}
	}
	return retour;
}

/*function cloneArray(_object)
{
	
	
	
	var i=0;
	var newObj;
	if(_object instanceof Array)
	{
		
	}
  var i=0;
  var newObj = (_array instanceof Array) ? [] : {};
  
  for (i in _array) {
    if (i == 'clone') continue;
    if (this[i] && typeof this[i] == "object") {
      newObj[i] = this[i].clone();
    } else newObj[i] = this[i]
  } return newObj;
}*/

/*Object.prototype.clone = function() {
  var i=0;
  var newObj = (this instanceof Array) ? [] : {};
  for (i in this) {
    if (i == 'clone') continue;
    if (this[i] && typeof this[i] == "object") {
      newObj[i] = this[i].clone();
    } else newObj[i] = this[i]
  } return newObj;
};*/
