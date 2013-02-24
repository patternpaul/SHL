<?php


/**
 * NAME:    record_page_load_test.php
 * AUTHOR:  Paul Everton
 * DATE:    Feb 24, 2013
 * DESCRIPTION: tests to make sure the record page
 */

class record_page_load_test extends WebTestCase {
    /*
     * NAME:    testRecordPageLoad
     * PARAMS:  N/A
     * DESC:    tests that the record page loads
     */
    function testRecordPageLoad() {
        $this->get('http://'. $_SERVER['SERVER_NAME'] . '/recordList.php');
        $this->assertText('Best Regular Season GAA', 'The text was not found.');
    }
 
    
}
?>
