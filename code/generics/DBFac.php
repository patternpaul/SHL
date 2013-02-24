<?php
/**
 * NAME:    DBFac.php
 * AUTHOR:  Paul Everton
 * DATE:    Feb 24, 2013
 * DESCRIPTION: factory class for the DB
 */
class DBFac {
    protected static $vars;



    static function getDB($ps_dbName = 'shlDB'){
        if(!self::$vars[$ps_dbName]){
            $configs = getIniConfigs();
            //instantiate the DB
            self::$vars[$ps_dbName] = new db_ff(
                    'mysql:host=' . $configs["db_settings"]["host"] . 
                    ';port=3306;dbname=' . $configs["db_settings"]["db_name"] ,
                    $configs["db_settings"]["user"],
                    $configs["db_settings"]["password"]
            );
        }
        
        //return the DB
        return self::$vars[$ps_dbName];
    }
    
    
      
    
    
}
?>
