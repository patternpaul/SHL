<?php

/**
 * NAME:    post.php
 * AUTHOR:  Paul Everton
 * DATE:    March 28, 2011
 * DESCRIPTION: Object describing a post
 */

//requires



class post extends object {
    //class variables
    private $c_postID;
    private $c_title;
    private $c_dateCreated;
    private $c_content;
    private $c_poster;
    private $c_posterObject;

    //Constructors
    function post($p_postID){
        //set the class variable
        $this->c_postID = $p_postID;
        $this->c_title = "";
        $this->c_dateCreated = strftime("%m/%d/%Y  %H:%M:%S");
        $this->c_content = "";
        if(isLoggedIn()){
            $this->c_poster = $_SESSION['user']->getPlayerID();
        }else{
            $this->c_poster = 0;
        }

        $this->setObjectID($p_postID);

        //check to see if game should be loaded
        if($this->c_postID > 0){
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
            SELECT  p.PostID,
                    p.Title,
                    p.DateCreated,
                    p.Content,
                    p.Poster
            FROM    Post AS p
            WHERE   p.PostID = " . db::fmt($this->c_postID,1));

        //fill the data
        foreach($data as $row) {
            $this->c_postID = $row['PostID'];
            $this->c_title = $row['Title'];
            $this->c_dateCreated = $row['DateCreated'];
            $this->c_content = $row['Content'];
            $this->c_poster = $row['Poster'];
        }
    }


     /*
     * NAME:    update
     * PARAMS:  N/A
     * DESC:    updates a post
     *
     */
    public function update(){
        //check to ensure no errors occured
        if(!$this->hasError()){
            //database connection
            $d = new db(0);

            //update the data
            $data = $d->exec("
                UPDATE  Post
                SET     Title = " . db::fmt($this->c_title,0) . ",
                        DateCreated = " . db::fmt($this->c_dateCreated,0) . ",
                        Content = " . db::fmt($this->c_content,0) . ",
                        Poster = " . db::fmt($this->c_poster,1) . "
                WHERE   PostID = " . db::fmt($this->c_postID,1));

            //indicate that the request was updated
            $this->addDBMessage("Post Updated", "Post not Updated! Database Error!", $data, $d);
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
                INSERT INTO  Post (Title, DateCreated, Content, Poster)
                VALUES (" . db::fmt($this->c_title,0) . ",
                        " . db::fmt($this->c_dateCreated,0) . ",
                        " . db::fmt($this->c_content,0) . ",
                        " . db::fmt($this->c_poster,1) . ")");

            //set the id inserted
            $this->c_postID = $d->last_id;

            //indicate that the game was inserted
            $this->addDBMessage("Post Added", "Post not Added! Database Error!", $data, $d);

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
     * NAME:    getMonth
     * PARAMS:  N/A
     * DESC:    returns the request creation date month
     */
    public function getMonth(){
        //variable declaration
        $timeStampVal = strtotime($this->c_dateCreated);
        $formatedTimeVal = strftime("%b",$timeStampVal);

        //return the value
        return $formatedTimeVal;
    }
     /*
     * NAME:    getDay
     * PARAMS:  N/A
     * DESC:    returns the request creation date day
     */
    public function getDay(){
        //variable declaration
        $timeStampVal = strtotime($this->c_dateCreated);
        $formatedTimeVal = strftime("%e",$timeStampVal);

        //return the value
        return $formatedTimeVal;
    }
      /*
     * NAME:    getYear
     * PARAMS:  N/A
     * DESC:    returns the request creation date year
     */
    public function getYear(){
        //variable declaration
        $timeStampVal = strtotime($this->c_dateCreated);
        $formatedTimeVal = strftime("%Y",$timeStampVal);

        //return the value
        return $formatedTimeVal;
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






    /*
     * NAME:    setTitle
     * PARAMS:  $p_val = the value to set
     * DESC:    sets the request's title
     */
    public function setTitle($p_val){
        if(strlen($p_val) > 0){
            //set the class variable
            $this->c_title = $p_val;
        }else{
            //Title is invalid, set error and error message
            $this->errorOccured();
            $this->addMessage("Title is required.");
        }
    }

    /*
     * NAME:    getTitle
     * PARAMS:  N/A
     * DESC:    returns the request title
     */
    public function getTitle(){
        //return class variable
        return $this->c_title;
    }



     /*
     * NAME:    getPostID
     * PARAMS:  N/A
     * DESC:    returns the post ID
     */
    public function getPostID(){
        //return the value
        return $this->c_postID;
    }

     /*
     * NAME:    setPostID
     * PARAMS:  $p_passedVal = the value to set
     * DESC:    set the post ID
     */
    public function setPostID($p_passedVal){
        if(is_numeric($p_passedVal)){
            //set the value
            $this->c_postID = $p_passedVal;
        } else {
            //game number is wrong, add error message
            $this->errorOccured();
            $this->addMessage("The Post ID must be a numeric.");
        }
    }





}
?>
