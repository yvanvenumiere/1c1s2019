<?php
				$infos="mysql:host=localhost:8889;dbname=webplatform";
				$user="webplatform";
				$mdp="webplatform";
				$dbh=new PDO($infos,$user,$mdp,array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		     ?>