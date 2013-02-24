<?php

/**
 * NAME:    about.php
 * AUTHOR:  Paul Everton
 * DATE:    July 21, 2011
 * DESCRIPTION: Object describing a about
 */

//requires



class about extends object {
    //class variables
    private $c_aboutID;
    private $c_dateCreated;
    private $c_content;
    private $c_poster;
    private $c_posterObject;

    //Constructors
    function about(){
        //set the class variable
        $this->c_aboutID = 0;
        $this->c_dateCreated = strftime("%m/%d/%Y  %H:%M:%S");
        $this->c_content = "";
        if(isLoggedIn()){
            $this->c_poster = $_SESSION['user']->getPlayerID();
        }else{
            $this->c_poster = 0;
        }

        $this->setObjectID($this->c_aboutID);


        $this->load();

    }



   /*
     * NAME:    load
     * PARAMS:  N/A
     * DESC:    Loads the game based off the game ID
     *
     */
    public function load(){
        //variable declaration
        $la_params = array();
        $ls_sql = '
            SELECT  a.AboutID,
                    a.DateCreated,
                    a.Content,
                    a.Poster
            FROM    About AS a
            ORDER BY a.DateCreated DESC LIMIT 1';
        

        //querry the DB
        $data = DBFac::getDB()->sql($ls_sql, $la_params);

        //fill the data
        foreach($data as $row) {
            $this->c_content = $row['Content'];
        }
    }


   /*
     * NAME:    loadForDisplay
     * PARAMS:  N/A
     * DESC:    Loads the game based off the game ID
     *
     */
    public function loadForDisplay(){
        //load the request bassed off the ID

        //database connection
        $d = new db(0);

        //fetch the data
        $data = $d->fetch("
            SELECT  a.AboutID,
                    a.DateCreated,
                    a.Content,
                    a.Poster
            FROM    About AS a
            ORDER BY a.DateCreated DESC LIMIT 1 ");

        //fill the data
        foreach($data as $row) {
            $this->c_content = $row['Content'];
            $this->c_poster = $row['Poster'];
            $this->c_dateCreated = $row['DateCreated'];
        }
    }

    /*
     * NAME:    insert
     * PARAMS:  N/A
     * DESC:    inserts a new post
     *
     */
    public function insert(){
        //check to ensure no errors occured
        if(!$this->hasError()){

            //database connection
            $d = new db(0);


            //update the data
            $data = $d->exec("
                INSERT INTO  About (DateCreated, Content, Poster)
                VALUES (" . db::fmt($this->c_dateCreated,0) . ",
                        " . db::fmt($this->c_content,0) . ",
                        " . db::fmt($this->c_poster,1) . ")");

            //set the id inserted
            $this->c_aboutID = $d->last_id;

            //indicate that the game was inserted
            $this->addDBMessage("About Edited", "About not Edited! Database Error!", $data, $d);

        }
    }


   /*
     * NAME:    getPosterObj
     * PARAMS:  N/A
     * DESC:    gets the poster object
     *
     */
    public function getPosterObj(){
        //check to see if the collection has been created
        if(!isset($this->c_posterObject)){
            //create the collection
            $this->c_posterObject = new player($this->c_poster);
        }

        //return the team white collection
        return $this->c_posterObject;
    }


     /*
     * NAME:    getPoster
     * PARAMS:  N/A
     * DESC:    returns the request poster ID
     */
    public function getPoster(){
        //return the value
        return $this->c_poster;
    }

     /*
     * NAME:    setPoster
     * PARAMS:  $p_passedVal = the value to set
     * DESC:    set the request status
     */
    public function setPoster($p_passedVal){
        if(is_numeric($p_passedVal)){
            //set the value
            $this->c_poster = $p_passedVal;
        } else {
            //status, add error message
            $this->errorOccured();
            $this->addMessage("The Poster must be a numeric.");
        }
    }




  


     /*
     * NAME:    getDateCreated
     * PARAMS:  N/A
     * DESC:    returns the request creation date
     */
    public function getDateCreated(){
        //variable declaration
        $timeStampVal = strtotime($this->c_dateCreated);
        $formatedTimeVal = strftime("%m/%d/%Y %H:%M:%S",$timeStampVal);

        //return the value
        return $formatedTimeVal;
    }

     /*
     * NAME:    setDateCreated
     * PARAMS:  $p_passedVal = the value to set
     * DESC:    set the request creation data
     */
    public function setDateCreated($p_passedVal){
        //set the value
        $timeStampVal = strtotime($p_passedVal);
        $formatedTimeVal = strftime("%Y-%m-%d %H:%M:%S",$timeStampVal);

        $this->c_dateCreated = $formatedTimeVal;
    }





    /*
     * NAME:    setContent
     * PARAMS:  $p_val = the value to set
     * DESC:    sets the request's content
     */
    public function setContent($p_val){
        if(strlen($p_val) > 0){
            //set the class variable
            $this->c_content = $p_val;
        }else{
            //Title is invalid, set error and error message
            $this->errorOccured();
            $this->addMessage("Content is required.");
        }
    }

    /*
     * NAME:    getContent
     * PARAMS:  N/A
     * DESC:    returns the request content
     */
    public function getContent(){
        //return class variable
        return $this->c_content;
    }

    /*
     * NAME:    getDisplayContent
     * PARAMS:  N/A
     * DESC:    returns the request content for display
     */
    public function getDisplayContent(){
        //variable declaration
        $returnContent = str_replace("\r", "<br />", $this->c_content);
        $returnContent = str_replace("\n", "<br />", $returnContent);
        //return class variable
        return $returnContent;
    }







}
?>
