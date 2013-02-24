<?php

/**
 * NAME:    player_details_page_load_test.php
 * AUTHOR:  Paul Everton
 * DATE:    Feb 24, 2013
 * DESCRIPTION: tests to make sure the player details page
 */

class player_details_page_load_test extends WebTestCase {
    /*
     * NAME:    testMainPageLoad
     * PARAMS:  N/A
     * DESC:    tests that the main page loads
     */
    function testMainPlayerDetailsLoad() {
        $this->get('http://'. $_SERVER['SERVER_NAME'] . '/playerDetails.php?playerid=35');
        $this->assertText('10 44 41 85 50 0.88 0.82 1.70 11.31 2 17 33 -16 34.00', 'The text was not found.');
        $this->assertText('10 5 4 9 7 0.71 0.57 1.29 9.43 0 3 4 -1 42.86', 'The text was not found.'); 
    }

    /*
     * NAME:    testMainPlayerGoalieDetailsLoad
     * PARAMS:  N/A
     * DESC:    tests that the main page loads
     */
    function testMainPlayerGoalieDetailsLoad() {
        $this->get('http://'. $_SERVER['SERVER_NAME'] . '/playerDetails.php?playerid=73&position=1');
        $this->assertText('10 282 36 7.83 1350 0.209 23 13 10 63.89 0 0 4 4', 'The text was not found.');
        $this->assertText('10 53 7 7.57 295 0.180 4 3 1 57.14 0 0 1 1', 'The text was not found.'); 
    }
    
    /*
     * NAME:    testMainPlayerRecordsLoad
     * PARAMS:  N/A
     * DESC:    tests that the main page loads
     */
    function testMainPlayerRecordsLoad() {
        $this->get('http://'. $_SERVER['SERVER_NAME'] . '/playerDetails.php?playerid=73&position=3');
        $this->assertText('Eric Bissonnette ', 'The text was not found.'); 
    }
}

?>
