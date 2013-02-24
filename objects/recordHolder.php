<?php

/**
 * NAME:    recordHolder.php
 * AUTHOR:  Paul Everton
 * DATE:    August 30, 2011
 * DESCRIPTION: Object describing a record holder
 */

//requires



class recordHolder extends object {
    //class variables
    private $c_recordHolderID;
    private $c_value;


    //Constructors
    function recordHolder($p_recordHolderID){
        //set the class variable
        $this->c_recordHolderID = $p_recordHolderID;
        $this->c_value = "";

        $this->setObjectID($p_recordHolderID);

        //check to see if record holder should be loaded
        if($this->c_recordHolderID > 0){
            //call the load function of the class
            $this->load();
        }
    }



   /*
     * NAME:    load
     * PARAMS:  N/A
     * DESC:    Loads the game based off the game ID
     *
     */
    public function load(){
        //load the request bassed off the ID

        //database connection
        $d = new db(0);

        //fetch the data
        $data = $d->fetch("
            SELECT  RH.RecordHolderID,
                    RH.Value
            FROM    RecordHolder AS RH
            WHERE   RH.RecordHolderID = " . db::fmt($this->c_recordHolderID,1));

        //fill the data
        foreach($data as $row) {
            $this->c_postID = $row['RecordHolderID'];
            $this->c_value = $row['Value'];
        }
    }


     /*
     * NAME:    update
     * PARAMS:  N/A
     * DESC:    updates a post
     *
     */
    public function update(){

    }


    /*
     * NAME:    insert
     * PARAMS:  N/A
     * DESC:    inserts a new post
     *
     */
    public function insert(){

    }




     /*
     * NAME:    getValue
     * PARAMS:  N/A
     * DESC:    returns the record holder value
     */
    public function getValue(){
        //return the value
        return $this->c_value;
    }

  


}
?>
