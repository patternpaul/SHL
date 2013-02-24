<?php

/**
 * NAME:    pointCollection.php
 * AUTHOR:  Paul Everton
 * DATE:    Feb 18, 2011
 * DESCRIPTION: Collection of Point Objects
 */

//requires



class requestCollection extends collection {
    //class variables
    public $sql_call;

    const base_sql = "SELECT r.RequestID FROM Request AS r";


    //Constructors
    function requestCollection(){
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
        $pointCreated;

        //database connection
        $d = new db(0);

        //fetch the data
        $data = $d->fetch($this->sql_call);

        //fill the data
        foreach($data as $row) {
            //create the request
            $requestCreated = new request($row['RequestID']);
            //load the request
            $requestCreated->load();

            //add the point to the collection
            $this->add($requestCreated);
        }

    }
}
?>