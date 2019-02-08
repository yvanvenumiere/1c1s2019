<?php
//import of differents files
if(!isset($_SESSION['connected']))
{
	 header('Location: auth.php'); 
}

if(file_exists("components/custom/".$page.".php"))
{
	require_once("components/custom/".$page.".php");
}
else 
{
	$globalInfosClass=new globalInfos();
	$globalInfosClass->requireModel($page);//the model
	include('libs/php/inclusion_'.$page.'.php');//the file that instanciate the model, creates $datasManager and $oneElement
	$xml=$oneElement->getXmlConfig();
	
	$myTools=new myTools();
	
	$globalInfosClass->setGlobalXmlConfig(simplexml_load_file("config/global_config.xml"));
	$globalInfosClass->setGlobalXmlHierarchy(simplexml_load_file("config/hierarchy.xml"));
	$hierarchyInfos=$globalInfosClass->getHierarchyInfos($page,(isset($_SESSION['hierarchy'])?$_SESSION['hierarchy']:false));
	//we get the session if  exist, and turn it into a json object
	if(isset($_SESSION['searchFilters'][$page]))
	{
		$sessionJson=json_encode($_SESSION['searchFilters'][$page]);
	}
	?>
	<!-- container -->
	
	<div class="container" id="content">
		  <div id="ariane"><?php if($hierarchyInfos['ariane']){echo $hierarchyInfos['ariane'];} ?></div>
	      <div class="hero-unit" id="gridContainer">
	        <button id="addElement" class="btn"><?php echo $globalInfosClass->getGlobalInfoForNodeAndTable("global_add_label",$page); ?></button>
	        <h4 id="filterBt" class="clickable"><?php echo $globalInfosClass->getGlobalInfoForNodeAndTable("global_filter_label",$page); ?><img src="medias/images/rouage.png"/></h4>
	        <form id="filterForm"> 
				
				<?php
				//we make a loop on the result of the 'getFields' method of the instance of the model and display the differents fields
				foreach($oneElement->getFields() as $key=>$value)
				{
					if($value["isFilterVisible"]=="true" && $value["isPictureField"]=="false" && $value["isFileField"]=="false" && $value["isRichTextEditorField"]=="false" && $value["isDateField"]=="false")
					{
						//we get the form with the right context
						echo $oneElement->getFormFromField($key,"filterContext");
					}
				}
				
				?>
				<br/>
				<button type="submit" class="btn" id="filterDatas"><?php echo $globalInfosClass->getGlobalInfoForNodeAndTable("global_search_label",$page); ?></button><!-- search button -->
				
			</form>
	        
	        <div id="gridDiv"></div><!-- container of the datagrid -->
	        
	        	<!-- container for the pagination -->
	            <div class="pagination">
				    <ul id="ulPagination">
				    <?php 
				    	for($i=0;$i<$datasManager->totalNumPages;$i++)
						{
							?>
								<li class="paginationButton <?php if($i==0){echo "active";} ?>" ><a href="#"><?php echo $i+1; ?></a></li>
							<?php
						}
				    ?>
				   
				    
				    </ul>
			    </div>
			    <!-- container for the pagination -->
			    
	      </div>
	</div>
	<!-- //container -->
	
	<!-- js part -->
	<script type="text/javascript"> 
		//hierarchy infos...
		var hierarchyObject=<?php echo json_encode($hierarchyInfos) ?>;
		//we define a variable that will stock the session datas
		var sessionJson;
		//we define a variable that will stock the filters visibility
		var filterState=false;
		
		//we empty or not the sessionJson variable
		<?php
			if(isset($sessionJson))
			{
				?>
				sessionJson=<?php echo $sessionJson ?>;
				<?php
			}
		?>
		
		//if the session datas are empty, we display the right informations, the id of the fields match to the key of the sessionJson object
		if(sessionJson)
		{
			for(var name in sessionJson)
			{
				$('#'+name).val(sessionJson[name]);
			}
		}
		
		var editableGrid = null;//variable that will stock the datagrid object
		var popupManager;//variable that will stock the popup object
		var currentPageIndex=1;//we initialize the index for the pagination at 1
		var globalConfig;
		
	
		$(document).ready(function()
		{
			
			$("#filterForm").hide();//we hide the filter form to begin and place a little toggle function on it
			$("#filterBt").click(function()
			{
				if(filterState==false)
				{
					filterState=true;
					$("#filterForm").show();
				}
				else
				{
					filterState=false;
					$("#filterForm").hide();
				}
			});
			
			//we put the margin top of the content at the height of the header
			var topContent=$("#headerDiv").height()+10;
			$("#content").css("margin-top",+topContent+"px");
			
			//instanciations
			popupManager=new sliderLoadTemplate();
			editableGrid = new EditableGrid("grid", 
			{
				
				// called when the XML has been fully loaded 
				tableLoaded: function() 
				{ 
					this.setCellRenderer("action", new CellRenderer({render: function(cell, value) {
						
						
		
								 
					    var htmlCell="";
					    
					    if(hierarchyObject.childsTable)
					    {
					    	for(var i=0;i<hierarchyObject.childsTable.length;i++)
					    	{
					    		htmlCell+="<a onclick=\" goToHierarchy(" + cell.rowIndex + ",'"+hierarchyObject.childsTable[i].tableName+"');\" style=\"cursor:pointer\">" +
										 "<img src=\""+hierarchyObject.childsTable[i].icon+"\" border=\"0\" alt=\""+hierarchyObject.childsTable[i].prompt+"\" title=\""+hierarchyObject.childsTable[i].prompt+"\"/></a>";
					    	}
					    }
					    htmlCell+="<a onclick=\" edit(" + cell.rowIndex + ");\" style=\"cursor:pointer\">" +
						 "<img src=\"medias/images/icone_crayon.png\" border=\"0\" alt=\"<?php echo $globalInfosClass->getGlobalInfoForNodeAndTable("global_edit_label",$page); ?>\" title=\"<?php echo $globalInfosClass->getGlobalInfoForNodeAndTable("global_edit_label",$page); ?>\"/></a>";
						htmlCell+="<a onclick=\"if (confirm('<?php echo $globalInfosClass->getGlobalInfoForNodeAndTable("global_delete_confirm_label",$page); ?> ')){deleteRow(" + cell.rowIndex + ");}\" style=\"cursor:pointer\">" +
						 "<img src=\"medias/images/icone_supprimer.png\" border=\"0\" alt=\"<?php echo $globalInfosClass->getGlobalInfoForNodeAndTable("global_delete_label",$page); ?>\" title=\"<?php echo $globalInfosClass->getGlobalInfoForNodeAndTable("global_delete_label",$page); ?>\"/></a>";
					    cell.innerHTML=htmlCell;
						
					}})); 
					
					//we strip the table for a better lisibility
					editableGrid.renderGrid("gridDiv", "table-striped myTab","myGrid");
					
					hidePK();
					hidePK();
					//setInterval('hidePK()',100);
				}
			
			});
		
			// load XML file
			refresh();
			
			
			//évènement on the add button
			$("#addElement").click(function()
			{
				//we define the different dimensions and positions of the popup
				largeur=$(window).width();
				hauteur=$(document).height()-$("#menuDiv").height();
				initTop=$(window).height()/2;
				initLeft=$(window).width()/2;
				finalTop=$("#menuDiv").height();
				finalLeft=($(window).width()/2)-(largeur*0.5);
				
				//we make the popup appear
				popupManager.appaTemplate
				({
						_urlPage:'components/popFiche.php?mode=add&table=<?php echo $page; ?>',
				 		_id:'popupFiche',
				 		_idShut:'cancelSaving',
				 		_initHauteur:0,
				 		_initLargeur:0,
				 		_hauteur:hauteur,
				 		_largeur:largeur,
						_positions:{initTop:initTop,initLeft:initLeft,finalTop:finalTop,finalLeft:finalLeft},
						_slideDuration:300,
						_cssClasses:''
				});
				$(window).scrollTo(0,200);
			});
			
			//event on the search button
			$("#filterDatas").click(function(event)
			{
				event.preventDefault();//...
				//this variable will contain all the parameters for the ajaxcall
				var postDatasToSearch={};
				postDatasToSearch={table:'<?php echo $page; ?>',params:{}};
				
				champs=$(".filterContext");
				
				for(var i=0;i<champs.length;i++)
				{
					//firstable
					var str=$(champs[i]).attr('id');
					var theField=str.replace("_s_filterContext","");
					postDatasToSearch.params[theField]=$(champs[i]).val();
					//we replace the datas fot specifics kind of formfields
				
					//check box
					/*if($(champs[i]).attr('type')=="checkbox")
					{
						if($(champs[i]).attr("checked")=="checked")
						{
							postDatasToSearch.params[theField]=1;
						}
						else
						{
							postDatasToSearch.params[theField]=0;
						}
					}*/
				}
				
				//now we can launch the ajax call
				$.ajax({
					type: 'POST',
					url: 'services/setSearchFilters.php',
					data: postDatasToSearch,
					success: function(retour)
					{ 
						//if the return is OK 
						if(retour.result=="ok")
						{
							//we refresh the grid
							refresh();
						}
						else//else
						{
						
						}
					},
					dataType: 'json'
				});	
				
				
			});
		});
		//function that hides primary keys
		function hidePK()
		{
			$.ajax({
					type: 'POST',
					url: 'services/getRealColumnVisibility.php',
					data: {table:'<?php echo $page ?>'},
					success: function(retour)
					{ 
						
						if(retour.need=="yes")
						{
							th=$('#myGrid th');
							for(var i=0;i<th.length;i++)
							{
								if($(th[i]).index()==retour.index)
								{
									$(th[i]).css('display','none');
								}
							}
							td=$('#myGrid td');
							for(var i=0;i<td.length;i++)
							{
								if($(td[i]).index()==retour.index)
								{
									$(td[i]).css('display','none');
								}
							}
						}
						
					},
					dataType: 'json'
				});	
		}
		
		//function that allows to delete a row
		function deleteRow(index)
		{
	
			var datas=editableGrid.getRowValues(index);//datas at this index ( json object )
			idElem=datas["<?php echo $datasManager->getPrimaryKey(); ?>"]; //we can have the id of the element ( the first primary key field )
			
			//we lauch an ajax call
			$.ajax({
				type: 'POST',
				url: 'services/delete.php',
				data: {table:'<?php echo $page; ?>',id:idElem},
				success: function(retour)
				{ 
					if(retour.result=="ok")
					{
						//we refresh the grid
						refresh();
					}
					else
					{
						alert(retour.message);
					}
					
				},
				dataType: 'json'
			});	
		}
		
		
		//function that allows to go to a child table
		function goToHierarchy(index,table)
		{
			var datas=editableGrid.getRowValues(index);//datas at this index ( json object )
			idElem=datas["<?php echo $datasManager->getPrimaryKey(); ?>"];//we can have the id of the element ( the first primary key field )	
			var link="router.php?childClass="+table+"&parentClass=<?php echo $page; ?>&idParent="+idElem;
			document.location.href=link;
		}
			
		
		//function that allows the edition of a row
		function edit(index)
		{
			var datas=editableGrid.getRowValues(index);//datas at this index ( json object )
			idElem=datas["<?php echo $datasManager->getPrimaryKey(); ?>"];//we can have the id of the element ( the first primary key field )
			
			//we define the different dimensions and positions of the popup
			largeur=$(window).width();
			hauteur=$(document).height()-$("#menuDiv").height();
			initTop=$(window).height()/2;
			initLeft=$(window).width()/2;
			finalTop=$("#menuDiv").height();
			finalLeft=($(window).width()/2)-(largeur*0.5);
			
			//we make the popup appear
			popupManager.appaTemplate
			({
					_urlPage:'components/popFiche.php?mode=edit&id='+idElem+'&table=<?php echo $page; ?>',
			 		_id:'popupFiche',
			 		_idShut:'cancelSaving',
			 		_initHauteur:0,
			 		_initLargeur:0,
			 		_hauteur:hauteur,
			 		_largeur:largeur,
					_positions:{initTop:initTop,initLeft:initLeft,finalTop:finalTop,finalLeft:finalLeft},
					_slideDuration:300,
					_cssClasses:''
			});
			$(window).scrollTo(0,200);
			
		}
		
		//function that refresh the datagrid with the last infos
		function refresh()
		{
			editableGrid.loadXML("services/listDatas.php?table=<?php echo $page; ?>&page="+currentPageIndex);
			
			//we launch an ajax call to get the number of pages for our datagrid
			$.ajax({
				type: 'POST',
				url: 'services/getNumPages.php',
				data: {table:'<?php echo $page; ?>'},
				success: function(retour)
				{ 
					//we empty the current html that contains the pagination
					$("#ulPagination").html();
					strHtml="";
					
					//and we make a loop on the number of pages to refreash the display
					for(var i=0;i<parseInt(retour.result);i++)
					{
						strHtml+="<li class='paginationButton "+(i==currentPageIndex-1?'active':'')+"' ><a href='#'>"+(i+1)+"</a></li>";
					}
					$("#ulPagination").html(strHtml);
					
					//now we can place events on the pagination buttons
					$(".paginationButton").click(function(event)
					{
						event.preventDefault();//...
						
						//we refresh the display
						$(".paginationButton").removeClass("active");
						$(this).addClass('active');
						
						//and we refresh the navigation datas
						currentPageIndex=$(this).index()+1;
						
						//refresh the display on click
						refresh();
					});
				},
				dataType: 'json'
			});	
			
			//hidePK();
			
		}
	</script>
		<?php 
}
