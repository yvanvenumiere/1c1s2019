<?php
ini_set ( "display_errors" , "1" );
session_start();
if(!isset($_SESSION['connected']))
{
	 header('Location: auth.php'); 
}

//import of differents files
include('../../libs/php/globalInfos.php');
$gb=new globalInfos();

?>