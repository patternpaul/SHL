<?php

/**
 * NAME:    front_page_load_test.php
 * AUTHOR:  Paul Everton
 * DATE:    Oct 21, 2011
 * DESCRIPTION: tests to make sure the front pages load
 */

class front_page_load_test extends WebTestCase {
    /*
     * NAME:    testMainPageLoad
     * PARAMS:  N/A
     * DESC:    tests that the main page loads
     */
    function testMainPageLoad() {
        $this->get('http://'. $_SERVER['SERVER_NAME'] . '/');
        $this->assertText('Player Leaders', 'The text was not found.');
    }

}

?>
