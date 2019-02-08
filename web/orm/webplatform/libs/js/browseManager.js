/** @constructor browseManager*/
function browseManager(_options)
{
var current=this;

////////////////////PROPERTIES/////////////////////////

 /**
 * div of the target id
 */
 this.targetId='something';
 
 
 /**
 * url of the server side file that handles saving
 */
 this.addManagerUrl='something';
 
 
 /**
 * name attribute of the browse button
 */
 this.inputFileName='something';
 
 /**
 * datas of the component
 */
 this.datas='something';
 
 
 /**
 * callback function that is called when the user choosed a file
 */
 this.onChoose='something';
 
 
 /**
 * callback function that is called when the user click on the button a file
 */
 this.onClick='something';
 
 
 /**
 * dom form that is used
 */
 this.form;
 
 /**
 * width of the component
 */
 this.width='something';
 
 /**
 * height of the component
 */
 this.height='something';
 
 /**
 * height of the component
 */
 this.expendHeight='something';
 
 
 /**
 * real button that handles file browsing
 */
 this.realBtBrowse='something';
 
 
 /**
 * uploadHandler object that submit the form
 */
 this.uploader='something';
 
 /**
 * plus width for the browse button
 */
 this.plusWidth='something';

 /**
 * id of the form
 */
 this.formId='something';
 
 /**
 * boolean that informs if the component is onBrowsing
 */
 this.isBrowsing=false; 
 
 
////////////////////METHODS/////////////////////////
/**
 * function that makes the layout
 */
 this.make=function()
 {
 	this.width=$('#'+this.targetId).width();
	this.height=$('#'+this.targetId).height();
	//$('#'+this.targetId).html('');
	
	
	
	
	var toto=document.createElement("form");
	var form=document.createElement("form");
	form.setAttribute("enctype","multipart/form-data");
	form.setAttribute("method","post");
	form.setAttribute("action",this.addManagerUrl);
	this.formId=this.targetId+"Form";
	form.setAttribute("id",this.formId);
	$(form).css("position","absolute");
	$(form).css("right","0px");
	$(form).css("top","0px");
	$(form).appendTo("#"+this.targetId);
	
	this.realBtBrowse=document.createElement("input");
	this.realBtBrowse.setAttribute("id",this.targetId+"FileInput");
	this.realBtBrowse.setAttribute("type","file");
	this.realBtBrowse.setAttribute("name",this.inputFileName);
	$(this.realBtBrowse).css("visibility","hidden");
	
	$(this.realBtBrowse).change(function(){$("#"+current.formId).submit();});
	$(this.realBtBrowse).appendTo("#"+this.formId);
	//console.log($(current.form).attr('id'));

	$("#"+this.targetId).click(function()
	{
		if(!current.isBrowsing)
		{
			current.onClick(current.datas);
			$("#"+current.targetId).animate({paddingRight:current.plusWidth});
			$(current.realBtBrowse).css("visibility","visible");
			$(current.realBtBrowse).css("opacity","0");
			$(current.realBtBrowse).animate({opacity:1});
			current.isBrowsing=true;
		}
		else
		{
			$("#"+current.targetId).animate({paddingRight:0});
			$(current.realBtBrowse).animate({opacity:1},{complete:function(){$(current.realBtBrowse).css("visibility","hidden");}});
			current.isBrowsing=false;
		}
		
	});
	
	this.uploader=new uploadHandler
	(
		{
			 _instanceName: this.targetId+"Uploader",
			 _callbackHandler:this.onChoose,
			 _idForm:this.formId
		}
	);
	
	
	
	
 };
 
/**
 * function that makes the layout
 */
 this.setBackgroundImage=function(url,expendHeight,link)
 {
 	$('#'+this.targetId).css('height',this.height+expendHeight);
 	$('#'+this.targetId).css('background-image',"url('"+url+"')");
 	$('#'+this.targetId).css('background-repeat',"no-repeat");
 	$('#'+this.targetId).css('background-position',"0px "+this.height+"px");
 	this.expendHeight=expendHeight;
 	$('#'+this.targetId).css('cursor','pointer');
 	$('#'+this.targetId).click(function()
 	{
 		document.location=link;
 	})
 	
 	//this.height=this.height+newHeight;
 	
 }
 
////////////////////AFFECTATION/////////////////////////
 var options;//objject that contains all the parameters
 if (_options == null) 
 {
 	options=
 	{
		_targetId:'something',
		_addManagerUrl:'something',
		_inputFileName:'something',
		_onClick:'something',
		_onChoose:'something',
		_datas:'something',
		_plusWidth:'something'
 	};
 }
 else
 {
 	options=_options;
 }
 this.targetId=options._targetId;
 this.addManagerUrl=options._addManagerUrl;
 this.inputFileName=options._inputFileName;
 this.onClick=options._onClick;
 this.onChoose=options._onChoose;
 this.datas=options._datas;
 this.plusWidth=options._plusWidth;
 
 this.make();

}