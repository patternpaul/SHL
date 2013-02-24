<?php

/**
 * NAME:    recordCollection.php
 * AUTHOR:  Paul Everton
 * DATE:    August 30, 2011
 * DESCRIPTION: Collection of record Objects
 */

//requires



class recordCollection extends collection {
    //class variables
    public $sql_call;
    const base_sql = "SELECT r.RecordID FROM Record AS r";
    
    //Constructors
    function recordCollection(){
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
        $recordCreated;

        //database connection
        $d = new db(0);

        //fetch the data
        $data = $d->fetch($this->sql_call);
        
        //fill the data
        foreach($data as $row) {
            //create the player
            $recordCreated = new record($row['RecordID']);
            $recordCreated->load();
            //add the record to the collection
            $this->add($recordCreated);
        }
    }

    /*
     * NAME:    load
     * PARAMS:  $p_playerID = player id
     * DESC:    loads the collection based off the SQL call
     */
    public function loadPlayerRecords($p_playerID){
        //variable declaration
        $recordCreated;
        $sqlCall = "
            SELECT DISTINCT r.RecordID
            FROM Record AS r
            INNER JOIN RecordHolder AS RH ON r.RecordID = RH.RecordID
            INNER JOIN RecordHolderID AS RHI ON RH.RecordHolderID = RHI.RecordHolderID
            WHERE RHI.RepresentativeID = " . $p_playerID .
            " AND RHI.IDTypeID = 2";
        //database connection
        $d = new db(0);

        //fetch the data
        $data = $d->fetch($sqlCall);

        //fill the data
        foreach($data as $row) {
            //create the player
            $recordCreated = new record($row['RecordID']);
            $recordCreated->loadPlayerRecords($p_playerID);
            //add the record to the collection
            $this->add($recordCreated);
        }
    }


}
?>