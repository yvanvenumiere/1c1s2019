<?php
//
ini_set("display_errors", "1");
include ("src/baseClasseContent.php");//import of the main content that do all the job
include ("src/baseGlobalInfosContent.php");//import of the main content that do the job for the global infos
include ("src/functions.php");//import of some functions 
include ("src/realExporter.php");//import of the ... real exporter
//include("src/globalInfos.php");//a class that can stock anything

//var_dump($_SERVER);exit;
//name of the database that is used to generate the backend
$base = (isset($_SERVER['argv'][1])?$_SERVER['argv'][1]:"pm_multimedia2");

//admin path from the document root with only separator slashes
$adminPath="orm/".$base;

//boolean that defines if it's a simple backend
$generationSimple = true;

//database infos
$infos = 'mysql:host='.(isset($_SERVER['argv'][4])?$_SERVER['argv'][4]:"pm_multimedia2").';dbname=' . $base . '';
$user = (isset($_SERVER['argv'][2])?$_SERVER['argv'][2]:"pm_multimedia2");
$mdp = (isset($_SERVER['argv'][3])?$_SERVER['argv'][3]:"xk3mgg2");
$dbh = new PDO($infos, $user, $mdp, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));




//simples instructions for the directories and the files to copy
if(!is_dir($base)) mkdir($base);
if(!is_dir($base. "/libs")) mkdir($base . "/libs");
if(!is_dir($base. "/model")) mkdir($base . "/model");
if(!is_dir($base. "/libs/php")) mkdir($base . "/libs/php");
if(!is_dir($base. "/tmp")) mkdir($base . "/tmp");
if(!is_dir($base. "/callbacks")) mkdir($base . "/callbacks");
if(!is_dir($base. "/config"))  mkdir($base . "/config");
copy("src/formCheck.php", $base . "/libs/php/formCheck.php");
copy("src/imgSaver.php", $base . "/libs/php/imgSaver.php");
copy("src/dbhHandler.php", $base . "/libs/php/dbhHandler.php");
copy("src/myTools.php", $base . "/libs/php/myTools.php");

copy("src/baseModel.php", $base . "/libs/php/baseModel.php");
copy_dir("src/js/", $base . "/libs/js/");
copy("src/index.php", $base . "/index.php");
copy("src/auth.php", $base . "/auth.php");
copy("src/router.php", $base . "/router.php");
copy("src/disconnect.php", $base . "/disconnect.php");
copy_dir("src/bootstrap/", $base . "/libs/bootstrap/");
copy_dir("src/css/", $base . "/css/");
copy_dir("src/components/", $base . "/components/");
copy_dir("src/services/", $base . "/services/");
copy_dir("src/medias/", $base . "/medias/");

//we launch the export function
export($base,$generationSimple,$infos,$user,$mdp,$dbh);

//we create the global infos class
$globalInfosClass=getGlobalInfos($adminPath,$base,$infos,$user,$mdp);

file_put_contents($base."/libs/php/globalInfos.php",$globalInfosClass);

echo "génération du model effectuée \n ";


?>