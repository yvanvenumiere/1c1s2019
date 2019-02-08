<?php
session_start();
unset($_SESSION['connected']);
if(!isset($_SESSION['connected']))
{
	 header('Location: auth.php'); 
}
