<?php

/**
 * NAME:    iniconfig.php
 * AUTHOR:  Paul Everton
 * DATE:    Feb 25, 2013
 * DESCRIPTION: Object to get configs
 */

//requires



class iniconfig {
    //class variables
    protected static $vars;

    //gets configs
    static function getConfigs($ps_configs = 'default'){
        if(!self::$vars[$ps_configs]){
            //set the configs
            self::$vars[$ps_configs] = parse_ini_file("/var/webini/shl.ini", true);
        }
        
        //return the DB
        return self::$vars[$ps_configs];
    }
    static function getConfigsToUse(){
        $db_setting = "db_settings";
        if(isLive()){
            $db_setting = "db_settings";
        }else{
            $db_setting = "test_db_settings";
        }
  
        //return the DB
        return $db_setting;
    }
}
?>
