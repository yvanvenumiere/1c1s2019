<?php
session_start();
if(!isset($_SESSION['connected']))
{
	 header('Location: auth.php'); 
}
//if there is a no file
if($_FILES["tempFile"]["error"]!=0)
{
	echo json_encode(array("result"=>"ko","message"=>"problème lors de la lecture du fichier"));exit;
}
else //else wee move it to a tempory file
{
	move_uploaded_file($_FILES["tempFile"]["tmp_name"], "../tmp/".$_FILES["tempFile"]["name"]);
	echo json_encode(array("result"=>"ok","message"=>"fichier lu","fileName"=>$_FILES["tempFile"]["name"]));exit;
}
?>