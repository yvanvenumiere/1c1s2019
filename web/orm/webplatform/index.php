<?php 
session_start(); 
if(!isset($_SESSION['connected']))
{
	 header('Location: auth.php'); 
}
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" 
	   "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
	    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	    <!-- import of the differents js ans css files -->
	    <link rel="stylesheet" type="text/css" href="css/main.css"/>
	    <link rel="stylesheet" type="text/css" href="libs/bootstrap/css/bootstrap.min.css"/>
	    <link rel="stylesheet" type="text/css" href="libs/js/editableGrid/editablegrid-2.0.1.css"/>
	    <link rel="stylesheet" type="text/css" href="css/smoothness/jquery-ui-1.9.2.custom.min.css"/>
	    <script src="libs/js/jquery.js"></script>
	    <script src="libs/js/globalHelper.js"></script>
	    <script src="libs/js/uploader/jqueryform.js"></script>
	    <script src="libs/js/uploader/uploadHandler.js"></script>
	    <script src="libs/js/browseManager.js"></script>
	    <script src="libs/js/globalHelper.js"></script>
	    <script src="libs/js/sliderLoadTemplate.js"></script>
	    <script src="libs/bootstrap/js/bootstrap.min.js"></script>
	    <script src="libs/js/editableGrid/editablegrid-2.0.1.js"></script>
	    <script src="libs/js/tiny_mce/jquery.tinymce.js"></script>
	    <script src="libs/js/tiny_mce/tiny_mce.js"></script>
	    <script src="libs/js/jquery.scrollTo-min.js"></script>
	    <script src="libs/js/jquery-ui-1.9.2.custom.min.js"></script>
	    <script src="libs/js/jscolor/jscolor.js"></script>
    </head>
    <body>
    	
    
    <?php 
        ini_set ( "display_errors" , "1" );
	    include('libs/php/globalInfos.php');//global info class, very usefull
	    $gb=new globalInfos();
	    include('components/header.php'); //header of the backend ( with the menu )
	    include('components/content.php'); //content ( list etc )
    ?>
   	
    </body>
</html>