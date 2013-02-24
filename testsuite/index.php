<?php
/**
 * NAME:    testsuite.php
 * AUTHOR:  Paul Everton
 * DATE:    Oct 1, 2011
 * DESCRIPTION: loads all tests
 */

require_once(dirname(__FILE__) . '/simpletest/autorun.php');
require_once(dirname(__FILE__) . '/simpletest/web_tester.php');
class AllFileTests extends TestSuite {
    function __construct() {
        parent::__construct();
        $this->collect(dirname(__FILE__) . '/unittests',
                       new SimplePatternCollector('/_test.php/'));
    }
}
?>
