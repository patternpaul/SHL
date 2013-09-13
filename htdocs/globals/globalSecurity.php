<?php

//start session tracking
//the @ symbol is a hack to suppress the error message
//possible fix by changing cron job http://forum.kohanaframework.org/discussion/565/garbage-collector-error-with-sessions-on-debian/p1
@session_start();






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
        $errorMsg = $errorMsg . "Aborting...<br />\n";
        echo "<meta http-equiv=\"refresh\" content=\"0; url=/errorPage.php\">";
        sendErrorMessage($errorMsg); 
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
        $errorMsg = $errorMsg . "Aborting...<br />\n";
        echo "<meta http-equiv=\"refresh\" content=\"0; url=/errorPage.php\">";
        sendErrorMessage($errorMsg); 
                exit(1);
        die();
        break;
    }
    echo "MESSAGE: " . $errorMsg;
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
    $errorMsg = $errorMsg . "\r\n ERROR CODE: " . $errorAr['code'];
    $errorMsg = $errorMsg . "\r\n ERROR TEXT: " . $errorAr['text'];
    $errorMsg = $errorMsg . "\r\n ERROR Stack Trace: " . print_r($errorAr['trace']);
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
