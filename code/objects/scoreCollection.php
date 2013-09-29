<?php

/**
 * NAME:    scoreCollection.php
 * AUTHOR:  Paul Everton
 * DATE:    Feb 18, 2011
 * DESCRIPTION: Collection of score Objects
 */

//requires



class scoreCollection extends collection {
    //class variables
    public $c_gameID;
    public $c_color;
    public $sql_call;
    const base_sql = "
        SELECT  p.PointNum, tp.GameID, tp.Color, tp.TeamPlayerID
        FROM    Point AS p
        INNER JOIN  TeamPlayer AS tp ON p.TeamPlayerID = tp.TeamPlayerID
        WHERE   p.PointType = 1";

    //Constructors
    function scoreCollection($p_gameID, $p_color){
        //initialize class variables
        $this->c_gameID = $p_gameID;
        $this->c_color = $p_color;

        parent::__construct();
        $this->sql_call = $this::base_sql .
                            " AND tp.GameID = " . db::fmt($this->c_gameID,1) .
                            " AND tp.Color = " . db::fmt($this->c_color,1) .
                            " ORDER BY p.PointNum";
    }



    /*
     * NAME:    load
     * PARAMS:  N/A
     * DESC:    loads the collection based off the SQL call
     */
    public function load(){
        //variable declaration
        $scoreCreated;

        //database connection
        $d = new db(0);

        //fetch the data
        $data = $d->fetch($this->sql_call);


        //fill the data
        foreach($data as $row) {

            //create the player
            $scoreCreated = new score($this->c_gameID, $row['PointNum'], $row['Color']);
            //load the player
            $scoreCreated->load();

            //add the player to the collection
            $this->add($scoreCreated);
        }


    }



}
?>
