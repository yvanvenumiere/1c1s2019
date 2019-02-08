<?php
session_start();
ini_set ( "display_errors" , "1" );
include('libs/php/globalInfos.php');//global info class, very usefull
$gb=new globalInfos();
$gb->setGlobalXmlConfig(simplexml_load_file("config/global_config.xml"));
if(isset($_SESSION['connected']))
{
	 header('Location: index.php'); 
}

if(isset($_POST['login']) && isset($_POST['mdp']))
{
	if($_POST['login']==(string)$gb->getGlobalInfoForNodeAndTable("global_login") && $_POST['mdp']==(string)$gb->getGlobalInfoForNodeAndTable("global_password"))
	{
		$_SESSION['connected']="ok";
		header('Location: index.php');
	}
	else 
	{
		$errorMessage=$gb->getGlobalInfoForNodeAndTable("global_wrong_login");
	}
	 //header('Location: auth.php'); 
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
	    <script src="libs/bootstrap/js/bootstrap.min.js"></script>
	    <script src="libs/js/jquery-ui-1.9.2.custom.min.js"></script>
    </head>
    <body>
   	
   	<div id="contAuth" class="mAuto relative hero-unit">
   		<legend>Authentification</legend>
	   	<form role="form" action="auth.php" method="POST">
		  <div class="form-group">
		    <label for="login"><?php echo $gb->getGlobalInfoForNodeAndTable("global_login_label");?></label>
		    <input type="text" class="form-control" name="login" id="login" placeholder="Identifiant">
		  </div>
		  <div class="form-group">
		    <label for="mdp"><?php echo $gb->getGlobalInfoForNodeAndTable("global_password_label");?></label>
		    <input type="password" class="form-control" name="mdp" id="mdp" placeholder="Mot de passe">
		  </div>
		  <?php if (isset($errorMessage)){echo $errorMessage;} ?>
		  <button type="submit" class="btn btn-default"><?php echo $gb->getGlobalInfoForNodeAndTable("global_connect");?></button>
		</form>
	</div>
   	
   	</body>
</html>