/**
 * This class creates a map
 * @class 
 * @constructor
 * @param _instanceName name of the instance.
 * @param _idMapDiv id of the div that contains the map.
 */
function easyGmap(_instanceName,_idMapDiv)
{
	///////////UTIL/////////
	/**
	* object that contains our instance
	* @type Object
	*/
	var current = this;
	//////////PROPERTIES/////
	/**
	* name of our instance
	* @type string
	*/
	this.instanceName=_instanceName;
	/**
	* id of the div that contains our map
	* @type string
	*/
	this.idMapDiv=_idMapDiv;
	/**
	* json object that contains the latitude and longitude our our marker<br/>
	* ex : foo={lat:"number for lat",lng:"number for lng"};
	* @type Object
	*/
	this.latLng;
	/**
	* zoomLevel of the map
	* @type int
	*/
	this.zoomLevel;
	/**
	* a hard reference to the map
	* @type Map
	*/
	this.map;
	/**
	* array that contains the customMarkers of this easyGmap
	* @type Array
	*/
	this.customMarkers=new Array();
	/**
	* array that contains the listeners on the map
	* @type Array
	*/
	this.arrayListeners=new Array();
	
	 /**
	 * number of markers on the map
	 * @type int
	 */
	 this.numMarkers;
	 
	
	/**
	* geocoder of this map
	* @type geocoder
	*/
	this.geocoder;
	
	/**
	* direction renderer of the map
	* @type DirectionsRenderer
	*/
	this.directionDisplayer;
	
	
	/**
	* direction service of the map
	* @type DirectionsService
	*/	
	this.servicePath
	
	/////////////FUNCTIONS//////
	/**
	 * function that init the map 
	 * @param _options json object that contain all parameters.
	 */
	this.init=function(_options)
	{
		//, 
		var options;////object that contains our parameters
		if (_options == null) 
		{
			options = {
						_latLng:{lat:49.237995,lng:-2.172490},
						_zoomLevel: 8,
                                                _realLatLng:'lesvrais'
					  };
		}
		else
		{
			options = _options;
		}
		//we affect our parameters
		this.latLng=options._latLng;
		this.zoomLevel=options._zoomLevel;
		
		//we create a mapOptions objetct(native to google map)
                if(options._realLatLng==false)
                {
                    
                    var mapOptions= 
                    {
                        zoom: this.zoomLevel,
                        center: new google.maps.LatLng(this.latLng.lat, this.latLng.lng),
                        mapTypeId: google.maps.MapTypeId.ROADMAP,
                        scrollwheel: false
                    };
                }
                else
                {
                    var mapOptions= 
                    {
                        zoom: this.zoomLevel,
                        center: options._realLatLng,
                        mapTypeId: google.maps.MapTypeId.ROADMAP,
                        scrollwheel: false
                    };
                }
		
		
		//we create a map objetct(native to google map)
		this.map=new google.maps.Map
		(
			document.getElementById(this.idMapDiv),
	        mapOptions
		);
	};
        
        this.setIdMap=function(idMap)
        {
            current.idMapDiv=idMap;
        };
        
	/**
	 * function that add a customMarker(object define by the class customMarker.js) to our map
	 * @param _customMarker customMarker object.
	 */
	this.addMarker=function(_customMarker)
	{
		//_customMarker.markerRef.setMap(this.map);
		_customMarker.setMap(this.map);
		this.customMarkers.push(_customMarker);
		this.numMarkers=this.customMarkers.length;
	};
	
	/**
	 * function that remove markers by their indexes or all the markers if no param
	 * @param array of indexes
	 */
	 this.removeMarkers=function(_arrayIndex)
	 {
	 	
	 	if(_arrayIndex)
		{
			//console.log("on veut effacer certains markers");
			//console.log("pour l'instant , il y a "+this.customMarkers.length+" markers");
			//console.log("on veut effacer ceux de cette plage");
			//console.log(_arrayIndex);
			
			
			/*var tmp=getArrayWithoutIndexes(this.customMarkers,_arrayIndex);
			console.log("on duplique");
			var tmp=this.customMarkers;
			this.customMarkers=null;
			console.log(tmp);*/
			
			//console.log("on veut donc se retrouver avec çà");
			//console.log(tmp);
			//console.log("donc ce nombre de markers");
			//console.log(tmp.length);
			//console.log("les marqueurs");
			//console.log(this.customMarkers);
			//console.log("les marqueurs après");
			//console.log(tmp);
			//return false;
			//return false;
			for(i=0;i<this.numMarkers;i++)
			{
				this.customMarkers[i].setVisibility(false);
				//this.customMarkers[i].dispose();
				//this.customMarkers[i]=null;
			}
			//return false;
			this.customMarkers=getArrayWithoutIndexes(this.customMarkers,_arrayIndex);
			//this.customMarkers=tmp;
			this.numMarkers=this.customMarkers.length;
			for(i=0;i<this.numMarkers;i++)
			{
				this.customMarkers[i].setVisibility(true);
			}
		}
		else
		{
			for(i=0;i<this.numMarkers;i++)
			{
				this.customMarkers[i].setVisibility(false);
                this.customMarkers[i].setMap(null);
				this.customMarkers[i]=null;


				//this.customMarkers[i].dispose();
				//this.customMarkers[i]=null
			}
			this.customMarkers=null;
			this.customMarkers=new Array();
			this.numMarkers=0;
		}
	 };
	
	/**
	 * function that hide a list of customMarkers or all the markers if no param
	 * @param _arrayIndices array of int
	 */
	this.hideMarkers=function(_arrayIndex) 
	{
		
		if(_arrayIndex)
		{
			for(var i=0;i<_arrayIndex.length;i++)
			{
				this.customMarkers[_arrayIndex[i]].setVisibility(false);
			}
		}
		else
		{
			for(i=0;i<this.numMarkers;i++)
			{
				this.customMarkers[i].setVisibility(false);
			}
		}
	};
	
	/**
	 * function that show a list of customMarkers or all the markers if no param
	 * @param _arrayIndices array of int
	 */
	this.showMarkers=function(_arrayIndex) 
	{
		
		if(_arrayIndex)
		{
			for(var i=0;i<_arrayIndex.length;i++)
			{
				this.customMarkers[_arrayIndex[i]].setVisibility(true);
			}
		}
		else
		{
			for(i=0;i<this.numMarkers;i++)
			{
				this.customMarkers[i].setVisibility(true);
			}
		}
	};
	
	/**
	 * function that returns the markers 
	 */
	this.getMarkers=function() 
	{
		return this.customMarkers;
	};
	
	/**
	 * function that returns the markers 
	 * @param _index int that represents the index of the marker
	 */
	this.getMarkerAtIndex=function(_index) 
	{
		return this.customMarkers[_index];
	};
	
	/**
	 * function that returns the zoomLevel of the map
	 * @return zoomLevel of the map
	 */
	this.getZoomLevel=function()
	{
		return this.map.getZoom();
	};
	
	/**
	 * function that set the zoomLevel of the map
	 * 
	 */
	this.setZoomLevel=function(_zoom)
	{
		this.map.setZoom(_zoom);
	};
	
	/**
	 * function that return the map
	 * @return the map
	 */
	this.getMap=function()
	{
		return this.map;
	};
	
	/**
	 * function that listen to an event on the map
	 * @param _eventName string that represent the name of the event
	 * @param _callBack a callback function to call when the event is trigerred
	 */
	this.addEventListener=function(_eventName,_callBack,_listenerName)
	{
		
		var elem={};
		elem["listenerName"]=_listenerName;
		elem["value"]=google.maps.event.addListener(this.map, _eventName, _callBack);		
		this.arrayListeners.push(elem);
	};
	
	/**
	 * function that remove an event listener an  on the map
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
	 * function that return the last marker added the map
	 */
	this.getLastAddedMarker=function(_listenerName)
	{
		return this.customMarkers[this.numMarkers-1];
		
	};
	
	/**
	 * function that returns the index of a custom marker 
	 * @param _marker customMarker with the unknow index
	 */
	this.getIndexOfMarker=function(_marker)
	{
		for(var i=0;i<this.numMarkers;i++)
		{
			if(this.customMarkers[i]==_marker)
			{
				return i;
			}
		}
		
	};


	/**
	 * function that places the map on the marker at the specified index
	 * @param _index int index of the marker
	 */
	this.setOnMarker=function(_index)
	{
		this.map.panTo(this.customMarkers[_index].getNativeLatLng());
	};
	
	/**
	 * function that geocode something
	 * @param _type string that represent the name of the event
	 * @param _customMarker customMarker object that we use to get the coors
	 */
	this.geocodeFromMarker=function(_customMarker)
	{
		//return this.customMarkers[this.numMarkers-1];
		if(this.geocoder==null)
		{this.geocoder = new google.maps.Geocoder();}
		
		if(_customMarker!=null)
		{
			this.geocoder.geocode({'latLng': new google.maps.LatLng(_customMarker.getLatitude(), _customMarker.getLongitude())}, function(results, status) 
			{
		      	if (status == google.maps.GeocoderStatus.OK) 
				{
			        _customMarker.setAdress(results[1].formatted_address);
		      	}
				else 
				{
		           _customMarker.setAdress("");
		      	}
	    	});
		}
	};
	
	/**
	 * function that geocode something
	 * @param _type string that represent the name of the event
	 * @param _customMarker customMarker object that we use to get the coors
	 */
	this.geocodeFromDatas=function(_type,_datas,callback,extraDatas)
	{
            
		//return this.customMarkers[this.numMarkers-1];
		if(this.geocoder==null)
		{this.geocoder = new google.maps.Geocoder();}
		
		if(_type=="latLng")
		{
			this.geocoder.geocode({'latLng': new google.maps.LatLng(_datas.lat,_datas.lng)}, function(results, status) 
			{
			    if (status == google.maps.GeocoderStatus.OK) 
				{
					_callback(results[1].formatted_address);
			    }
				else 
				{
			       _callback("");
			    }
				
		    });
		    
		    
		    
		    
		}
		
		if(_type=="pays")
		{
			this.geocoder.geocode({'address':'14 quai fernand saguet, 94700 Maisons Alfort'}, function(results, status) 
			{
				console.log(results);
				console.log(status);
			    if (status == google.maps.GeocoderStatus.OK) 
				{
					return results;
			    }
				else 
				{
			       return false;
			    }
				
		    });
		}
		
		if(_type=="adresse")
		{
                        var retour;
			this.geocoder.geocode({'address':_datas}, function(results, status) 
			{
				
			    if (status == google.maps.GeocoderStatus.OK) 
                            {
					//callback(results);
                                        callback({result:true,content:results,extra:extraDatas});
			    }
                            else 
                            {
                               
			       callback({result:false,extra:extraDatas});
			    }
				
                        });
		}
		
	};
	

	/**
	 * function that clears the path between some markers
	 */
	this.clearPath=function()
	{
		if(this.directionDisplayer!=null)
		{
			//this.directionDisplayer.setDirections(null);
			this.directionDisplayer.setMap(null);
		}
	};

	/**
	 * function that trace the path between some markers
	 * @param _arrayMarkers array of index
	 */
	this.tracePath=function(_arrayIndex,_withMarkers)
	{
		if(!this.directionDisplayer) 
		{
			this.directionDisplayer = new google.maps.DirectionsRenderer();
			this.servicePath =new google.maps.DirectionsService();
			
		}
		
		var params={};
		var request;
		var numPaths=_arrayIndex.length;
		if(numPaths<2)
		{return false;}
		//this.hideMarkers(_arrayIndex);
		var etapes;
		if(numPaths>2)
		{
			etapes=new Array();
			for(var i=0;i<numPaths;i++)
			{
				if(i>0 && i<numPaths-1)
				{
					etapes.push({
						            location:this.customMarkers[_arrayIndex[i]].getNativeLatLng(),
						            stopover:true
						       });
				}
			}
		}
		if(etapes!=null)
		{
			request= 
			{
			   origin:this.customMarkers[_arrayIndex[0]].getNativeLatLng(), 
			   destination:this.customMarkers[_arrayIndex[numPaths-1]].getNativeLatLng(),
			   travelMode: google.maps.DirectionsTravelMode.DRIVING,
			   waypoints: etapes,
	           optimizeWaypoints: true
			};
		}
		else
		{
			request= 
			{
			   origin:this.customMarkers[_arrayIndex[0]].getNativeLatLng(), 
			   destination:this.customMarkers[_arrayIndex[numPaths-1]].getNativeLatLng(),
			   travelMode: google.maps.DirectionsTravelMode.DRIVING
			};
		}
		
		if(this.directionDisplayer.getMap()==null)
		{this.directionDisplayer.setMap(this.map);}
		
		this.servicePath.route
		(
			request, 
			function(response, status) 
			{
		      	if (status == google.maps.DirectionsStatus.OK) 
		      	{
			        current.directionDisplayer.setDirections(response);
			        if(!_withMarkers)
			        {
			        	current.directionDisplayer.suppressMarkers=true;
			        }
			        //console.log(current.directionDisplayer.getDirections());
		      	}
		   	}
	    );
		
	};
	
}