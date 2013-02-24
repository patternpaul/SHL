<?php
/**
 * NAME:    globaVars.php
 * AUTHOR:  Paul Everton
 * DATE:    Feb 16, 2011
 * DESCRIPTION: global variables. Mainly constants
 */

$config['gc_probability'] = 0;
//define("APPLICATION_PATH", "/var/www/shl/htdocs/");
define("APPLICATION_PATH", dirname(dirname(__FILE__)) . "/");

define("VIEW_DBERRORS", true);
define("HANDLE_ERRORS", true); 
define("CHECK_PAGE_SECURITY", true);

?>
