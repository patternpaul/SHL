<?php
/**
 * NAME:    globalFunctions.php
 * AUTHOR:  Paul Everton
 * DATE:    Feb 16, 2011
 * DESCRIPTION: global functions
 */




function isLive(){
    
}

/*
 * NAME:    getIniConfigs

 * PARAMS:  n/a
 * DESC:    will return the ini config array. Checks session for the array
 *
 */
function getIniConfigs(){
    if(!isset($_SESSION['shl_configs'])){
        $_SESSION['shl_configs'] = parse_ini_file("/var/webini/shl.ini", true);
    }
    
    return $_SESSION['shl_configs'];
}


/*
 * NAME:    headerDisplay
 * PARAMS:  $p_id = the id indicating if it's an add or edit
 *          $p_addMsg = the add header
 *          $p_editMsg = the edit header
 * DESC:    Will display  either the add or the edit header
 *          message depending on the id being 0 or not
 */
function headerDisplay($p_id, $p_addMsg, $p_editMsg) {
    if($p_id > 0){
        echo $p_editMsg;
    }else{
        echo $p_addMsg;
    }
}


/**
 * Function to calculate date or time difference.
 * 
 * Function to calculate date or time difference. Returns an array or
 * false on error.
 *
 * @author       J de Silva                             <giddomains@gmail.com>
 * @copyright    Copyright &copy; 2005, J de Silva
 * @link         http://www.gidnetwork.com/b-16.html    Get the date / time difference with PHP
 * @param        string                                 $start
 * @param        string                                 $end
 * @return       array
 */
function get_time_difference( $start, $end )
{
    $uts['start']      =    strtotime( $start );
    $uts['end']        =    strtotime( $end );
    if( $uts['start']!==-1 && $uts['end']!==-1 )
    {
        if( $uts['end'] >= $uts['start'] )
        {
            $diff    =    $uts['end'] - $uts['start'];
            if( $days=intval((floor($diff/86400))) )
                $diff = $diff % 86400;
            if( $hours=intval((floor($diff/3600))) )
                $diff = $diff % 3600;
            if( $minutes=intval((floor($diff/60))) )
                $diff = $diff % 60;
            $diff    =    intval( $diff );            
            return( array('days'=>$days, 'hours'=>$hours, 'minutes'=>$minutes, 'seconds'=>$diff) );
        }
        else
        {
            trigger_error( "Ending date/time is earlier than the start date/time", E_USER_WARNING );
        }
    }
    else
    {
        trigger_error( "Invalid date/time data detected", E_USER_WARNING );
    }
    return( false );
}


/*
 * NAME:    getAutoloadString
 * PARAMS:  $arrayOfLocs = array of locations to build the string
 * DESC:    creates the autoload string based off passed array
 *
 */
function getAutoloadString($arrayOfLocs) {
    //variable declaration
    $ls_autoLoad = "";
    //build the string
    foreach ($arrayOfLocs as $file) {
        $ls_autoLoad = $ls_autoLoad . BASE_PATH . $file . "; ";
    }

    return $ls_autoLoad;
}


/*
 * NAME:    __autoload
 * PARAMS:  name= the class name trying to load
 * DESC:    Loads the player based off the player ID
 *
 */
function __autoload($className) {
    $possibilities = array(
        APPLICATION_PATH.'db'.DIRECTORY_SEPARATOR.$className.'.php',
        APPLICATION_PATH.'globals'.DIRECTORY_SEPARATOR.$className.'.php',
        APPLICATION_PATH.'objects'.DIRECTORY_SEPARATOR.$className.'.php',
        APPLICATION_PATH.'pageComponents'.DIRECTORY_SEPARATOR.$className.'.php',
        APPLICATION_PATH.'generics'.DIRECTORY_SEPARATOR.$className.'.php',
        APPLICATION_PATH.'smallff'.DIRECTORY_SEPARATOR.$className.'.php',
        '/usr/share/php' .DIRECTORY_SEPARATOR.$className.'.php'
    );
    foreach ($possibilities as $file) {

        if (file_exists($file)) {

            require_once($file);
        }
    }

}

/*
 * NAME:    gameAddEditGetGame
 * PARAMS:  $p_gameID = the game id to get
 * DESC:    Will return  the game that is currently in session
 *
 */
function gameAddEditGetGame($p_gameID) {
    //variable declaration
    $returnVal;

    //check step of game edit wizard
    if(default_post("curstep",0) == 0){
        //starting at the first step, restart
        resetGetGame($p_gameID);
    }

    $returnVal = $_SESSION['gameWizardGame'];

    //return the value
    return $returnVal;
}

/*
 * NAME:    resetGetGame
 * PARAMS:  $p_gameID = the game id to get
 * DESC:    Will reset the game object
 *
 */
function resetGetGame($p_gameID) {
    $_SESSION['gameWizardGame'] = new game($p_gameID);
}


/*
 * NAME:    default_get
 * PARAMS:  $getVarName= the name of the _GET variable,
 *          $defaultVal= the default value to return
 * DESC:    Will do verification that the variable is define, verify it has
 *          a value. If not, return the defaul
 *
 */
function default_get($getVarName, $defaultVal) {
    //variable declaration
    $returnVal = $defaultVal;

    $returnVal = default_struct("_GET", $getVarName, $defaultVal);

    //return the value
    return $returnVal;
}


/*
 * NAME:    default_post
 * PARAMS:  $getVarName= the name of the _POST variable,
 *          $defaultVal= the default value to return
 * DESC:    Will do verification that the variable is define, verify it has
 *          a value. If not, return the defaul
 *
 */
function default_post($getVarName, $defaultVal) {
    //variable declaration
    $returnVal = $defaultVal;

    $returnVal = default_struct("_POST", $getVarName, $defaultVal);

    //return the value
    return $returnVal;
}

/*
 * NAME:    default_val
 * PARAMS:  $getVarName= the name of the any variable,
 *          $defaultVal= the default value to return
 * DESC:    Will do verification that the variable is define, verify it has
 *          a value. If not, return the defaul
 *
 */
function default_val($getVarName, $defaultVal) {
    //variable declaration
    $returnVal = $defaultVal;

    $returnVal = default_struct("", $getVarName, $defaultVal);

    //return the value
    return $returnVal;
}

/*
 * NAME:    default_struct
 * PARAMS:  $p_stuct = struct to use
 *          $getVarName= the name of the _struct variable,
 *          $defaultVal= the default value to return
 * DESC:    Will do verification that the variable is define, verify it has
 *          a value. If not, return the defaul
 *
 */
function default_struct($p_stuct, $getVarName, $defaultVal) {
    //variable declaration
    $returnVal = $defaultVal;
    $structToUse;

    //find the struct to use
    switch ($p_stuct) {
        case "_GET":
            $structToUse = $_GET;
            break;
        case "_POST":
            $structToUse = $_POST;
            break;
        case "_REQUEST":
            $structToUse = $_REQUEST;
            break;
        default:
           $structToUse = $_REQUEST;
    }


    //check to see if the value is set
    if(isset($structToUse[$getVarName])){
        //ensure it is set with a value
        if($structToUse[$getVarName] != ""){
            //the variable is set, return the value
            $returnVal = $structToUse[$getVarName];
        }
    }

    //return the value
    return $returnVal;
}



/*
 * NAME:    formAction
 * PARAMS:  N/A
 * DESC:    Will build the form action attribute
 *
 */
function formAction() {
    //variable declaration
    $returnVal = "";

    $returnVal = $_SERVER['SCRIPT_NAME'] . "?" . $_SERVER['QUERY_STRING'];

    return $returnVal;
}




/*
 * NAME:    submitted
 * PARAMS:  N/A
 * DESC:    Will indicate if a form has been submitted by looking for submit button
 *
 */
function submitted($submit_Button) {
    //variable declaration
    $returnVal = false;

    if(isset($_POST[$submit_Button])){
        //form has been submitted
        $returnVal = true;
    }

    return $returnVal;
}


/*
 *
 *http://www.php-mysql-tutorial.com/wikis/php-tutorial/php-12-24-hour-time-converter-part-1.aspx
 * 
 */


    function TimeCvt($time, $format) {

  #
  #   $time   - String:  Time in either 24hr format or 12hr AM/PM format
  #   $format - Integer: "0" = 24 to 12 convert    "1" = 12 to 24 convert
  #
  #   RETURNS Time String converted to the proper format
  #

      if (preg_match("/[0-9]{1,2}:[0-9]{2}:[0-9]{2}/", $time))   {
        $has_seconds = TRUE;
      }
      else   {
        $has_seconds = FALSE;
      }

      if ($format == 0)   {         //  24 to 12 hr convert
        $time = trim ($time);

        if ($has_seconds == TRUE)   {
          $RetStr = date("g:i:s A", strtotime($time));
        }
        else   {
          $RetStr = date("g:i A", strtotime($time));
        }
      }
      elseif ($format == 1)   {     // 12 to 24 hr convert
        $time = trim ($time);

        if ($has_seconds == TRUE)   {
          $RetStr = date("H:i:s", strtotime($time));
        }
        else   {
          $RetStr = date("H:i", strtotime($time));
        }
      }

      return $RetStr;
    }



?>
