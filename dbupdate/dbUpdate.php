<?php

/**
 * NAME:    dbUpdate.php
 * AUTHOR:  Paul Gagne
 * DATE:    Oct 11, 2011
 * DESCRIPTION: DB updater that uses the scripts found in the /scripts/ folder
 */

class dbUpdate {
    private $cs_protectedTables = "|DbVersion|";
    /*
     * NAME:    Update
     * PARAMS:  N/A
     * DESC:    Updates the DB to the latest version
     */
    public function Update() {

        $currentVersion;
        $upgradeVersion;
        $filepath;
        $result;

        $currentVersion =  $this->GetDbVersion();
        $filepath = APPLICATION_PATH . '/db/scripts/';

        //loop through all the script files in alphabetical order
        foreach($this->GetFiles($filepath) as $file) {
            $upgradeVersion = $this->GetDbUpgradeVersion($filepath . $file);

            //run the script if it's a greater version than the DB's current version
            if ($upgradeVersion->greaterThan($currentVersion)) {
                $result = $this->UpgradeDB($upgradeVersion, $filepath . $file);
                if ($result == false) {
                    return false;
                }
            }

        }

    }

    /*
     * NAME:    GetFiles
     * PARAMS:  $path
     * DESC:    Returns an array of files from the given path
     */
    private function GetFiles($path) {

        $handle = opendir($path);
        $files = array();

        if ($handle) {
            while (false !== ($file = readdir($handle))) {
                if ($file != "." && $file != "..") {
                    $files[] = $file;
                }
            }
            closedir($handle);
        }

        sort($files);

        return $files;

    }

    /*
     * NAME:    GetDbVersion
     * PARAMS:  $path
     * DESC:    Returns the DBs current version
     */
    private function GetDbVersion() {

        $major = 0;
        $minor = 0;
        $build = 0;
        $currentVersion;

        if ($this->TableExists('DbVersion')) {
              $ls_sql = '
            SELECT  v.Major, v.Minor, v.Build
            FROM    DbVersion AS v
            ORDER BY v.Major, v.Minor, v.Build DESC
            LIMIT 1';

            //query the DB
            $data = DBFac::getDB()->sql($ls_sql);

            if ($data && count($data) > 0) {
                $major = $data[0]['Major'];
                $minor = $data[0]['Minor'];
                $build = $data[0]['Build'];
            }
        }

        $currentVersion = new dbVersion($major, $minor, $build);

        return $currentVersion;

    }

    /*
     * NAME:    GetDbVersion
     * PARAMS:  $file
     * DESC:    Returns the DB version of the given upgrade script
     */
    private function GetDbUpgradeVersion($file) {

        $identifiers;
        $major = 0;
        $minor = 0;
        $build = 0;
        $upgradeVersion;

        preg_match_all("/\d+/", $file, $identifiers, PREG_PATTERN_ORDER);
        if (count($identifiers[0]) == 3) {
            $major = intval($identifiers[0][0]);
            $minor = intval($identifiers[0][1]);
            $build = intval($identifiers[0][2]);
        }

        $upgradeVersion = new dbVersion($major, $minor, $build);

        return $upgradeVersion;

    }
    
    
    /**
     * Creates commands based off the file
     * @param string $ps_filePath
     * @return array 
     */
    private function GetComands($ps_filePath){
        //variable declaration
        $ls_fileContents = '';
        $la_commands = array();
        
        //get the file contents
        $ls_fileContents = file_get_contents($ps_filePath);
        
        //create the commands
        $la_commands = preg_split("/;/", $ls_fileContents, -1, PREG_SPLIT_NO_EMPTY);
        
        //return the commands
        return $la_commands;
    }
    
    /**
     * Returns an array of empty arguments for the commands
     * @param array $pa_cmds
     * @return array 
     */
    private function GetComandArgs($pa_cmds){
        //variable declaration
        $la_args = array();
        
        //build the args
        for ($i = 0; $i < count($pa_cmds); $i++) {
            $la_args[$i] = array();
        }
        
        //return the args
        return $la_args;
    }
    
    /*
     * NAME:    ReloadData
     * PARAMS:  N/A
     * DESC:    Reloads the data
     */
    public function ReloadData() {
        //variable declaration
        $mra_filepath = BASE_PATH . '/resources/MraDbConverted.sql';
        $add_filepath = BASE_PATH . '/resources/AdditionalTestData.sql';
        $mra_cmds;
        $mra_args = array();
        $add_cmds;
        $add_args = array();
        $ls_dbName = DBFac::getDB()->dbname;
        $lb_mra;
        $lb_add;

        //empty out all tables
        $la_tableRows = DBFac::getDB()->sql("SHOW TABLES", array());
        //loop over tables and empty them out
        foreach($la_tableRows as $la_row){
            //protect specific tables
            if(strpos($this->cs_protectedTables, $la_row['Tables_in_' . $ls_dbName]) > 0){
                
            }else{
                DBFac::getDB()->sql("TRUNCATE " . "`".$la_row['Tables_in_' . $ls_dbName]."`", array());
            }
        }
        
        //get the comands
        $mra_cmds = $this->GetComands($mra_filepath);
        //create the args
        $mra_args = $this->GetComandArgs($mra_cmds);
        
        //get the comands
        $add_cmds = $this->GetComands($add_filepath);
        //create the args
        $add_args = $this->GetComandArgs($add_cmds);

        $lb_mra = DBFac::getDB()->sql($mra_cmds, $mra_args);
        $lb_add = DBFac::getDB()->sql($add_cmds, $add_args);
        
        return ($lb_mra && $lb_add);
    } 

    /*
     * NAME:    UpgradeDB
     * PARAMS:  $version, $filepath
     * DESC:    Upgrades the DB with the contents of the given upgrade script
     */
    private function UpgradeDB($version, $filepath) {

        $la_params = array();
        $filecontent;
        $cmds;
        $args = array();
        $ls_sql;

        //get the comands
        $cmds = $this->GetComands($filepath);
        //create the args
        $args = $this->GetComandArgs($cmds);

        $ls_sql = '
            INSERT INTO DbVersion
            (Major, Minor, Build, VersionDate)
            VALUES (:Major, :Minor, :Build, Now())';

        $la_params[':Major'] = $version->getMajor();
        $la_params[':Minor'] = $version->getMinor();
        $la_params[':Build'] = $version->getBuild();

        $cmds[] = $ls_sql;
        $args[count($cmds)-1] = $la_params;

        return DBFac::getDB()->sql($cmds, $args);
    }

    /*
     * NAME:    TableExists
     * PARAMS:  $tablename
     * DESC:    Returns true if the given table exists in the DB
     */
    private function TableExists($tablename) {

        $ls_sql = 'SHOW TABLES LIKE "' . $tablename . '"';
        $data = DBFac::getDB()->sql($ls_sql);

        if ($data) {
            return true;
        } else {
            return false;
        }

        return true;
    }

}

?>
