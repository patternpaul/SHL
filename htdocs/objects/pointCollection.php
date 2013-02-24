<?php

/**
 * NAME:    pointCollection.php
 * AUTHOR:  Paul Everton
 * DATE:    Feb 18, 2011
 * DESCRIPTION: Collection of Point Objects
 */

//requires



class pointCollection extends statCollection {
    //class variables
    public $sql_call;

    const base_sql = "SELECT p.PointID FROM Point AS p";


    //Constructors
    function pointCollection(){
        //initialize class variables
        parent::__construct();
        $this->sql_call = $this::base_sql;
        $this->c_assistCount = 0;
        $this->c_goalCount = 0;
    }



    /*
     * NAME:    load
     * PARAMS:  N/A
     * DESC:    loads the collection based off the SQL call
     */
    public function load(){
        //variable declaration
        $pointCreated;

        //database connection
        $d = new db(0);

        //fetch the data
        $data = $d->fetch($this->sql_call);

        //fill the data
        foreach($data as $row) {
            //create the player
            $pointCreated = new point($row['PointID']);
            //load the player
            $pointCreated->load();
            //count the point
            $this->countPoint($pointCreated);

            //add the point to the collection
            $this->add($pointCreated);
        }

        //add the one game
        $this->addGameCount(1);
        //indicate it has been filled;
        $this->hasBeenFilled();
    }


    /*
     * NAME:    countPoint
     * PARAMS:  $p_point = point to count
     * DESC:    will count the point as an assist or goal
     */
    public function countPoint($p_point){
        if($p_point->c_pointType == 1){
            $this->addGoalCount(1);
        }else{
            $this->addAssistCount(1);
        }
    }


    /*
     * NAME:    getTeamPlayerPoints
     * PARAMS:  $p_teamPlayerID = the team player
     * DESC:    sets the SQL call to select points from a specific team player
     */
    public function getTeamPlayerPoints($p_teamPlayerID){
        $this->sql_call = "
            SELECT      p.PointID
            FROM        Point AS p
            WHERE       p.TeamPlayerID = " . db::fmt($p_teamPlayerID,1);
    }


    /*
     * NAME:    fillScores
     * PARAMS:  N/A
     * DESC:    will fill the assist and goal counts
     */
    public function fillScores(){
        //empty implementation
    }



}
?>