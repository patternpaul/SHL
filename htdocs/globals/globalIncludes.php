<?php
/**
 * NAME:    globalIncludes.php
 * AUTHOR:  Paul Everton
 * DATE:    Feb 16, 2011
 * DESCRIPTION: a global include file
 */
require_once(dirname(__FILE__).'/globalVars.php');
require_once(dirname(__FILE__).'/globalFunctions.php');
require_once(BASE_PATH.'/packages/fatfree/lib/base.php');
require_once(BASE_PATH.'/packages/fatfree/lib/smtp.php');
    F3::set('AUTOLOAD',getAutoloadString(array('/htdocs/db/', '/code/generics/', '/htdocs/globals/', '/code/objects/',
        '/htdocs/pageComponents/', '/dbupdate/')) . " /usr/share/php;");
    
    require_once(dirname(__FILE__).'/globalSecurity.php');
if(isLive()){
    F3::set('ONERROR','myErrorHandler');
}



?>
