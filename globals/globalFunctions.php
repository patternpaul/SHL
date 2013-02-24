<?php
/**
 * NAME:    globalFunctions.php
 * AUTHOR:  Paul Everton
 * DATE:    Feb 16, 2011
 * DESCRIPTION: global functions
 */




//start session tracking
//the @ symbol is a hack to suppress the error message
//possible fix by changing cron job http://forum.kohanaframework.org/discussion/565/garbage-collector-error-with-sessions-on-debian/p1
@session_start();






 /*
 * NAME:    myErrorHandler
 * PARAMS:  N/A
 * DESC:    used to handle errors
 */
function myErrorHandler() {
    $errorMsg = '';
    $errorAr = F3::get('ERROR');


    //create the email string
    $errorMsg = $errorMsg . "\r\n ERROR CODE: " . $errorAr['code'];
    $errorMsg = $errorMsg . "\r\n ERROR TITLE: " . $errorAr['title'];
    $errorMsg = $errorMsg . "\r\n ERROR TEXT: " . $errorAr['text'];
    $errorMsg = $errorMsg . "\r\n ERROR Stack Trace: " . $errorAr['trace'];
    $errorMsg = $errorMsg . "\r\n SERVER ARRAY \r\n". print_r($_SERVER, true);
    
    if($errorAr['code'] == '404'){
        echo "<meta http-equiv=\"refresh\" content=\"0; url=/404.php\">";
        die();
    }else{
       sendErrorMessage($errorMsg); 
       echo "<meta http-equiv=\"refresh\" content=\"0; url=/errorPage.php\">";
        die();
    }
    

} // end func myErrorHandler 
     
    





    
/*
 * NAME:    sendErrorMessage
 * PARAMS:  $p_message = the message to send
 * DESC:    will email error message
 */
function sendErrorMessage($p_message) {
    //variable declaration
    $localMessage = $p_message;
    $configs = getIniConfigs();
    
    $from = $configs["error_email"]["from_email"];
    $to = $configs["error_email"]["to_email"];
    $subject = 'SHL ERROR CAUGHT ON ' . strftime("%m/%d/%Y %r");
    $body = $p_message;
    $host = $configs["error_email"]["host"];
    $port = $configs["error_email"]["port"];
    $username = $configs["error_email"]["from_email"];
    $password = $configs["error_email"]["from_email_pw"];

    $headers = array ('From' => $from,
      'To' => $to,
      'Subject' => $subject);
    $smtp = Mail::factory('smtp',
      array ('host' => $host,
        'port' => $port,
        'auth' => true,
        'username' => $username,
        'password' => $password));

    $mail = $smtp->send($to, $headers, $body);
}  





/*
 * NAME:    pageSecurityCheck
 * PARAMS:  N/A
 * DESC:    will handle all page security checks
 *
 */
function pageSecurityCheck() {
    //variable declaration
    $pageSecurity = new pageSecurity();
    $hasAccess = false;
    $message = "";
    //check access
    $hasAccess = $pageSecurity->hasAccessToPage();
    if($hasAccess){
        if(!CHECK_PAGE_SECURITY){
            echo 'has access';
        }
    }else{
        if(!CHECK_PAGE_SECURITY){
            echo 'has no access';
        }else{
            //send warning
            $message = " Attempt to access secured page " . $_SERVER['SCRIPT_NAME'] . "?" . $_SERVER['QUERY_STRING'];
            sendErrorMessage($message);
            //redirect to error page
            header( 'Location: /index.php' ) ;
            die();
        }
    }
}

//check to see if page security should be handled
pageSecurityCheck();




/*
 * NAME:    login
 * PARAMS:  $p_userName= the username
 *          $p_password = the password
 * DESC:    will log in a player
 *
 */
function login($p_userName, $p_password) {
    //variable declaration
    $userObj = new user();

    if($userObj->login($p_userName, $p_password)){
        $_SESSION['user'] = $userObj;
    }

    return $userObj->getMessage();
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
 * NAME:    logout
 * PARAMS:  N/A
 * DESC:    will log a player out
 *
 */
function logout() {
    if(isset($_SESSION['user'])){
        unset($_SESSION['user']);
        session_unset();
        //redirect to index
        header( 'Location: /index.php' ) ;
        die();
    }
}

/*
 * NAME:    isLoggedIn
 * PARAMS:  N/A
 * DESC:    will indicate if user is logged in
 *
 */
function isLoggedIn() {
    //variable declaration
    $returnLog = false;

    if(isset($_SESSION['user'])){
        $returnLog = true;
    }

    return $returnLog;
}

/*
 * NAME:    userName
 * PARAMS:  N/A
 * DESC:    will return logged in user's name
 *
 */
function userName() {
    //variable declaration
    $returnName = "";

    if(isLoggedIn()){
        $returnName = $_SESSION['user']->getFullName();
    }

    return $returnName;
}







/*
 * NAME:    userID
 * PARAMS:  N/A
 * DESC:    will return logged in user's id
 *
 */
function userID() {
    //variable declaration
    $returnID = "";

    if(isLoggedIn()){
        $returnID = $_SESSION['user']->getPlayerID();
    }

    return $returnID;
}

/*
 * NAME:    hasAccessLevel
 * PARAMS:  $p_level
 * DESC:    will return true or false if the currently logged in user
 *          has the appropriate access level
 *
 */
function hasAccessLevel($p_level) {
    //variable declaration
    $returnAccess = false;

    if(isLoggedIn()){
        //user is logged in, check access level
        if($_SESSION['user']->getAccess() >= $p_level){
            //user has access level, return true
            $returnAccess = true;
        }
    }
    return $returnAccess;
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
        $ls_autoLoad = $ls_autoLoad . APPLICATION_PATH . $file . "; ";
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
