function sliderLoadTemplate()
{
	var i;//
	var current = this;//objet encupsulant notre objet
	this.largeur;
	this.hauteur;
	this.initLargeur;
	this.initHauteur;
	this.urlPage;
	this.idConteneur;
	this.cssClasses;
	this.template;
	this.slideDuration;
	this.enableGlobalClick=function()
	{
		$(current.template).remove();
		current.template=null;
	}
	
	this.appaTemplate=function(_options)
	{
		var options;//object that contains all the parameters
		 if (_options == null) 
		 {
		 	options=
		 	{
		 		_urlPage:'something',
		 		_id:'something',
		 		_idShut:'something',
		 		_initHauteur:'something',
		 		_initLargeur:'something',
		 		_hauteur:'something',
		 		_largeur:'something',
				_positions:'something',
				_slideDuration:'something',
				_cssClasses:'something'
		 	};
		 }
		 else
		 {
		 	options=_options;
		 }
		
		this.urlPage=options._urlPage;
		this.idConteneur=options._id;
		this.idShut=options._idShut;
		
		if(options._initHauteur!=null)
		{this.initHauteur=options._initHauteur;}
		if(options._initLargeur!=null)
		{this.initLargeur=options._initLargeur;}

		
		
		if(options._largeur!=null)
		{this.largeur=options._largeur;}
		if(options._hauteur!=null)
		{this.hauteur=options._hauteur;}
		
		if(options._cssClasses)
		{this.cssClasses=options._cssClasses;}
		
		if(options._positions)
		{this.positions=options._positions;}//{initTop:0,initLeft:0,finalTop:23,finalLeft:432}
		this.slideDuration=options._slideDuration;
		
		///////////////////////////TEMPLATE/////////////////////
		//cr�ation du conteneur du gabarit
		this.template=null;
		this.template=document.createElement("div");
		this.template.setAttribute("id",this.idConteneur);
		document.body.appendChild(this.template);
		$(this.template).css("width",this.initLargeur+"px");
		$(this.template).css("height",this.initHauteur+"px");
		$(this.template).css("position","absolute");
		$(this.template).css("overflow","auto");
		$(this.template).addClass(this.cssClasses);
		if(this.positions)
		{
			$(this.template).css("top",this.positions.initTop+"px");
			$(this.template).css("left",this.positions.initLeft+"px");
		}
		else
		{
			$(this.template).css("top","0px");
			$(this.template).css("left","0px");
		}
		
		
		$(this.template).css("z-index","1000");
		//chargement du template
		$(this.template).load(
								current.urlPage,
								function(){
											$(current.template).children().css("opacity","0");
											var posX=0;
											var posY=0;
											if(current.positions)
											{posX=current.positions.finalLeft;posY=current.positions.finalTop;}
											
											var transformValue;
											if(touchOrNot.isIpad)
											{
												transformValue={height:current.hauteur+"px",width:current.largeur+"px",top:posY+"px",left:posX+"px",useTranslate3d:true};
											}
											else
											{
												transformValue={height:current.hauteur+"px",width:current.largeur+"px",top:posY+"px",left:posX+"px"};
											}
											$(this).animate
											(
													transformValue,
													{
														duration:current.slideDuration,
														complete:function()
														{$(current.template).children().animate({opacity:1},{duration:current.slideDuration});}
													}
													
											);
											$("#"+current.idShut+"").css("cursor","pointer");
											$("#"+current.idShut+"").click(function()
											{
												current.enableGlobalClick();
											}
											);
										  }
							  );	
	}
	
	this.dispaTemplate=function()
	{
		current.enableGlobalClick();
		/*if(current.template!=null)
		{
			$(current.template).children().animate({opacity:0},{duration:current.slideDuration});
			if(current.positions)
			{posX=current.positions.initLeft;posY=current.positions.initTop;}
			$(current.template).animate({height:current.initHauteur+"px",width:current.initLargeur+"px",top:posY+"px",left:posX+"px"},current.slideDuration);
			setTimeout(current.enableGlobalClick,current.slideDuration+100);
		}*/
		
	};
}