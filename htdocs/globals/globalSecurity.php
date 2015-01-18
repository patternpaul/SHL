<?php

//start session tracking
//the @ symbol is a hack to suppress the error message
//possible fix by changing cron job http://forum.kohanaframework.org/discussion/565/garbage-collector-error-with-sessions-on-debian/p1
@session_start();


require_once(BASE_PATH.'/packages/le_php-master/logentries.php');

 /*
 * NAME:    myErrorHandler
 * PARAMS:  N/A
 * DESC:    used to handle errors
 */
function myErrorHandlerZ() {
    $errorMsg = '';
    $errorAr = F3::get('ERROR');
echo 'ERROR HANDLER!!!LOZL';

    //create the email string
//    $errorMsg = $errorMsg . "\r\n ERROR CODE: " . $errorAr['code'];
//    $errorMsg = $errorMsg . "\r\n ERROR TITLE: " . $errorAr['title'];
//    $errorMsg = $errorMsg . "\r\n ERROR TEXT: " . $errorAr['text'];
//    $errorMsg = $errorMsg . "\r\n ERROR Stack Trace: " . $errorAr['trace'];
//    $errorMsg = $errorMsg . "\r\n SERVER ARRAY \r\n". print_r($_SERVER, true);
$errorMsg = $errorMsg . "\r\n ERROR ARRAY \r\n". print_r($errorAr, true);
    echo $errorMsg;
//    if($errorAr['code'] == '404'){
//        echo "<meta http-equiv=\"refresh\" content=\"0; url=/404.php\">";
//        die();
//    }else{
//       sendErrorMessage($errorMsg); 
//       echo "<meta http-equiv=\"refresh\" content=\"0; url=/errorPage.php\">";
//        die();
//    }
    

} // end func myErrorHandler 
     
// error handler function
function myErrorHandlerTwo($errno, $errstr, $errfile, $errline)
{


        $errorMsg = $errorMsg . "Unknown error type: [$errno] $errstr<br />\n";
        $errorMsg = $errorMsg . "  Fatal error on line $errline in file $errfile";
        $errorMsg = $errorMsg . ", PHP " . PHP_VERSION . " (" . PHP_OS . ")<br />\n";
            $errorMsg = $errorMsg . "\r\n SERVER ARRAY \r\n". print_r($_SERVER, true);
            $errorMsg = $errorMsg . "\r\n SESSION ARRAY \r\n". print_r($_SESSION, true);
            
        $errorMsg = $errorMsg . "Aborting...<br />\n";
 
        sendErrorMessage($errorMsg); 
        F3::reroute('/errorPage.php');


    /* Don't execute PHP internal error handler */
    return true;
}
function exception_handler($e)
{
		$errstr = $e->getMessage();
		$errline = $e->getLine();
		$errfile = $e->getFile();
		$errno = $e->getCode();
		$errtrace = $e->getTraceAsString();
		
		$errorMsg = "";
        $errorMsg = $errorMsg . "[$errno] $errstr\n\r";
        $errorMsg = $errorMsg . "  Fatal error on line $errline in file $errfile \n\r";
		$errorMsg = $errorMsg . "  Trace $errtrace \n\r";
        $errorMsg = $errorMsg . ", PHP " . PHP_VERSION . " (" . PHP_OS . ")\n\r";
                    $errorMsg = $errorMsg . "\r\n SERVER ARRAY \r\n". print_r($_SERVER, true);
            $errorMsg = $errorMsg . "\r\n SESSION ARRAY \r\n". print_r($_SESSION, true);
        $errorMsg = $errorMsg . "Aborting...\n\r";



    sendErrorMessage($errorMsg);
}

// error handler function
function myErrorHandler($errno, $errstr, $errfile, $errline)
{
    if (!(error_reporting() & $errno)) {
        // This error code is not included in error_reporting
        return;
    }

    $errorMsg = '';
    
    switch ($errno) {
    case E_USER_ERROR:
        $errorMsg = $errorMsg . "<b>My ERROR</b> [$errno] $errstr<br />\n";
        $errorMsg = $errorMsg . "  Fatal error on line $errline in file $errfile";
        $errorMsg = $errorMsg . ", PHP " . PHP_VERSION . " (" . PHP_OS . ")<br />\n";
                    $errorMsg = $errorMsg . "\r\n SERVER ARRAY \r\n". print_r($_SERVER, true);
            $errorMsg = $errorMsg . "\r\n SESSION ARRAY \r\n". print_r($_SESSION, true);
        $errorMsg = $errorMsg . "Aborting...<br />\n";
        //echo "<meta http-equiv=\"refresh\" content=\"0; url=/errorPage.php\">";
        sendErrorMessage($errorMsg);
        //F3::reroute('/errorPage.php');
        exit(1);
        die();
        break;

    case E_USER_WARNING:
        $errorMsg = $errorMsg . "<b>My WARNING</b> [$errno] $errstr<br />\n";
        sendErrorMessage($errorMsg); 
        break;

    case E_USER_NOTICE:
        $errorMsg = $errorMsg . "<b>My NOTICE</b> [$errno] $errstr<br />\n";
        sendErrorMessage($errorMsg); 
        break;

    default:
        $errorMsg = $errorMsg . "Unknown error type: [$errno] $errstr<br />\n";
        $errorMsg = $errorMsg . "  Fatal error on line $errline in file $errfile";
        $errorMsg = $errorMsg . ", PHP " . PHP_VERSION . " (" . PHP_OS . ")<br />\n";
                    $errorMsg = $errorMsg . "\r\n SERVER ARRAY \r\n". print_r($_SERVER, true);
            $errorMsg = $errorMsg . "\r\n SESSION ARRAY \r\n". print_r($_SESSION, true);
        $errorMsg = $errorMsg . "Aborting...<br />\n";
        //echo "<meta http-equiv=\"refresh\" content=\"0; url=/errorPage.php\">";
        sendErrorMessage($errorMsg); 
       // F3::reroute('/errorPage.php');
                exit(1);
        die();
        break;
    }
    
    /* Don't execute PHP internal error handler */
    return true;
}






 /*
 * NAME:    myErrorHandlerTest
 * PARAMS:  N/A
 * DESC:    used to handle errors
 */
function myErrorHandlerTest() {
    $errorMsg = '';
    $errorAr = F3::get('ERROR');


    //create the email string
    $errorMsg = $errorMsg . "\r\n ERROR CODE: " . F3::get('ERROR.code');
    $errorMsg = $errorMsg . "\r\n ERROR TEXT: " . F3::get('ERROR.title');
    $errorMsg = $errorMsg . "\r\n ERROR Stack Trace: " . F3::get('ERROR.trace');
        echo "<meta http-equiv=\"refresh\" content=\"0; url=/errorPage.php\">";
        sendErrorMessage($errorMsg); 
                exit(1);
        die();
    echo $errorMsg;
    

} // end func myErrorHandler 
        
    
/*
 * NAME:    sendErrorMessage
 * PARAMS:  $p_message = the message to send
 * DESC:    will email error message
 */
function sendErrorMessage($p_message) {
    //variable declaration
    $localMessage = $p_message;
	
/**********
*  BEGIN - User - Defined Variables
***********/


	// put your Logentries Log Token inside the double quotes in the $LOGENTRIES_TOKEN constant below.
  	$lelogconfigs = iniconfig::getConfigs();
	
  	$LOGENTRIES_TOKEN = $lelogconfigs["logentries"]["token"];



/*  
*	To Send Log Events To Your DataHub, Change The Following Variables
*		1. Change the $DATAHUB_ENABLED variable to true;	
*		2. IP Address of your datahub location  
*		3. Set the Port for communicating with Datahub (10000 default) 
*
*		NOTE: If $DATAHUB_ENABLED = true, Datahub will ignore your Logentries log token as it is not required when using Datahub.
*/
	
	$DATAHUB_ENABLED = false;
	
	
	// Your DataHub IP Address MUST be specified if $DATAHUB_ENABLED = true
 	
 	$DATAHUB_IP_ADDRESS = "";
	
		
	//	  Default port for DataHub is 10000, 
	//    If you change this from port 10000, you will have to change your settings port on your datahub machine, 
	//	  specifically in the datahub local config file in /etc/leproxy/leproxyLocal.config then restart leproxy - sudo service leproxy restart
	
	$DATAHUB_PORT = 10000;	
	
	
	// Allow Your Host Name To Be Printed To Your Log Events As Key / Value Pairs.
	// To give your Log events a Host_Name which will appear in your logs as Key Value Pairs, change this value to 'true' (without quotes)

	$HOST_NAME_ENABLED = true;

	
	// Enter a Customized Host Name to appear in your Logs - If no host name is entered one will be assigned based on your own Host name for the local machine using the php function gethostname();

	$HOST_NAME = "DemoHost";
 
	
	
	// Enter a Host ID to appear in your Log events
	// if $HOST_ID is empty "", it wil not print to your log events.  This value will only print to your log events if there is a value below as in $HOST_ID="12345".
	
	$HOST_ID = "DigitalOceanKey";
	
	
	
/************
*  END  -  User - Defined Variables
************/

	
	

	// Whether the socket is persistent or not
	$Persistent = true;

	// Whether the socket uses SSL/TLS or not
	$SSL = false;
	
	// Set the minimum severity of events to send
	$Severity = LOG_DEBUG;
	/*
	 *  END  User - Defined Variables
	 */

	// Ignore this, used for PaaS that support configuration variables
	$ENV_TOKEN = getenv('LOGENTRIES_TOKEN');
	
	// Check for environment variable first and override LOGENTRIES_TOKEN variable accordingly
	if ($ENV_TOKEN != false && $LOGENTRIES_TOKEN === "")
	{
		$LOGENTRIES_TOKEN = $ENV_TOKEN;
	}
	
	print_r("<b> ERROR HAPPENED. It has been logged </b>");
	$newLog = LeLogger::getLogger($LOGENTRIES_TOKEN, $Persistent, $SSL, $Severity, $DATAHUB_ENABLED, $DATAHUB_IP_ADDRESS, $DATAHUB_PORT, $HOST_ID, $HOST_NAME, $HOST_NAME_ENABLED);
    $newLog->Debug($localMessage);
    
}  


/*
 * NAME:    sendErrorMessage
 * PARAMS:  $p_message = the message to send
 * DESC:    will email error message
 */
function sendErrorMessageBAK($p_message) {
    //variable declaration
    $localMessage = $p_message;
    $configs = iniconfig::getConfigs();
    
    $from = $configs["error_email"]["from_email"];
    $to = $configs["error_email"]["to_email"];
    $subject = 'SHL ERROR CAUGHT ON ' . strftime("%m/%d/%Y %r");
    $body = $p_message;
    $host = $configs["error_email"]["host"];
    $port = $configs["error_email"]["port"];
    $username = $configs["error_email"]["from_email"];
    $password = $configs["error_email"]["from_email_pw"];
     
    $mail=new SMTP($host,$port,'SSL',$username,$password);
    $mail->set('from','<'.$from.'>');
    $mail->set('to','<'.$to.'>');
    $mail->set('subject',$subject);
    $mail->send($body);
    
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
    if(isLive()){
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
}

//check to see if page security should be handled
pageSecurityCheck();

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
?>
