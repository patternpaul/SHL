<?php

/**
 * NAME:    stats_page_load_test.php
 * AUTHOR:  Paul Everton
 * DATE:    Feb 24, 2013
 * DESCRIPTION: tests to make sure the stats page
 */

class stats_page_load_test extends WebTestCase {
    /*
     * NAME:    testSeasonTenLoad
     * PARAMS:  N/A
     * DESC:    tests that the main season ten page loads
     */
    function testSeasonTenLoad() {
        $this->get('http://'. $_SERVER['SERVER_NAME'] . '/playerList.php?position=2&playoff=0&seasonid=10&mingames=0');
        $this->assertText('J. Auger 103 56 159 35 2.94 1.60 4.54 32.19 9 27 8 19 77.14', 'The text was not found.');
    }

    /*
     * NAME:    testSeasonTenGoalieLoad
     * PARAMS:  N/A
     * DESC:    tests that the main season ten goalie page loads
     */
    function testSeasonTenGoalieLoad() {
        $this->get('http://'. $_SERVER['SERVER_NAME'] . '/playerList.php?position=1&playoff=0&seasonid=10');
        $this->assertText('E. Bissonnette 282 36 7.83 1350 0.209 23 13 10 63.89 0 0 4 4', 'The text was not found.');
    }  
    
}

?>
