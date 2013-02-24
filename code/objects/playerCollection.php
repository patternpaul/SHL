<?php

/**
 * NAME:    playerCollection.php
 * AUTHOR:  Paul Everton
 * DATE:    Feb 18, 2011
 * DESCRIPTION: Collection of Player Objects
 */

//requires



class playerCollection extends collection {
    //class variables
    public $sql_call;
    const base_sql = "SELECT p.PlayerID FROM Player AS p ORDER BY p.FName, p.LName";
    public $c_seasonID;
    
    //Constructors
    function playerCollection(){
        //initialize class variables
        parent::__construct();
        $this->sql_call = $this::base_sql;
        $this->c_seasonID = 0;
    }



    /*
     * NAME:    load
     * PARAMS:  N/A
     * DESC:    loads the collection based off the SQL call
     */
    public function load(){
        //variable declaration
        $playerCreated;

        //database connection
        $d = new db(0);

        //fetch the data
        $data = $d->fetch($this->sql_call);

        //fill the data
        foreach($data as $row) {
            //create the player
            $playerCreated = new player($row['PlayerID']);
            
            //add the player to the collection
            $this->add($playerCreated);
        }
    }


    /*
     * NAME:    getSeasonPlayers
     * PARAMS:  $p_seasonID = the season you want
     * DESC:    sets the SQL call to select players from a specific season
     */
    public function getSeasonPlayers($p_seasonID){
        $this->c_seasonID = $p_seasonID;
        $this->sql_call = "
            SELECT DISTINCT p.PlayerID
            FROM        Player AS p
            INNER JOIN  TeamPlayer AS tp ON p.PlayerID = tp.PlayerID
            INNER JOIN  Game AS g ON tp.GameID = g.GameID
            WHERE       1 = 1 ";
            if($p_seasonID > 0){
                 $this->sql_call =  $this->sql_call . " AND g.SeasonID = " . db::fmt($this->c_seasonID,1);
            }

            $this->sql_call =  $this->sql_call . " ORDER BY p.FName, p.LName";
    }


}
?>