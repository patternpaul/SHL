<?php
/**
 * NAME:    globalIncludes.php
 * AUTHOR:  Paul Everton
 * DATE:    Feb 16, 2011
 * DESCRIPTION: a global include file
 */
require_once(dirname(__FILE__).'/globalVars.php');
require_once(dirname(__FILE__).'/globalFunctions.php');
require_once(APPLICATION_PATH.'/fatfree/lib/base.php');
require_once(APPLICATION_PATH.'/fatfree/lib/smtp.php');

    F3::set('AUTOLOAD',getAutoloadString(array('/db/', '/globals/', '/objects/',
        '/pageComponents/', '/generics/', '/smallff/')) . " /usr/share/php;");
if(HANDLE_ERRORS){
    F3::set('ONERROR','myErrorHandler');
}
?>
