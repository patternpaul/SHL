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
        $this->get('http://test.shl-wpg.ca/');
        $this->assertText('Posted By');
    }

    /*
     * NAME:    testAboutPageLoad
     * PARAMS:  N/A
     * DESC:    tests that the about page loads
     */
    function testLoginPageLoad() {
        $this->get('http://test.shl-wpg.ca/aboutPage.php');
        $this->assertText('Posted By');
    }

    /*
     * NAME:    testPlayerListPageLoad
     * PARAMS:  N/A
     * DESC:    tests that the player list page loads
     */
    function testPlayerListPageLoad() {
        $this->get('http://test.shl-wpg.ca/playerList.php');
        $this->assertText('Player Stats');
    }   
    /*
     * NAME:    testPlayerListPageLoad
     * PARAMS:  N/A
     * DESC:    tests that the player list page loads
     */
    function testPlayerListGoaliePageLoad() {
        $this->get('http://test.shl-wpg.ca/playerList.php?position=1&playoff=0&seasonid=10');
        $this->assertText('Goalie Stats');
    } 
    
    /*
     * NAME:    testPlayerPageLoad
     * PARAMS:  N/A
     * DESC:    tests that the player page loads
     */
    function testPlayerPageLoad() {
        $this->get('http://test.shl-wpg.ca/playerDetails.php?playerid=36');
        $this->assertText('Regular Season Stats');
    }     
    /*
     * NAME:    testPlayerGoaliePageLoad
     * PARAMS:  N/A
     * DESC:    tests that the player page loads
     */
    function testPlayerGoaliePageLoad() {
        $this->get('http://test.shl-wpg.ca/playerDetails.php?playerid=36&position=1');
        $this->assertText('Regular Season Stats');
    }     
    /*
     * NAME:    testPlayerRecordPageLoad
     * PARAMS:  N/A
     * DESC:    tests that the player record page loads
     */
    function testPlayerRecordPageLoad() {
        $this->get('http://test.shl-wpg.ca/playerDetails.php?playerid=36&position=3');
        $this->assertText('Email');
    }        
    
    /*
     * NAME:    testRecordPageLoad
     * PARAMS:  N/A
     * DESC:    tests that the  record page loads
     */
    function testRecordPageLoad() {
        $this->get('http://test.shl-wpg.ca/recordList.php');
        $this->assertText('Club');
    }       
    
}

?>
