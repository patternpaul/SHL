<?php

/**
 * NAME:    point.php
 * AUTHOR:  Paul Everton
 * DATE:    Feb, 2011
 * DESCRIPTION: Object describing a point
 */


class point extends object implements iComparable {
    //class variables
    public $c_pointID;
    public $c_pointNum;
    public $c_pointType;
    public $c_teamPlayerID;

    //Constructors

    function point($p_pointID){
        //set the class variable
        $this->c_pointID = $p_pointID;

        $this->setObjectID($p_pointID);
        
        if($p_pointID > 0){
            //call the load function of the class
            $this->load();
        }
    }

    /*
     * NAME:    load
     * PARAMS:  N/A
     * DESC:    Loads the point based off the point ID
     *
     */
    public function load(){
        

        //database connection
        $d = new db(0);

        //fetch the data
        $data1 = $d->fetch("
            SELECT  p.PointID,
                    p.PointNum,
                    p.PointType,
                    p.TeamPlayerID
            FROM    Point AS p
            WHERE   p.PointID = " . db::fmt($this->c_pointID,1));


        //fill the data
        foreach($data1 as $row) {
            $this->c_pointID = $row['PointID']; 
            $this->c_pointNum = $row['PointNum'];
            $this->c_pointType = $row['PointType'];
            $this->c_teamPlayerID = $row['TeamPlayerID'];
        }
    }


     /*
     * NAME:    update
     * PARAMS:  N/A
     * DESC:    updates a game
     *
     */
    public function update(){
        //check to ensure no errors occured
        if(!$this->hasError()){
            //database connection
            $d = new db(0);



            //update the data
            $data1 = $d->exec("
                UPDATE  Point
                SET     PointNum = " . db::fmt($this->c_pointNum,1) . ",
                        PointType = " . db::fmt($this->c_pointType,1) . ",
                        TeamPlayerID = " . db::fmt($this->c_teamPlayerID,1) . "
                WHERE   PointID = " . db::fmt($this->c_pointID,1));

            //indicate that the point was updated
            $this->addDBMessage("Point Updated", "Point not Updated! Database Error!", $data1, $d);
        }

    }


    /*
     * NAME:    insert
     * PARAMS:  N/A
     * DESC:    inserts a player
     *
     */
    public function insert(){
        //check to ensure no errors occured
        if(!$this->hasError()){
            //database connection
            $d = new db(0);


            //update the data
            $data = $d->exec("
                INSERT INTO  Point (PointNum, PointType, TeamPlayerID)
                VALUES (" . db::fmt($this->c_pointNum,1) . ",
                        " . db::fmt($this->c_pointType,1) . ",
                        " . db::fmt($this->c_teamPlayerID,1) . ")");

            //set the id inserted
            $this->c_pointID = $d->last_id;

            //indicate that the point was inserted
            $this->addDBMessage("Point Added", "Point not Added! Database Error!", $data, $d);
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
        if($this->c_pointID == $p_objectToCompare->c_pointID){
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
        if($this->c_pointID == $p_ID){
            $is_id = true;
        }

        return $is_id;
     }

}
?>
