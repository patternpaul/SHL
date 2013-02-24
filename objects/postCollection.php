<?php

/**
 * NAME:    postCollection.php
 * AUTHOR:  Paul Everton
 * DATE:    Feb 18, 2011
 * DESCRIPTION: Collection of post Objects
 */

//requires



class postCollection extends collection {
    //class variables
    public $sql_call;

    const base_sql = "SELECT p.PostID FROM Post AS p ORDER BY p.DateCreated DESC";


    //Constructors
    function postCollection(){
        //initialize class variables
        parent::__construct();
        $this->sql_call = $this::base_sql;
    }

    /*
     * NAME:    getLastPosts
     * PARAMS:  $p_postCount = the last X posts to return
     * DESC:    sets the SQL call
     */
    public function getLastPosts($p_postCount){
        $this->sql_call = "
            SELECT p.PostID
            FROM Post AS p
            ORDER BY p.DateCreated DESC
            LIMIT " . $p_postCount;
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
            $postCreated = new post($row['PostID']);
            //load the request
            $postCreated->load();

            //add the point to the collection
            $this->add($postCreated);
        }

    }
}
?>