/**
 * Creates a new customMarker.
 * @constructor
 * @param _options json object that contain all parameters.
 */
function customMarker(_options)
{
	///////////UTIL/////////
	var i;
   /**
	* object that contains our instance
	*/
	var current = this;
	
	//////////PROPERTIES////
	
	/**
	* name of our instance
	* @type string
	*/
	this.instanceName;
	/**
	* json object that contains the latitude and longitude our our marker<br/>
	* ex : foo={lat:"number for lat",lng:"number for lng"};
	* @type Object
	*/
	this.latLng;
	/**
	* json object that contains datas on our custommarker<br/>
	* ex : foo={name:"toto",pics:["toto.jpg","toto2.jpg"};
	* @type Object
	*/
	this.datas;
	/**
	* title of our marker
	* @type string
	*/
	this.title;
	/**
	* title of our marker
	* @type Marker
	*/
	this.markerRef;
	/**
	* title of our marker
	* @type string
	*/
	this.iconLink;
	/**
	* title of our marker
	* @type Boolean
	*/
	this.toolTipHasBeenCreated=false;
	/**
	* title of our marker
	* @type infoWindow
	*/
	this.toolTip;
	
	/**
	* title of our marker
	* @type boolean
	*/
	this.isToolTipOpen=false;
	
	/**
	* finded adress of our marker
	* @type string
	*/
	this.adress;
	
	/**
	* array that contains the listeners on the marker
	* @type Array
	*/
	this.arrayListeners=new Array();
	
	/**
	* mapobject that contains e reference to the current map of this custom marker
	* @type mapObject
	*/
	this.currentMap;	
	/////////FUNCTIONS//////
	/**
	 * function that listen to an event on the marker
	 * @param _eventName string that represent the name of the event
	 * @param _callBack a callback function to call when the event is trigerred
	 */
	this.addEventListener=function(_eventName,_callBack,_listenerName)
	{
		var elem={};
		elem["listenerName"]=_listenerName;
		google.maps.event.addListener(this.markerRef, _eventName, function(){_callBack(current.datas)});
		this.arrayListeners.push(elem);
	};
	
	/**
	 * function that remove an event listener an  on the marker
	 * @param _eventName string that represent the name of the event
	 * @param _callBack a callback function to call when the event is trigerred
	 */
	this.removeEventListener=function(_listenerName)
	{
		
		var indice;
		var canRemove=false;
		for(i=0;i<this.arrayListeners.length;i++)
		{
			if(this.arrayListeners[i]["listenerName"]==_listenerName)
			{indice=i;canRemove=true;}
		}
		
		if(!canRemove)
		{
			return;
		}
		google.maps.event.removeListener(this.arrayListeners[indice].value);
		var save=getArrayWithoutIndexes(this.arrayListeners,indice);
		this.arrayListeners=null;
		this.arrayListeners=save;
	};
	
	/**
	 * function that return the latitude of the custommarker
	 * @return the latitude of the custommarker
	 */
	this.getLatitude=function()
	{
		return easyGmapUtils.convertLatLngToArrayCoors(current.markerRef.getPosition())[0];
	};
	
	/**
	 * function that return the longitude of the custommarker
	 * @return the longitude of the custommarker
	 */
	this.getLongitude=function()
	{
		return easyGmapUtils.convertLatLngToArrayCoors(current.markerRef.getPosition())[1];
	};
	
	/**
	 * function that return the real LatLng object of the custommarker
	 * @return LatLng
	 */
	this.getNativeLatLng=function()
	{
		return this.markerRef.getPosition();
	};
	
	
	/**
	 * function that return the adress of the custommarker
	 * @return the longitude of the custommarker
	 */
	this.getAdress=function()
	{
		return this.adress;
	};
	
	/**
	 * function that set the adress of the custommarker
	 * @param _adress string that represent the adress for this marker
	 */
	this.setAdress=function(_adress)
	{
		this.adress=_adress;
	};
	
	/**
	 * function that affects the datas propertie
	 * @param _datas json object that represents the datas
	 */
	this.setDatas=function(_datas)
	{
		this.datas=_datas;
	};
	
	/**
	 * function that affect a new propertie to the datas propertie ...
	 * @param _name string that represents the name of the propertie
	 * @param _value string that represents the value of the propertie
	 */
	this.setNewPropertie=function(_name,_value)
	{
		this.datas[_name]=_value;
	};
	
	/**
	 * function that changes the visibility of the custom marker ...
	 * @param _boolean that define the visibility og the marker
	 */
	this.setVisibility=function(_boolean)
	{
		this.markerRef.setVisible(_boolean);
	};
	
	/**
	 * function that set a map to this custom marker ...
	 * @param _mapObject google map object 
	 */
	this.setMap=function(_map)
	{
		this.markerRef.setMap(_map);
	};
	
	/**
	
	 * function that handle tooltip
	 * @param _nomPropData name of the json node of the datas object used as content provider of the tooltip
	 */
	this.manageToolTip=function(_nomPropData)
	{
		if(!this.toolTipHasBeenCreated)
		{
			this.toolTip= new google.maps.InfoWindow
			({
	    		content: this.datas[_nomPropData]
			});	
			this.toolTipHasBeenCreated=true;
		}
		else
		{
			this.toolTip.setContent(this.datas[_nomPropData]);
		}
		
		
		google.maps.event.addListener(this.markerRef, 'mouseover', function() 
		{
	  		current.toolTip.open(current.markerRef.getMap(),current.markerRef);
		});
		
		google.maps.event.addListener(this.markerRef, 'mouseout', function() 
		{
	  		current.toolTip.close();
		});

	};

	/**
	 * function that set a tooltip to the marker
	 * @param _content string that represent the content in text or html
	 */
	this.setToolTip=function(_content,_callbackReady)
	{
		//google.maps.event.addListener(this.markerRef, _eventName, _callBack);
		if(!this.toolTipHasBeenCreated)
		{
			this.toolTipHasBeenCreated=true;
			this.toolTip= new google.maps.InfoWindow
			({
	    		content: _content
			});	
			google.maps.event.addDomListener(this.toolTip, 'closeclick', current.onCloseClickToolTip);
			
		}
		else
		{
			this.toolTip.setContent(_content);
		}
		this.toolTip.open(current.markerRef.getMap(),current.markerRef);
		this.isToolTipOpen=true;
		if(_callbackReady)
		{
			google.maps.event.addDomListener(this.toolTip, 'domready', _callbackReady);
		}
		
	};
	
	/**
	 * function that set is called when closing the tooltip
	 */
	this.onCloseClickToolTip=function()
	{
		this.isToolTipOpen=false;
		
	};
	
	/**
	 * function that close the current tooltip of the marker
	 */
	this.closeToolTip=function(clear)
	{

		if(this.toolTipHasBeenCreated && this.isToolTipOpen)
		{
			//if(clear)
			//{this.toolTip.setContent("");}
			//console.log(this.toolTip);
			this.toolTip.close();
		}
	};
	
	/**
	 * function dispose the customMarker
	 */
	this.dispose=function()
	{

		this.markerRef.setMap(null);
		this.markerRef=null;
	};
	
	/////////AFFECTATION
	var options;////object that contains our parameters
	if (_options == null) 
	{
		options = {
					_instanceName:"instance's name",
					_latLng:{lat:"number for lat",lng:"number for lng"},
                                        _objectLatLng:'object',
					_title:"titre marker",
					_datas:"json de datas",
					_iconLink:"lien image",
				  };
	}
	else
	{
		options = _options;
	}
	this.instanceName=options._instanceName;
	//latLng
	this.latLng=options._latLng;
	//title
	if(options._title)
	{this.title=options._title;}
	else{this.title="";}
	//datas
	if(options._datas)
	{this.datas=options._datas;}
	//icon
	if(options._iconLink)
	{this.iconLink=options._iconLink;}
        
        //objectLatLng
	if(options._objectLatLng)
	{this.objectLatLng=options._objectLatLng;}
        
        if(options._objectLatLng)
	{
            this.markerRef= new google.maps.Marker
            (
                    {
                            position: this.objectLatLng,
                            title:this.title,
                            datas:this.datas,
                            icon:this.iconLink
                    }
            );
        }
        else
        {
            //creation of the marker
            this.markerRef= new google.maps.Marker
            (
                    {
                        position: new google.maps.LatLng(this.latLng.lat,this.latLng.lng),
                            title:this.title,
                            datas:this.datas,
                            icon:this.iconLink
                    }
            );
        }
	
	
	
}
