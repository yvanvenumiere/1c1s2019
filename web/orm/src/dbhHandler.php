<?php
/*
* This file is a class that reprensents the base class for every model object
* (c) 2012 Yvan Vénumière <yvan.venumierer@gmail.com>
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

/**
 * baseModel is an object that contains the base properties and medthods for a database model class
 *
 */
class dbhHandler
{
    private static $dbh = null;
    public static function getInstance($dbInfos,$dbUser,$dbPass) {

        if(is_null(self::$dbh)) {
            self::$dbh = new PDO($dbInfos, $dbUser,$dbPass, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
        }

        return self::$dbh;
    }
}