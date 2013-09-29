<?php
/**
 * NAME:    globalIncludes.php
 * AUTHOR:  Paul Everton
 * DATE:    Feb 16, 2011
 * DESCRIPTION: a global include file
 */


require_once(dirname(__FILE__).'/globalVars.php');
require_once(dirname(__FILE__).'/globalFunctions.php');
  $f3=require_once(BASE_PATH.'/packages/fatfree/lib/base.php');
require_once(BASE_PATH.'/packages/fatfree/lib/smtp.php');
    F3::set('AUTOLOAD',getAutoloadString(array('/htdocs/db/', '/code/generics/', '/htdocs/globals/', '/code/objects/',
        '/htdocs/pageComponents/', '/dbupdate/')));
    
    require_once(dirname(__FILE__).'/globalSecurity.php');
    
 ini_set('display_errors', 'On'); 
error_reporting(E_ALL);

ini_set('error_reporting', E_ALL);
    

if(isLive()){
    F3::set('DEBUG',3);
    $old_error_handler = set_error_handler("myErrorHandlerTwo");
    //set the assertions
    assert_options(ASSERT_ACTIVE, 0);
    assert_options(ASSERT_WARNING, 0);
    assert_options(ASSERT_BAIL, 0);
    
}else{
    //set the assertions
    assert_options(ASSERT_ACTIVE, 1);
    assert_options(ASSERT_WARNING, 1);
    assert_options(ASSERT_BAIL, 0);
    assert_options(ASSERT_CALLBACK, 'my_assert_handler');  
    
}



?>
