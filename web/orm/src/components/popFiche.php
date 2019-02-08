<?php
ini_set ( "display_errors" , "1" );
session_start();
if(!isset($_SESSION['connected']))
{
	 header('Location: auth.php'); 
}
//we extract the get parameters
extract($_GET);

//import of the differents files
include('../libs/php/globalInfos.php');
$gb=new globalInfos();
$gb->requireModel($table);//the model for the current page
$myTools=new myTools();
include('../libs/php/inclusion_'.$table.'.php');//file that instanciate the differents models
$globalInfosClass=new globalInfos();
$gb->setGlobalXmlConfig(simplexml_load_file("../config/global_config.xml"));
//this variable contains the title for the form ( edit or add )
$titre=($mode=="edit"?$gb->getGlobalInfoForNodeAndTable("global_edit_title_label",$table):$gb->getGlobalInfoForNodeAndTable("global_add_title_label",$table));

//if we are in edit mode , we init the model with the saved values
if($mode=="edit")
{
	$oneElement->initFromDatas(array($datasManager->getPrimaryKey()=>$id));
}
else 
{
	$id='aucun';
	/*if(isset($_SESSION['hierarchy']["filter"]) && count($_SESSION['hierarchy']["filter"])>0)
	{
		$initDatas=array();
		foreach($_SESSION['hierarchy']["filter"] as $filter=>$value)
		{
			$initDatas[$filter]=$value;
		}
		$oneElement->initWithDatas($initDatas);
		
	}*/
}

?>	
<form class="width940 mAuto relative"> 
		<legend><?php echo $titre; ?></legend>
		<?php
		//we make a loop on the instance of the model and display the differents fields
		$arrayOrder=null;
		$xmlTable=$oneElement->getXmlConfig();
		if(strlen((string)$xmlTable['formOrder'])>0)
		{
			$arrayOrder=explode(",",(string)$xmlTable['formOrder']);
		}
		foreach($oneElement->getFields($arrayOrder) as $key=>$value)
		{
			$invisibleFields=array();
			if(isset($_SESSION['hierarchy']["filter"]) && count($_SESSION['hierarchy']["filter"])>0)
			{
				foreach($_SESSION['hierarchy']["filter"] as $filter=>$valueFilter)
				{
					$invisibleFields[]=$filter;
				}				
			}
			if($value["isVisibleInForm"]=="true" && !in_array($key, $invisibleFields))
			{
				echo $oneElement->getFormFromField($key,"ficheContext");
			}
		}
		
		?>
		<br/>
		<button type="submit" class="btn" id="saveDatas"><?php echo $gb->getGlobalInfoForNodeAndTable("global_save_label",$table); ?></button><!-- save button -->
		<button type="button" class="btn" id="cancelSaving"><?php echo $gb->getGlobalInfoForNodeAndTable("global_cancel_label",$table); ?></button>
</form>
<!-- js part -->
<script type="text/javascript"> 
	
	
	var lastIdFileField;//variable that contains the name of the column ( in database ) concerned by the last upload
	var fileFields=$(".uploadField");//array that contains dom references to the upload fields
	var arrayBrowseFields=new Array();// array that contains objects linked to the file fields, they are the fileManagers
	var mode="<?php echo $mode; ?>";//variable that contains the mode ( edit or add )
	
	//we make a loop on the file fields
	for(var i=0;i<fileFields.length;i++)
	{
		//we instanciate the differents file managers
		arrayBrowseFields[i]=new browseManager
		({
			_targetId:$(fileFields[i]).attr('id'),
			_addManagerUrl:'services/tempFile.php',
			_inputFileName:'tempFile',
			_onClick:clickBrowse,
			_onChoose:clickChoose,
			_datas:{idField:$(fileFields[i]).attr('id').replace("browse","")},
			_plusWidth:400
		});
		
		
		var inputsFromBrowsClass=$("#"+$(fileFields[i]).attr('id')+" input");//we keep a reference to the differents input files that are linked to file fields in an array
		
		//we make a loop on this array
		for(var u=0;u<inputsFromBrowsClass.length;u++)
		{
			if($(inputsFromBrowsClass[u]).attr('type')=="hidden")//if the input is hidden ( use to recognize )
			{
				if(mode=="edit" && $(inputsFromBrowsClass[u]).attr('value')!="" && $(fileFields[i]).hasClass('pictureField'))//if we are in edit mode and there is a picture associated with it
				{
					//we make what we got to do in a closure function
					( function() { 
						//we launch an ajax call to get the dimensions of the picture
						$.ajax({
							type: 'POST',
							url: 'services/getImageSize.php?tmp='+new Date().getTime(),
							data: {id:'<?php echo $id; ?>',column:$(inputsFromBrowsClass[u]).attr('id').replace("_s_ficheContext",""),table:'<?php echo $table; ?>'},
							success: function(retour)
							{ 
								if(retour.result=="ok")
								{
									//now we got the size , so we can put a background image to our picture manager 
									imgBrowser.setBackgroundImage(retour.path,retour.height);
								}
							},
							dataType: 'json'
						});	
					} ) ();
				}
				
				if(mode=="edit" && $(inputsFromBrowsClass[u]).attr('value')!="" && $(fileFields[i]).hasClass('fileField'))
				{
					
					//we make what we got to do in a closure function
					( function() { 
						var imgBrowser=arrayBrowseFields[i];//reference to the object ( fileManager )
						
						//we launch an ajax call to get the dimensions of the picture
						$.ajax({
							type: 'POST',
							url: 'services/getFileLink.php?tmp='+new Date().getTime(),
							data: {id:'<?php echo $id; ?>',column:$(inputsFromBrowsClass[u]).attr('id').replace("_s_ficheContext",""),table:'<?php echo $table; ?>'},
							success: function(retour)
							{ 
								if(retour.result=="ok")
								{
									//now we got the size , so we can put a background image to our picture manager 
									imgBrowser.setBackgroundImage("medias/images/icone_dl.png",200,retour.path);
								}
							},
							dataType: 'json'
						});	
						
						//we launch an ajax call to get the dimensions of the picture
						
						
					
					} ) ();
				}
			}
			
			
		}
	}
	
	//we apply a rich text editor on all the items that have this class =>rteClass
	$('.rteClass').tinymce({
			// Location of TinyMCE script
			script_url : 'libs/js/tiny_mce.js',

			// General options
			theme : "advanced",
			//plugins : "autolink,lists,pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template,advlist",

			// Theme options
			/*theme_advanced_buttons1 : "save,newdocument,|,bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,styleselect,formatselect,fontselect,fontsizeselect",
			theme_advanced_buttons2 : "cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,image,cleanup,help,code,|,insertdate,inserttime,preview,|,forecolor,backcolor",
			theme_advanced_buttons3 : "tablecontrols,|,hr,removeformat,visualaid,|,sub,sup,|,charmap,emotions,iespell,media,advhr,|,print,|,ltr,rtl,|,fullscreen",
			theme_advanced_buttons4 : "insertlayer,moveforward,movebackward,absolute,|,styleprops,|,cite,abbr,acronym,del,ins,attribs,|,visualchars,nonbreaking,template,pagebreak",
			theme_advanced_toolbar_location : "top",
			theme_advanced_toolbar_align : "left",
			theme_advanced_statusbar_location : "bottom",
			theme_advanced_resizing : true,*/

			// Example content CSS (should be your site CSS)
			content_css : "css/contentTiny.css",

			// Drop lists for link/image/media/template dialogs
			template_external_list_url : "lists/template_list.js",
			external_link_list_url : "lists/link_list.js",
			external_image_list_url : "lists/image_list.js",
			media_external_list_url : "lists/media_list.js"

			// Replace values for the template plugin
			/*template_replace_values : {
				username : "Some User",
				staffid : "991234"
			}*/
	});
	
	//we apply a datepicker on all the items that have this class =>pickerClass
	$(".pickerClass").datepicker({ dateFormat: "<?php echo $gb->getGlobalInfoForNodeAndTable("global_date_format",$table); ?>" });
	//$()
	
	//we add an event to de save button
	$("#saveDatas").click(function(event)
	{
		event.preventDefault();//...
		
		//this variable will contain all the parameters for the ajaxcall
		var postDatas;
		//we keep a reference to the different formfields in an array
		champs=$(".ficheContext");
		
		//we begin to empty the postDatas variable
		if(mode=="edit")
		{
			postDatas={mode:'edit',table:'<?php echo $table; ?>',id:'<?php echo $id; ?>',params:{}};
		}
		else
		{
			postDatas={mode:'add',table:'<?php echo $table; ?>',params:{}};
		}
		
		//we make a loop on the differents form fields
		for(var i=0;i<champs.length;i++)
		{
			//firstable
			var str=$(champs[i]).attr('id');
			var theField=str.replace("_s_ficheContext","");
			postDatas.params[theField]=$(champs[i]).val();
			
			//we replace the datas fot specifics kind of formfields
			
			//check box
			/*if($(champs[i]).attr('type')=="checkbox")
			{
				if($(champs[i]).attr("checked")=="checked")
				{
					postDatas.params[theField]=1;
				}
				else
				{
					postDatas.params[theField]=0;
				}
			}*/
			
			//rich text editor
			if($(champs[i]).hasClass('rteClass'))
			{
				postDatas.params[theField]=$(champs[i]).html();
			}
			
			//date field; picker class
			if($(champs[i]).hasClass('pickerClass'))
			{
				postDatas.params[theField]=getTimeFromFormatedDate("<?php echo $gb->getGlobalInfoForNodeAndTable("global_date_format",$table); ?>","-",$(champs[i]).val());
			}
		}
		
		//now we can launch the ajax call
		$.ajax({
			type: 'POST',
			url: 'services/saveDatas.php',
			data: postDatas,
			success: function(retour)
			{ 
				//if the return is OK 
				if(retour.result=="ok")
				{
					$('.ficheContext').removeClass('errorField');//we erase the error styles if exist
					refresh();//we refresh the datas of the main datagrid
					popupManager.dispaTemplate();//we close the popup
				}
				else//else
				{
					//we create a variable that will contains the messages
					var messageRetour="";
					messageRetour+=retour.message+"\n";
					if(retour.errors.pictureSaving)
					{
						messageRetour+="<?php echo $gb->getGlobalInfoForNodeAndTable("global_error_image_label",$table); ?>\n";
					}
					if(retour.errors.fileSaving)
					{
						messageRetour+="<?php echo $gb->getGlobalInfoForNodeAndTable("global_error_file_label",$table); ?>\n";
					}
					
					//we display some error classes on the fields
					if(retour.errors)
					{
						if(retour.errors.fields)
						{
							messageRetour+="<?php echo $gb->getGlobalInfoForNodeAndTable("global_error_fields_label",$table); ?>\n";
							for(var fieldError in retour.errors.fields)
							{
								$("#"+fieldError+"_s_ficheContext").addClass('errorField');
							}
						}
					}
					
					//we alert the message
					alert(messageRetour);
				}
			},
			dataType: 'json'
		});	
	});
	
	
	//function that empty a hidden field relative to an upload
	function clickChoose(datas)
	{
		if(datas.result=="ko")
		{
			alert(datas.message);
		}
		else
		{
			$("#"+lastIdFileField).val(datas.fileName);
		}
	}
	
	//function that empty the lastIdFileField variable
	function clickBrowse(datas)
	{
		lastIdFileField=datas.idField+"_s_ficheContext";
	}
</script>
