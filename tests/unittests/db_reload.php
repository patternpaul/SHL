<?php

/**
 * NAME:    db_reload.php
 * AUTHOR:  Paul Everton
 * DATE:    Feb 26, 2013
 * DESCRIPTION: tests reloading the DB
 */


class DbReloadTest extends UnitTestCase {

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


