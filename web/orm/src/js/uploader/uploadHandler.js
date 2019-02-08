/*
* Classe that create an uploadHandler
* @param _options json object that contains options on the instance<br/>
* ex : foo=
* {
* _instanceName: "instanceName",
* _callbackHandler:"a function that handler the response"
* _idForm:"idForm"
* };
*
*/ 
function uploadHandler(_options)
{
	//util
	var current = this; 
	
	//////////PROPERTIES///////////
	/*
	 * instance's name
	 */
	this.instanceName;
	
	/*
	 * callbackhandler
	 */
	this.callbackHandler;
	
	/*
	 * id of the form
	 */
	this.idForm;
	
	///////////AFFECTATION/////////
	var options;//objet contenant les param�tres
	if (_options == null)
	{
	alert("parameter _options cannot be null");
	}
	else//sinon on affecte avec l'objet pass� en param�tre
	{
	options = _options;
	} 
	
	this.instanceName=options._instanceName;
	this.callbackHandler=options._callbackHandler;
	this.idForm=options._idForm;
	var options2 = 
	{ 
			target: '#output'  ,
			success:function(data)
									{
										current.callbackHandler(data);
									},
			dataType:'json' 
	}; 
	
    $("#"+this.idForm+"").submit(function() 
    { 
        $(this).ajaxSubmit(options2); 
        return false; 
    });
	
	
}