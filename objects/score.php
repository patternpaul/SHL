<?php

/**
 * NAME:    score.php
 * AUTHOR:  Paul Everton
 * DATE:    Feb 18, 2011
 * DESCRIPTION: Struct like object to describe a score
 */


class score implements iComparable {
    //class variables
    public $c_gameID;
    public $c_pointNum;
    public $c_color;
    public $c_goal;
    public $c_assist;


    //Constructors

    function score($p_gameID, $p_pointNum, $p_color){
        //set the class variable
        $this->c_gameID = $p_gameID;
        $this->c_pointNum = $p_pointNum;
        $this->c_color = $p_color;
        //call the load function of the class
        $this->load();
    }

    /*
     * NAME:    load
     * PARAMS:  N/A
     * DESC:    Loads the score based off the game ID and point number
     *
     */
    public function load(){ 

        //database connection
        $d = new db(0);

        //fetch the data
        $data1 = $d->fetch("
            SELECT  MAX(IF(p.PointType = 1, p.PointID, NULL)) AS Goal,
                    MAX(IF(p.PointType = 2, p.PointID, NULL)) AS Assist
            FROM    Point AS p
            INNER JOIN  TeamPlayer AS tp ON p.TeamPlayerID = tp.TeamPlayerID
            WHERE   p.PointNum = " . db::fmt($this->c_pointNum,1) .
            " AND   tp.GameID = " . db::fmt($this->c_gameID,1) .
            " AND   tp.Color = " . db::fmt($this->c_color,1));

      
        //fill the data
        foreach($data1 as $row) {
            //enter point code here
            $this->c_goal = new point($row['Goal']);
            if(isset($row['Assist'])){
                $this->c_assist = new point($row['Assist']);
            }
        }
    }


     /*
     * NAME:    compare
     * PARAMS:  $p_objectToCompare = passed object to compare
     * DESC:    will return true or false if its the same object
     *
     */
     public function compareTo($p_objectToCompare){
        //variable declaration
        $is_comparable = false;

        //compare the objects
        if(($this->c_gameID == $p_objectToCompare->c_gameID)
             && ($this->c_pointNum == $p_objectToCompare->c_pointNum)){
            $is_comparable = true;
        }

        return $is_comparable;
     }

     /*
     * NAME:    isID
     * PARAMS:  $p_ID = passed ID to compare
     * DESC:    will return true or false if the object has that ID
     *
     */
     public function isID($p_ID){
        //variable declaration
        $is_id = false;

        //compare the objects
        if($this->c_pointNum == $p_ID){
            $is_id = true;
        }

        return $is_id;
     }

}
?>
