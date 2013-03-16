<?php

/**
 * NAME:    db_mass_update.php
 * AUTHOR:  Paul Everton
 * DATE:    Feb 26, 2013
 * DESCRIPTION: tests reloading the DB
 */


class DbMassUpdateTest extends UnitTestCase {

/*
     * NAME:    testReloadData
     * PARAMS:  N/A
     * DESC:    this function will reload the base data
     */
    function testMassUpdate() {
        //variable declaration
        $la_params = array();
        $ls_sql = '
          SELECT MAX(SeasonID) As MaxSeasonID
          FROM Game';
        
        $li_maxSeason = 0;
        
        //querry the DB
        $data = DBFac::getDB()->exec($ls_sql, $la_params);
        
        //get the season
        foreach($data as $row) {
            $li_maxSeason = $row['MaxSeasonID'];
        }
        
        //loop over and update all the seasons data
        $ls_sql = 'CALL masterUpdateProc(:seasonID)';
        $la_params = array();
        for ( $alpha = 1; $alpha <= $li_maxSeason; $alpha += 1) {
            $la_params["seasonID"] = $alpha;
            //full update
            $data = DBFac::getDB()->exec($ls_sql, $la_params);
        }
        
        //reload the records
        $recordObj = new records();
        $recordObj->recordRefil();

    }
}


