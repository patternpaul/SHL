<?php

/**
 * NAME:    playerCollection.php
 * AUTHOR:  Paul Everton
 * DATE:    Feb 18, 2011
 * DESCRIPTION: Collection of Player Objects
 */

//requires



class gameCollection extends collection {
    //class variables
    const base_sql = "SELECT g.GameID FROM Game AS g ORDER BY g.SeasonID DESC, g.Playoff DESC, g.GameNum DESC";

    //Constructors
    function gameCollection(){
        //initialize class variables
        parent::__construct();
        $this->sql_call = $this::base_sql;
    }

    /*
     * NAME:    getLastGames
     * PARAMS:  $p_gameCount = the last X games to return
     * DESC:    sets the SQL call
     */
    public function getLastGames($p_gameCount){
        $this->sql_call = "
            SELECT g.GameID
            FROM Game AS g
            ORDER BY g.SeasonID DESC, g.Playoff DESC, g.GameNum DESC
            LIMIT " . $p_gameCount;
    }

    /*
     * NAME:    getSpecificSeason
     * PARAMS:  $p_seasonID = the season to get
     * DESC:    sets the SQL call. 0 Returns all seasons
     */
    public function getSpecificSeason($p_seasonID){
        $this->sql_call = "
            SELECT g.GameID
            FROM Game AS g ";
            if($p_seasonID > 0){
                $this->sql_call = $this->sql_call . " WHERE g.SeasonID = " . $p_seasonID;
            }
            $this->sql_call = $this->sql_call . " ORDER BY g.SeasonID DESC, g.Playoff DESC, g.GameNum DESC";
    }

    /*
     * NAME:    load
     * PARAMS:  N/A
     * DESC:    loads the collection based off the SQL call
     */
    public function load(){
        //variable declaration
        $gameCreated;

        //database connection
        $d = new db(0);

        //fetch the data
        $data = $d->fetch($this->sql_call);


        //fill the data
        foreach($data as $row) {
            //create the game
            $gameCreated = new game($row['GameID']);

            //add the player to the collection
            $this->add($gameCreated);
        }
    }




}
?>