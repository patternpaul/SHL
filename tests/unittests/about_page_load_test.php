<?php


/**
 * NAME:    about_page_load_testc.php
 * AUTHOR:  Paul Everton
 * DATE:    Feb 24, 2013
 * DESCRIPTION: tests to make sure the record page
 */

class about_page_load_testc extends WebTestCase {
    /*
     * NAME:    testAboutPageLoad
     * PARAMS:  N/A
     * DESC:    tests that the record page loads
     */
    function testAboutPageLoad() {
        
        $this->get('http://'. $_SERVER['SERVER_NAME'] . '/aboutPage.php');
        $this->assertText('Posted By', 'The text was not found.');
    }
 
    
}
?>
