<?php 
	if(!isset($_SESSION['connected']))
	{
		 header('Location: auth.php'); 
	}
	//we load the menu xml
	$datas=simplexml_load_file('config/menu.xml');
	
	//we create a pages array and make a loop on the content of the xml to empty our array
	$pages=array();
	foreach($datas->aMenu as $menu)
	{
		$pages[]=(string) $menu['tableName'];
	}
	
	//we create a page variable that will contains the name of the current page
	$page;
	if(isset($_GET['page']))
	{
		if(in_array($_GET['page'],$pages))
		{
			$page=$_GET['page'];
		}
		else
		{
			$page=$pages[0];
		}
	}
	else 
	{
		$page=$pages[0];
	}
	$globalInfosClass=new globalInfos();
	$globalInfosClass->setGlobalXmlConfig(simplexml_load_file("config/global_config.xml"));
?>
<!-- container of the header -->
<div class="navbar navbar-inverse navbar-fixed-top" id="headerDiv">
      <div class="navbar-inner">
        <div class="container">
          
          <div class="nav-collapse collapse">
            <ul class="nav" id="menuDiv">
              <?php 
              	foreach($datas->aMenu as $menu)
				{
					if(strlen((string)$menu['displayName'])>0)
					{?> <li <?php if($page==$menu['tableName']){echo "class='active'";} ?>><a href="router.php?page=<?php echo $menu['tableName']; ?>"><?php echo $menu['displayName']; ?></a></li> <?php } 
				}
              ?>
              
            </ul>
           
          </div>
        </div>
        
        <div id="disconnectLink"><a href="disconnect.php"> <?php echo $globalInfosClass->getGlobalInfoForNodeAndTable("global_disconnect"); ?></a></div>
      </div>
</div>
<!-- //container of the header -->

