<?php
/**
 * NAME:    user.php
 * AUTHOR:  Paul Everton
 * DATE:    Feb 22, 2011
 * DESCRIPTION: Object describing a user
 */

class user extends player{
    //class variables
    private $c_userName;
    private $c_password;
    private $c_passwordConfirm;
    private $c_passwordChange;
    private $c_access;
    private $c_saltString;

    
    //constructor
    function user(){
        //set class variables
        $this->c_userName = "";
        $this->c_password = "";
        $this->c_access = "";
        $this->c_saltString = "shl";
        $this->c_passwordChange = false;
        $this->c_facebookAccess = "";
        $this->c_facebookID = "";
        //call constructor of parent
        parent::__construct(0);
    }





     /*
     * NAME:    setFacebookID
     * PARAMS:  $p_val = the value to set
     * DESC:    sets the facebook id token
     */
    public function setFacebookID($p_val){
            //set the class variable
            $this->c_facebookID = $p_val;
    }


    /*
     * NAME:    setFacebookAccess
     * PARAMS:  $p_val = the value to set
     * DESC:    sets the facebook access token
     */
    public function setFacebookAccess($p_val){
            //set the class variable
            $this->c_facebookAccess = $p_val;
    }



    /*
     * NAME:    setAccess
     * PARAMS:  $p_val = the value to set
     * DESC:    sets the player's last name
     */
    public function setAccess($p_val){
        //set the value
        if(is_numeric($p_val)){
            if($p_val > 0){
                $this->c_access = $p_val;
            }else{
                //access level is wrong, add error message
                $this->errorOccured();
                $this->addMessage("The Access Level must be larger or equal to 0.");

            }
        }  else {
            //access level is wrong, add error message
            $this->errorOccured();
            $this->addMessage("The Access Level must be numeric.");
        }
    }

    /*
     * NAME:    getAccess
     * PARAMS:  N/A
     * DESC:    returns the player's access level
     */
    public function getAccess(){
        //return class variable
        return $this->c_access;
    }

    

    /*
     * NAME:    setUserName
     * PARAMS:  $p_val = the value to set
     * DESC:    sets the username
     */
    public function setUserName($p_val){
            //set the class variable
            $this->c_userName = $p_val;
    }

    /*
     * NAME:    getUserName
     * PARAMS:  N/A
     * DESC:    returns the username
     */
    public function getUserName(){
        //return class variable
        return $this->c_userName;
    }

    /*
     * NAME:    setPassword
     * PARAMS:  $p_val = the value to set
     * DESC:    sets the password
     */
    public function setPassword($p_val){
        //set the class variable.
        if(strlen($p_val) > 0){
            $this->c_passwordChange = true;
            //md5 hash the password with a salt string
            $this->c_password = $this->getHash($p_val);
        }
    }
    /*
     * NAME:    setPasswordConfirm
     * PARAMS:  $p_val = the value to set
     * DESC:    sets the password confirm
     */
    public function setPasswordConfirm($p_val){
        //set the class variable.

        //md5 hash the password with a salt string
        $this->c_passwordConfirm = $this->getHash($p_val);

    }

     /*
     * NAME:    getHash
     * PARAMS:  $p_val = the value to hash
     * DESC:    hash the string with a salt
     */
    public function getHash($p_val){
        return md5($this->c_saltString . $p_val);
    }


    /*
     * NAME:    getPassword
     * PARAMS:  N/A
     * DESC:    returns the password
     */
    public function getPassword(){
        //return class variable
        return $this->c_password;
    }

    /*
     * NAME:    getPasswordConfirm
     * PARAMS:  N/A
     * DESC:    returns the password
     */
    public function getPasswordConfirm(){

        //return class variable
        return $this->c_passwordConfirm;
    }

    /*
     * NAME:    checkPasswordConfirm
     * PARAMS:  N/A
     * DESC:    checks to see if the user has confirmed their password
     */
    public function checkPasswordConfirm(){

        if($this->c_passwordChange){
            //password has changed
            if($this->getPasswordConfirm() != $this->getPassword()){
                //password confirm is not the same, throw error
                $this->errorOccured();
                $this->addMessage("The password and confirmed password are not the same.");
            }
        }


    }

    public function login($p_userName, $p_password){
        //variable declaration
        $returnLog = false;

        $passHash = $this->getHash($p_password);
        //$passHash = $p_password;

        //database connection
        $d = new db(0);

        //fetch the data
        $data = $d->fetch("
            SELECT  p.PlayerID
            FROM    Player AS p
            WHERE   p.UserName = " . db::fmt($p_userName,0) .
            " AND   p.Password = " . db::fmt($passHash,0));


        if(count($data) == 1){
            //fill the data
            foreach($data as $row) {
                $this->setPlayerID($row['PlayerID']);
                $this->load();
                $returnLog = true;
            }
        }else{
            $this->errorOccured();
            $this->addMessage("The username and/or password are incorrect.");
        }

        return $returnLog;
    }


    /*
     * NAME:    load
     * PARAMS:  N/A
     * DESC:    Loads the user based off the player ID
     *
     */
    public function load(){

        //database connection
        $d = new db(0);

        //fetch the data
        $data1 = $d->fetch("
            SELECT  p.UserName, p.Password, p.Access, p.FacebookAccess, p.FacebookID
            FROM    Player AS p
            WHERE   p.PlayerID = " . db::fmt($this->getPlayerID(),1));


        //fill the data
        foreach($data1 as $row) {
            $this->c_password = $row['Password'];
            $this->c_userName = $row['UserName'];
            $this->c_access = $row['Access'];

        }

        //call the parent load
        parent::load();
    }




    /*
     * NAME:    update
     * PARAMS:  N/A
     * DESC:    updates a player
     *
     */
    public function update(){

        //check the password confirm
        $this->checkPasswordConfirm();


        //check to ensure no errors occured
        if(!$this->hasError()){

            //call the player class to update
            parent::update();


            //database connection
            $d = new db(0);

            if(trim($this->c_facebookAccess) != "" && trim($this->c_facebookID) != ""){
                //update the data
                $data = $d->exec("
                    UPDATE  Player
                    SET     UserName = " . db::fmt($this->c_userName,0) . ",
                            Password = " . db::fmt($this->c_password,0) . ",
                            FacebookAccess = " . db::fmt($this->c_facebookAccess,0) . ",
                            FacebookID = " . db::fmt($this->c_facebookID,0) . "
                    WHERE   PlayerID = " . db::fmt($this->getPlayerID(),1));
            }else{
                //update the data
                $data = $d->exec("
                    UPDATE  Player
                    SET     UserName = " . db::fmt($this->c_userName,0) . ",
                            Password = " . db::fmt($this->c_password,0) . "
                    WHERE   PlayerID = " . db::fmt($this->getPlayerID(),1));
            }

            //$this->addDBMessage("User Updated", "User not updated! Database Error!", $data, $d);
        }
    }


    /*
     * NAME:    insert
     * PARAMS:  N/A
     * DESC:    inserts a player
     *
     */
    public function insert(){
        //check the password confirm
        $this->checkPasswordConfirm();

        //check to ensure no errors occured
        if(!$this->hasError()){
            //call the player class to insert
            parent::insert();

            //database connection
            $d = new db(0);

            //update the data
            $data = $d->exec("
                UPDATE  Player
                SET     UserName = " . db::fmt($this->c_userName,0) . ",
                        Password = " . db::fmt($this->c_password,0) . ",
                        FacebookAccess = " . db::fmt($this->c_facebookAccess,0) . "
                WHERE   PlayerID = " . db::fmt($this->getPlayerID(),1));
            
        }
    }







}
?>
