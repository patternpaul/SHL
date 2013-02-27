<?php

/**
 * NAME:    testsuite.php
 * AUTHOR:  Paul Everton
 * DATE:    Feb 24, 2013
 * DESCRIPTION: loads all tests
 */

require_once('simpletest/unit_tester.php');
require_once('simpletest/mock_objects.php');
require_once('simpletest/collector.php');
require_once('simpletest/default_reporter.php');

require_once('simpletest/web_tester.php');
require_once('simpletest/reporter.php');
require_once('simpletest/xml.php');


class AllFileTests extends TestSuite {
    function __construct() {
        parent::__construct();
        
        $this->addFile(dirname(__FILE__) . '/unittests/db_update.php');
        if(!isLive()){
            $this->addFile(dirname(__FILE__) . '/unittests/db_reload.php');
            $this->addFile(dirname(__FILE__) . '/unittests/admin_navigation.php');
        }
        $this->collect(dirname(__FILE__) . '/unittests', new SimplePatternCollector('/_test.php/'));
    }
}

$at = new AllFileTests();
if(default_get("reporttype", "html") == "xml"){
    $at->run(new XMLReporter());
}else{
    $at->run(new HtmlReporter());
}

?>
