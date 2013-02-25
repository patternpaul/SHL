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
            self::$vars[$ps_dbName] = DBFac::makeDB($configs["db_settings"]["host"], $configs["db_settings"]["db_name"], $configs["db_settings"]["user"], $configs["db_settings"]["password"]);
                    
        }
        
        //return the DB
        return self::$vars[$ps_dbName];
    }
    
    /**
     * Returns a premade DB
     * @param string $ps_host host 
     * @param string $ps_dbname the database name
     * @param string $ps_user the login user
     * @param string $ps_password password
     */
    private static function makeDB($ps_host, $ps_dbname, $ps_user, $ps_password){
        return new db_ff(
                    'mysql:host=' . $ps_host . 
                    ';port=3306;dbname=' . $ps_dbname ,
                    $ps_user,
                    $ps_password
            );
    }
    
    
      
    
    
}
?>
