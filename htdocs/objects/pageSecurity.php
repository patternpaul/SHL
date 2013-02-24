<?php

/**
 * NAME:    pageSecurity.php
 * AUTHOR:  Paul Everton
 * DATE:    March 28, 2011
 * DESCRIPTION: Object describing a page security
 */

//requires



class pageSecurity extends object {
    //class variables
    private $c_pageSecurityID;
    private $c_page;
    private $c_securityLevel;
    private $c_playerID;
    private $sql_call;
    
    //Constructors
    function pageSecurity(){

    }


    /*
     * NAME:    hasAccessToPage
     * PARAMS:  N/A
     * DESC:    will indicate if current user should have access to page
     */
    public function hasAccessToPage(){
        //variable declaration
        $returnAccessCheck = true;
        $pageToCheck = $_SERVER['SCRIPT_NAME'];
        $playerID = default_get("playerid", 0);

        

        //load the data
        $this->getPageSecurityByPage($pageToCheck);

        //check to see if page is defined
        if(isset($this->c_securityLevel)){

            //check if user ID is required
            if($this->c_playerID == 1){
                //check if the ID does not belongs to the user
                if(userID() != $playerID){
                    //check to see if the logged on player has the proper access level
                    if(!hasAccessLevel($this->c_securityLevel)){
                        //does not have access level
                        $returnAccessCheck = false;
                    }
                }
            }elseif(!hasAccessLevel($this->c_securityLevel) && ($this->c_securityLevel >= 0)){
                //does not have access level
                $returnAccessCheck = false;
            }
        }else{
            //page not set, return false
            $returnAccessCheck = false;
        }

        return $returnAccessCheck;
    }




    /*
     * NAME:    getPageSecurityByPage
     * PARAMS:  $p_page = the page you want
     * DESC:    sets the SQL call to select page security base off the page
     */
    public function getPageSecurityByPage($p_page){
        $local_sql_call = "
            SELECT      PageSecurityID, Page, SecurityLevel, PlayerID
            FROM        PageSecurity
            WHERE       Page = " . db::fmt($p_page,0);
        $idToPass = 0;
        //database connection
        $d = new db(0);

        //fetch the data
        $data = $d->fetch($local_sql_call);


        //fill the data
        foreach($data as $row) {
            $idToPass = $row['PageSecurityID'];
        }
        //call the loading function
        $this->getPageSecurityByID($idToPass);
    }


    /*
     * NAME:    getPageSecurityByID
     * PARAMS:  $p_pageID = the id you want
     * DESC:    sets the SQL call to select page security base off an ID
     */
    public function getPageSecurityByID($p_pageID){
        $this->c_pageSecurityID = $p_pageID;
        $this->setObjectID($p_pageID);
        $this->sql_call = "
            SELECT      PageSecurityID, Page, SecurityLevel, PlayerID
            FROM        PageSecurity
            WHERE       PageSecurityID = " . db::fmt($this->c_pageSecurityID,1);
        
        if($this->c_pageSecurityID > 0){
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
        $data = $d->fetch($this->sql_call);

        
        //fill the data
        foreach($data as $row) {
            $this->c_pageSecurityID = $row['PageSecurityID'];
            $this->c_page = $row['Page'];
            $this->c_securityLevel = $row['SecurityLevel'];
            $this->c_playerID = $row['PlayerID'];
        }
    }





      /*
     * NAME:    getPlayerID
     * PARAMS:  N/A
     * DESC:    returns the whether or not playerID check is required
     */
    public function getPlayerID(){
        //return the value
        return $this->c_playerID;
    }

     /*
     * NAME:    setPlayerID
     * PARAMS:  $p_passedVal = the value to set
     * DESC:    set whether or not playerID check is required
     */
    public function setPlayerID($p_passedVal){
        if(is_numeric($p_passedVal)){
            //set the value
            $this->c_playerID = $p_passedVal;
        } else {
            //game number is wrong, add error message
            $this->errorOccured();
            $this->addMessage("The Player ID must be a boolean numeric.");
        }
    }



     /*
     * NAME:    getSecurityLevel
     * PARAMS:  N/A
     * DESC:    returns the page security level
     */
    public function getSecurityLevel(){
        //return the value
        return $this->c_securityLevel;
    }

     /*
     * NAME:    setSecurityLevel
     * PARAMS:  $p_passedVal = the value to set
     * DESC:    set the page security level
     */
    public function setSecurityLevel($p_passedVal){
        if(is_numeric($p_passedVal)){
            //set the value
            $this->c_securityLevel = $p_passedVal;
        } else {
            //game number is wrong, add error message
            $this->errorOccured();
            $this->addMessage("The Page Security Level must be a numeric.");
        }
    }




      /*
     * NAME:    getPage
     * PARAMS:  N/A
     * DESC:    returns the page 
     */
    public function getPage(){
        //return the value
        return $this->c_page;
    }

     /*
     * NAME:    setPage
     * PARAMS:  $p_passedVal = the value to set
     * DESC:    set the page 
     */
    public function setPage($p_val){
        if(strlen($p_val) > 0){
            //set the class variable
            $this->c_page = $p_val;
        }else{
            //Title is invalid, set error and error message
            $this->errorOccured();
            $this->addMessage("The page is required");
        }
    }
    

     /*
     * NAME:    getPageSecurityID
     * PARAMS:  N/A
     * DESC:    returns the page security ID
     */
    public function getPageSecurityID(){
        //return the value
        return $this->c_pageSecurityID;
    }

     /*
     * NAME:    setPageSecurityID
     * PARAMS:  $p_passedVal = the value to set
     * DESC:    set the page security ID
     */
    public function setPageSecurityID($p_passedVal){
        if(is_numeric($p_passedVal)){
            //set the value
            $this->c_pageSecurityID = $p_passedVal;
        } else {
            //game number is wrong, add error message
            $this->errorOccured();
            $this->addMessage("The Page Security ID must be a numeric.");
        }
    }







//
//     /*
//     * NAME:    update
//     * PARAMS:  N/A
//     * DESC:    updates a post
//     *
//     */
//    public function update(){
//        //check to ensure no errors occured
//        if(!$this->hasError()){
//            //database connection
//            $d = new db(0);
//
//            //update the data
//            $data = $d->exec("
//                UPDATE  Post
//                SET     Title = " . db::fmt($this->c_title,0) . ",
//                        DateCreated = " . db::fmt($this->c_dateCreated,0) . ",
//                        Content = " . db::fmt($this->c_content,0) . ",
//                        Poster = " . db::fmt($this->c_poster,1) . "
//                WHERE   PostID = " . db::fmt($this->c_postID,1));
//
//            //indicate that the request was updated
//            $this->addDBMessage("Post Updated", "Post not Updated! Database Error!", $data, $d);
//        }
//    }
//
//
//    /*
//     * NAME:    insert
//     * PARAMS:  N/A
//     * DESC:    inserts a new post
//     *
//     */
//    public function insert(){
//        //check to ensure no errors occured
//        if(!$this->hasError()){
//
//            //database connection
//            $d = new db(0);
//
//
//            //update the data
//            $data = $d->exec("
//                INSERT INTO  Post (Title, DateCreated, Content, Poster)
//                VALUES (" . db::fmt($this->c_title,0) . ",
//                        " . db::fmt($this->c_dateCreated,0) . ",
//                        " . db::fmt($this->c_content,0) . ",
//                        " . db::fmt($this->c_poster,1) . ")");
//
//            //set the id inserted
//            $this->c_postID = $d->last_id;
//
//            //indicate that the game was inserted
//            $this->addDBMessage("Post Added", "Post not Added! Database Error!", $data, $d);
//
//        }
//    }





}
?>
