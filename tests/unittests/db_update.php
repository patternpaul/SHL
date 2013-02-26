<?php

/**
 * NAME:    db_update.php
 * AUTHOR:  Paul Gagne
 * DATE:    Oct 11, 2011
 * DESCRIPTION: tests updating the DB to the latest version
 */


class DbUpdateTest extends UnitTestCase {

    /*
     * NAME:    testDbUpdate
     * PARAMS:  N/A
     * DESC:    this function will update the DB to the latest version
     */
    function testDbUpdate() {
        //variable declaration

        $updater = new dbUpdate();
        $updater->Update();

    }
/*
     * NAME:    testReloadData
     * PARAMS:  N/A
     * DESC:    this function will reload the base data
     */
    function testReloadData() {
        //variable declaration

        $updater = new dbUpdate();
        $updater->ReloadData();

    }
}


