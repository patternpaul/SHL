<?php

/**
 * NAME:    request.php
 * AUTHOR:  Paul Everton
 * DATE:    March 28, 2011
 * DESCRIPTION: Object describing a request
 */

//requires



class request extends object {
    //class variables
    private $c_requestID;
    private $c_title;
    private $c_dateCreated;
    private $c_content;
    private $c_priority;
    private $c_status;
    private $c_poster;
    private $c_posterObject;
    public $c_priorityArr;
    public $c_statusArr;
    
    //Constructors
    function request($p_requestID){
        //set the class variable
        $this->c_requestID = $p_requestID;
        $this->c_title = "";
        $this->c_dateCreated = strftime("%m/%d/%Y");
        $this->c_content = "";
        $this->c_priority = 0;
        $this->c_status = 0;
        if(isLoggedIn()){
            $this->c_poster = $_SESSION['user']->getPlayerID();
        }else{
            $this->c_poster = 0;
        }
        
        //set up arrays
        $this->c_priorityArr = array();
        $this->c_priorityArr[0] = "LOW";
        $this->c_priorityArr[1] = "MEDIUM";
        $this->c_priorityArr[2] = "HIGH";

        $this->c_statusArr = array();
        $this->c_statusArr[0] = "NEW";
        $this->c_statusArr[1] = "DISCARDED";
        $this->c_statusArr[2] = "IN PROGRESS";
        $this->c_statusArr[3] = "COMPLETED";

        $this->setObjectID($p_requestID);

        //check to see if game should be loaded
        if($this->c_requestID > 0){
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
            SELECT  r.RequestID,
                    r.Title,
                    r.DateCreated,
                    r.Content,
                    r.Priority,
                    r.Status,
                    r.Poster
            FROM    Request AS r
            WHERE   r.RequestID = " . db::fmt($this->c_requestID,1));

        //fill the data
        foreach($data as $row) {
            $this->c_requestID = $row['RequestID'];
            $this->c_title = $row['Title'];
            $this->c_dateCreated = $row['DateCreated'];
            $this->c_content = $row['Content'];
            $this->c_priority = $row['Priority'];
            $this->c_status = $row['Status'];
            $this->c_poster = $row['Poster'];
        }
    }


     /*
     * NAME:    update
     * PARAMS:  N/A
     * DESC:    updates a request
     *
     */
    public function update(){
        //check to ensure no errors occured
        if(!$this->hasError()){
            //database connection
            $d = new db(0);

            //update the data
            $data = $d->exec("
                UPDATE  Request
                SET     Title = " . db::fmt($this->c_title,0) . ",
                        DateCreated = " . db::fmt($this->c_dateCreated,0) . ",
                        Content = " . db::fmt($this->c_content,0) . ",
                        Priority = " . db::fmt($this->c_priority,1) . ",
                        Status = " . db::fmt($this->c_status,1) . ",
                        Poster = " . db::fmt($this->c_poster,1) . "
                WHERE   RequestID = " . db::fmt($this->c_requestID,1));

            //indicate that the request was updated
            $this->addDBMessage("Request Updated", "Request not Updated! Database Error!", $data, $d);
        }
    }


    /*
     * NAME:    insert
     * PARAMS:  N/A
     * DESC:    inserts a new request
     *
     */
    public function insert(){
        //check to ensure no errors occured
        if(!$this->hasError()){

            //database connection
            $d = new db(0);

            
            //update the data
            $data = $d->exec("
                INSERT INTO  Request (Title, DateCreated, Content, Priority, Status, Poster)
                VALUES (" . db::fmt($this->c_title,0) . ",
                        " . db::fmt($this->c_dateCreated,0) . ",
                        " . db::fmt($this->c_content,0) . ",
                        " . db::fmt($this->c_priority,1) . ",
                        " . db::fmt($this->c_status,1) . ",
                        " . db::fmt($this->c_poster,1) . ")");

            //set the id inserted
            $this->c_requestID = $d->last_id;

            //indicate that the game was inserted
            $this->addDBMessage("Request Added", "Request not Added! Database Error!", $data, $d);

            if(!$d->hasError()){
                //no error, send email
                $to      = 'pattern.paul@gmail.com';
                $subject = 'NEW REQUEST: ' . $this->c_title;
                $headers = 'From: request@shl-wpg.ca' . "\r\n" .
                    'Reply-To: pattern.paul@gmail.com' . "\r\n" .
                    'X-Mailer: PHP/' . phpversion();
                $priority = $this->getPriorityText();
                $message = "Title: $this->c_title \r\n Priority: $priority \r\nContent: " . $this->c_content;

                mail($to, $subject, $message, $headers);

            }



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
     * NAME:    getStatus
     * PARAMS:  N/A
     * DESC:    returns the request status
     */
    public function getStatus(){
        //return the value
        return $this->c_status;
    }

     /*
     * NAME:    getStatusText
     * PARAMS:  N/A
     * DESC:    returns the request status text
     */
    public function getStatusText(){
        //return the value
        return $this->c_statusArr[$this->c_status];
    }

     /*
     * NAME:    setStatus
     * PARAMS:  $p_passedVal = the value to set
     * DESC:    set the request status
     */
    public function setStatus($p_passedVal){
        if(is_numeric($p_passedVal)){
            //set the value
            $this->c_status = $p_passedVal;
        } else {
            //status, add error message
            $this->errorOccured();
            $this->addMessage("The Status must be a numeric.");
        }
    }




     /*
     * NAME:    getPriority
     * PARAMS:  N/A
     * DESC:    returns the request priority
     */
    public function getPriority(){
        //return the value
        return $this->c_priority;
    }

     /*
     * NAME:    getPriorityText
     * PARAMS:  N/A
     * DESC:    returns the request priority text
     */
    public function getPriorityText(){
        //return the value
        return $this->c_priorityArr[$this->c_priority];
    }


     /*
     * NAME:    setPriority
     * PARAMS:  $p_passedVal = the value to set
     * DESC:    set the request priority
     */
    public function setPriority($p_passedVal){
        if(is_numeric($p_passedVal)){
            //set the value
            $this->c_priority = $p_passedVal;
        } else {
            //game number is wrong, add error message
            $this->errorOccured();
            $this->addMessage("The Priority must be a numeric.");
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
        $formatedTimeVal = strftime("%m/%d/%Y",$timeStampVal);

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
        $formatedTimeVal = strftime("%Y-%m-%d",$timeStampVal);

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
     * NAME:    getRequestID
     * PARAMS:  N/A
     * DESC:    returns the request ID
     */
    public function getRequestID(){
        //return the value
        return $this->c_requestID;
    }

     /*
     * NAME:    setRequestID
     * PARAMS:  $p_passedVal = the value to set
     * DESC:    set the request ID
     */
    public function setRequestID($p_passedVal){
        if(is_numeric($p_passedVal)){
            //set the value
            $this->c_requestID = $p_passedVal;
        } else {
            //game number is wrong, add error message
            $this->errorOccured();
            $this->addMessage("The Request ID must be a numeric.");
        }
    }





}
?>
