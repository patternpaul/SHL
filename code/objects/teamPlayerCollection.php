<?php

/**
 * NAME:    teamPlayerCollection.php
 * AUTHOR:  Paul Everton
 * DATE:    Feb 18, 2011
 * DESCRIPTION: Collection of Team Player Objects
 */



class teamPlayerCollection extends collection {
    //class variables
    public $sql_call;
    const base_sql = "SELECT tp.TeamPlayerID FROM TeamPlayer AS tp";

    //Constructors
    function playerCollection(){
        //initialize class variables
        parent::__construct();
        $this->sql_call = $this::base_sql;
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
            $playerCreated = new teamPlayer($row['TeamPlayerID']);
            //load the player
            $playerCreated->load();

            //add the player to the collection
            $this->add($playerCreated);
        }
    }

    /*
     * NAME:    getGameTeam
     * PARAMS:  $p_gameID = the game you want
     *          $p_color = the specific team
     * DESC:    sets the SQL call to the game and team
     */
    public function getGameTeam($p_gameID, $p_color){
        $this->sql_call = "
            SELECT      tp.TeamPlayerID
            FROM        TeamPlayer AS tp
            WHERE       tp.Color = " . db::fmt($p_color,1) .
            " AND        tp.GameID = " . db::fmt($p_gameID,1) .
            " ORDER BY tp.TeamPlayerID ASC";
    }


    /*
     * NAME:    getPlayer
     * PARAMS:  $p_playerID = the player id 
     * DESC:    gets the team player with the ID specified
     */
    public function getPlayer($p_playerID){
        $returnObj = null;
        $tempObj;

        for($alpha = 0; $alpha < $this->count(); $alpha += 1) {
            $tempObj = $this->get($alpha);
            if($tempObj->getPlayerID() == $p_playerID){
               $returnObj = $tempObj;
            }
        }

        //return the found object
        return $returnObj;
    }

}
?>