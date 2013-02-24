<?php

/**
 * NAME:    record.php
 * AUTHOR:  Paul Everton
 * DATE:    August 30, 2011
 * DESCRIPTION: Collection of record holder Objects
 */

//requires



class record extends collection {
    //class variables
    public $sql_call;
    const base_sql = "SELECT RH.RecordHolderID FROM RecordHolder AS RH WHERE RH.RecordID = ";
    public $c_name;
    public $c_recordID;
    //Constructors
    function record($p_recordID){
        //initialize class variables
        parent::__construct();
        $this->sql_call = $this::base_sql . db::fmt($p_recordID,1);
        $this->c_name = "";
        $this->c_recordID = $p_recordID;
    }



    /*
     * NAME:    load
     * PARAMS:  N/A
     * DESC:    loads the collection based off the SQL call
     */
    public function load(){
        //variable declaration
        $recordHolderCreated;

        //database connection
        $d = new db(0);
        
        //fetch the record name
        $data = $d->fetch(
                "SELECT R.Name FROM Record AS R WHERE R.RecordID = " . db::fmt($this->c_recordID,1) 
                );
        
        foreach($data as $row) {
            $this->c_name = $row['Name'];
            
        }
        //fetch the data
        $data = $d->fetch($this->sql_call);

        //fill the data
        foreach($data as $row) {
            //create the player
            $recordHolderCreated = new recordHolder($row['RecordHolderID']);
            
            //add the record holder to the collection
            $this->add($recordHolderCreated);
        }
    }

    /*
     * NAME:    loadPlayerRecords
     * PARAMS:  N/A
     * DESC:    loads the collection based off the SQL call
     */
    public function loadPlayerRecords($p_playerID){
        //variable declaration
        $recordHolderCreated;

        //database connection
        $d = new db(0);

        //fetch the record name
        $data = $d->fetch(
                "SELECT R.Name
                    FROM Record AS R WHERE R.RecordID = " . db::fmt($this->c_recordID,1));

        foreach($data as $row) {
            $this->c_name = $row['Name'];

        }
        //fetch the data
        $data = $d->fetch("
                SELECT RH.RecordHolderID 
                FROM RecordHolder AS RH 
                INNER JOIN RecordHolderID AS RHI ON RH.RecordHolderID = RHI.RecordHolderID
                WHERE RH.RecordID = " . $this->c_recordID .
                " AND RHI.RepresentativeID = " . $p_playerID .
                " AND RHI.IDTypeID = 2");

        //fill the data
        foreach($data as $row) {
            //create the player
            $recordHolderCreated = new recordHolder($row['RecordHolderID']);

            //add the record holder to the collection
            $this->add($recordHolderCreated);
        }
    }


}
?>