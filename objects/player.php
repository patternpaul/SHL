<?php

/**
 * NAME:    player.php
 * AUTHOR:  Paul Everton
 * DATE:    Jan 28, 2011
 * DESCRIPTION: Object describing a player
 */


class player extends object implements iComparable {
    //class variables


    private $c_firstName;
    private $c_lastName;
    private $c_playerID;
    private $c_email;
    private $c_phoneNumber;
    private $c_careerGames;
    private $c_careerRegularGames;
    private $c_careerPlayoffGames;
    private $c_careerGoalieRegularGames;
    private $c_careerGoaliePlayoffGames;
    private $c_currentSeasonGames;
    private $c_careerGoalieGames;
    private $c_careerGoalieGamesAsPlayer;
    private $c_height;
    private $c_shoots;
    private $c_favPro;
    private $c_favTeam;
    private $c_picture;
    //Constructors


    private $c_quickWins;
    private $c_quickLosses;
    private $c_quickGoals;
    private $c_quickAssists;
    private $c_quickGamePlayed;
    private $c_quickGWG;
    private $c_quickPlayerTeamScores;

    //goalie
    private $c_quickGA;
    private $c_quickGWins;
    private $c_quickGLosees;
    private $c_quickGGames;
    private $c_quickGGoals;
    private $c_quickGAssists;
    private $c_quickGShutOutCount;
    private $c_quickGTotalGameSeconds;
    private $c_facebookAccess;
    private $c_facebookID;
    function player($p_playerID){
        //set the class variable
        $this->c_playerID = $p_playerID;
        $this->c_firstName = "";
        $this->c_lastName = "";
        $this->c_email = "";
        $this->c_phoneNumber = "";

        
        $this->c_height = "";
        $this->c_shoots  = "";
        $this->c_favPro = "";
        $this->c_favTeam = "";   
        $this->c_picture = "";

        $this->c_quickWins = 0;
        $this->c_quickLosses = 0;
        $this->c_quickGoals = 0;
        $this->c_quickAssists = 0;
        $this->c_quickGamePlayed = 0;
        $this->c_quickGWG = 0;
        $this->c_quickPlayerTeamScores = 0;

        //goalie stats
        $this->c_quickGA = 0;
        $this->c_quickGWins = 0;
        $this->c_quickGLosees = 0;
        $this->c_quickGGames = 0;
        $this->c_quickGGoals = 0;
        $this->c_quickGAssists = 0;
        $this->c_quickGShutOutCount = 0;
        $this->c_quickGTotalGameSeconds = 0;

        //check to see if it's a new object
        if($this->c_playerID > 0){
            //call the load function of the class
            $this->load();
        }
    }

    /*
     * NAME:    getCurrentSeasonGames
     * PARAMS:  N/A
     * DESC:    Loads the player's current season games
     *
     */
    public function getCurrentSeasonGames(){
        //variable declaration
        $sql = "
            SELECT  MAX(g.SeasonID) AS MaxSeason
            FROM    Game AS g";

        //check to see if this season games have been created
        if(!isset($this->c_currentSeasonGames)){

            //database connection
            $d = new db(0);

            //fetch the data
            $data = $d->fetch($sql);

            //has not been filed, create it
            $this->c_currentSeasonGames = $this->getsSpecificSeasonGames($data[0]["MaxSeason"]);
            //load the season
            //$this->c_currentSeasonGames->load();
        }
        //return the current season
        return $this->c_currentSeasonGames;
    }

    /*
     * NAME:    getsSpecificSeasonGames
     * PARAMS:  $p_seasonID
     * DESC:    Loads the specific season's games
     *
     */
    public function getsSpecificSeasonGames($p_seasonID){


        //variable declaration
        $seasonCol;

        //has not been filed, create it
        $seasonCol = new playerSeason($p_seasonID, $this->c_playerID);
        //load the season
        $seasonCol->load();

        //return the current season
        return $seasonCol;
    }


    /*
     * NAME:    getCareerGames
     * PARAMS:  N/A
     * DESC:    Loads the player's career games
     *
     */
    public function getCareerGames(){
        //check to see if career games have been created
        if(!isset($this->c_careerGames)){
            //has not been filed, create it
            $this->c_careerGames = new career($this->c_playerID);
            //load the career
            $this->c_careerGames->load();
        }
        //return the career seasons
        return $this->c_careerGames;
    }





    /*
     * NAME:    getCareerRegularGames
     * PARAMS:  N/A
     * DESC:    Loads the player's regular season career games
     *
     */
    public function getCareerRegularGames(){
        //check to see if career games have been created
        if(!isset($this->c_careerRegularGames)){
            //has not been filed, create it
            $this->c_careerRegularGames = new career($this->c_playerID);

            //get regular games
            $this->c_careerRegularGames->getPlayoff(career::regularSeason);

            //load the career
            $this->c_careerRegularGames->load();
        }
        //return the career seasons
        return $this->c_careerRegularGames;
    }

        /*
     * NAME:    getCareerPlayoffGames
     * PARAMS:  N/A
     * DESC:    Loads the player's playoff season career games
     *
     */
    public function getCareerPlayoffGames(){
        //check to see if career games have been created
        if(!isset($this->c_careerPlayoffGames)){
            //has not been filed, create it
            $this->c_careerPlayoffGames = new career($this->c_playerID);

            //get playoff games
            $this->c_careerPlayoffGames->getPlayoff(career::playoff);

            //load the career
            $this->c_careerPlayoffGames->load();
        }
        //return the career seasons
        return $this->c_careerPlayoffGames;
    }


    /*
     * NAME:    getGOalieCareerGames
     * PARAMS:  N/A
     * DESC:    Loads the goalie's career games
     *
     */
    public function getGoalieCareerGames(){
        //check to see if career games have been created
        if(!isset($this->c_careerGoalieGames)){
            //has not been filed, create it
            $this->c_careerGoalieGames = new career($this->c_playerID);
            $this->c_careerGoalieGames->getGoalieCareer();
            //load the career
            $this->c_careerGoalieGames->load();
        }
        //return the career seasons
        return $this->c_careerGoalieGames;
    }


 
    /*
     * NAME:    getGOalieCareerGamesAsPlayer
     * PARAMS:  N/A
     * DESC:    Loads the goalie's career games returning teamplayers are the goalie
     *
     */
    public function getGoalieCareerGamesAsPlayer(){
        //check to see if career games have been created
        if(!isset($this->c_careerGoalieGamesAsPlayer)){
            //has not been filed, create it
            $this->c_careerGoalieGamesAsPlayer = new career($this->c_playerID);
            $this->c_careerGoalieGamesAsPlayer->getGoalieCareerAsPlayer();
            //load the career
            $this->c_careerGoalieGamesAsPlayer->load();
        }
        //return the career seasons
        return $this->c_careerGoalieGamesAsPlayer;
    }


     /*
     * NAME:    getCareerGoalieRegularGames
     * PARAMS:  N/A
     * DESC:    Loads the player's goalie regular season career games
     *
     */
    public function getCareerGoalieRegularGames(){
        //check to see if career games have been created
        if(!isset($this->c_careerGoalieRegularGames)){
            //has not been filed, create it
            $this->c_careerGoalieRegularGames = new career($this->c_playerID);

            //get regular games
            $this->c_careerGoalieRegularGames->getGoaliePlayoff(career::regularSeason);

            //load the career
            $this->c_careerGoalieRegularGames->load();
        }
        //return the career seasons
        return $this->c_careerGoalieRegularGames;
    }

        /*
     * NAME:    getCareerGoaliePlayoffGames
     * PARAMS:  N/A
     * DESC:    Loads the player's goalie playoff season career games
     *
     */
    public function getCareerGoaliePlayoffGames(){
        //check to see if career games have been created
        if(!isset($this->c_careerGoaliePlayoffGames)){
            //has not been filed, create it
            $this->c_careerGoaliePlayoffGames = new career($this->c_playerID);

            //get playoff games
            $this->c_careerGoaliePlayoffGames->getGoaliePlayoff(career::playoff);

            //load the career
            $this->c_careerGoaliePlayoffGames->load();
        }
        //return the career seasons
        return $this->c_careerGoaliePlayoffGames;
    }







    /*
     * NAME:    load
     * PARAMS:  N/A
     * DESC:    Loads the player based off the player ID
     *
     */
    public function load(){
        
        //database connection
        $d = new db(0);

        //fetch the data
        $data1 = $d->fetch("
            SELECT  p.Access, p.Email, p.FName, p.LName, p.Password, p.PhoneNumb,
                    p.PlayerID, p.UserName,
                    p.Height,
                    p.Shoots,
                    p.FavPro,
                    p.FavProTeam,
                    p.PlayerPicture,
                    p.FacebookAccess,
                    p.FacebookID
            FROM    Player AS p
            WHERE   p.PlayerID = " . db::fmt($this->c_playerID,1));


        //fill the data
        foreach($data1 as $row) {
            $this->c_firstName = $row['FName'];
            $this->c_lastName = $row['LName'];
            $this->c_email = $row['Email'];
            $this->c_playerID = $row['PlayerID'];
            //set the object ID
            $this->setObjectID($row['PlayerID']);
            $this->c_phoneNumber = $row['PhoneNumb'];

            $this->c_height = $row['Height'];
            $this->c_shoots  = $row['Shoots'];
            $this->c_favPro = $row['FavPro'];
            $this->c_favTeam = $row['FavProTeam'];    
            $this->c_picture = $row['PlayerPicture'];  
            $this->c_facebookAccess = $row['FacebookAccess'];
            $this->c_facebookID = $row['FacebookID'];
            
        }
    }
    /*
     * NAME:    hasFacebookAccess
     * PARAMS:  N/A
     * DESC:    returns the whether or not facebook has been connected
     */
    public function hasFacebookAccess(){
        //variable declaration
        $facebook_connected = false;
        if(trim($this->c_facebookID) != "" && trim($this->c_facebookAccess) != ""){
            $facebook_connected = true;
        }

        return $facebook_connected;
    }

    /*
     * NAME:    getFacebookID
     * PARAMS:  N/A
     * DESC:    returns the player's facebook ID
     */
    public function getFacebookID(){
        //return class variable
        return $this->c_facebookID;
    }


    /*
     * NAME:    getFacebookAccess
     * PARAMS:  N/A
     * DESC:    returns the player's facebook access
     */
    public function getFacebookAccess(){
        //return class variable
        return $this->c_facebookAccess;
    }

    /*
     * NAME:    update
     * PARAMS:  N/A
     * DESC:    updates a player
     *
     */
    public function update(){
        //check to ensure no errors occured
        if(!$this->hasError()){
            //database connection
            $d = new db(0);
            
            //update the data
            $data1 = $d->exec("
                UPDATE  Player
                SET     FName = " . db::fmt($this->c_firstName,0) . ",
                        LName = " . db::fmt($this->c_lastName,0) . ",
                        PhoneNumb = " . db::fmt($this->c_phoneNumber,0) . ",
                        Email = " . db::fmt($this->c_email,0) . ",
                        Height = " . db::fmt($this->c_height,0) . ",
                        Shoots = " . db::fmt($this->c_shoots,0) . ",
                        FavPro = " . db::fmt($this->c_favPro,0) . ",
                        FavProTeam = " . db::fmt($this->c_favTeam,0) . ",
                        PlayerPicture = " . db::fmt($this->c_picture,0) . "
                WHERE   PlayerID = " . db::fmt($this->c_playerID,1));

            

            $this->addDBMessage("Player Updated", "Player not updated! Database Error!", $data1, $d);

        }

    }


    /*
     * NAME:    insert
     * PARAMS:  N/A
     * DESC:    inserts a player
     *
     */
    public function insert(){
        //check to ensure no errors occured
        if(!$this->hasError()){
            //database connection
            $d = new db(0);

            //update the data
            $data = $d->exec("
                INSERT INTO  Player (FName, LName, PhoneNumb, Email)
                VALUES (" . db::fmt($this->c_firstName,0) . ",
                        " . db::fmt($this->c_lastName,0) . ",
                        " . db::fmt($this->c_phoneNumber,0) . ",
                        " . db::fmt($this->c_email,0) . ")" );

            //set the id inserted
            $this->c_playerID = $d->last_id;

            //indicate that the player was Added
            $this->addDBMessage("Player Added", "Player not added! Database Error!", $data, $d);
        }
    }


     /*
     * NAME:    compare
     * PARAMS:  $p_objectToCompare = passed object to compare
     * DESC:    will return true or false if its the same object
     *
     */
     public function compareTo($p_objectToCompare){
        //variable declaration
        $is_comparable = false;

        //compare the objects
        if($this->c_playerID == $p_objectToCompare->c_playerID){
            $is_comparable = true;
        }

        return $is_comparable;
     }

     /*
     * NAME:    isID
     * PARAMS:  $p_ID = passed ID to compare
     * DESC:    will return true or false if the object has that ID
     *
     */
     public function isID($p_ID){
        //variable declaration
        $is_id = false;

        //compare the objects
        if($this->c_playerID == $p_ID){
            $is_id = true;
        }

        return $is_id;
     }


  /*
  GET SET FUNCTIONS BELOW THIS POINT
  ------------------------------------------------------------------------------
  */



     
     /*
     * NAME:    getPicture
     * PARAMS:  N/A
     * DESC:    returns the player's picture
     *
     */
    public function getPicture(){
        if(trim($this->c_picture) != ""){
            $returnVal = "/playerimg/" . $this->c_picture;
        }else{
            $returnVal = "/img/shlLogo.jpg";
        }
        //return the value
        return $returnVal;
    }     
     
    

    /*
     * NAME:    setPicture
     * PARAMS:  $p_val = the value to set
     * DESC:    sets the player's ID
     */
    public function setPicture($p_val){
        //set the class variable
        $this->c_picture = $p_val;
    }

    
     
     /*
     * NAME:    getEmail
     * PARAMS:  N/A
     * DESC:    returns the player's email address
     *
     */
    public function getEmail(){
        //variable declaration
        $returnVal = "Login to view";

        //check to see if user has access to view the data
        if(hasAccessLevel(0)){
            //user has access, return value
            $returnVal = $this->c_email;
        }

        //return the value
        return $returnVal;
    }



     /*
     * NAME:    setEmail
     * PARAMS:  $p_email = the value to set
     * DESC:    sets the player's email address
     *
     */
    public function setEmail($p_email){
        //check that the email address is correct
        if(($this->checkEmailFormat($p_email)) || (trim($p_email == ""))){
            //email is correct, set value
            $this->c_email = trim($p_email);
        }else{
            //email is invalid, indicate error and add message
            $this->errorOccured();
            $this->addMessage("Email Address is invalid.");
        }
    }


     /*
     * NAME:    setPhoneNumb
     * PARAMS:  $p_phone = the value to set
     * DESC:    sets the player's phone number
     *
     */
    public function setPhoneNumber($p_phone){
        //check to ensure the phone number is in the correct format
        if($this->checkPhoneNumber($p_phone)){
            //phone number is valid, set class variable
            $this->c_phoneNumber = $p_phone;
        }else{
            //phone number is invalid, set error and error message
            $this->errorOccured();
            $this->addMessage("Phone Number is invalid. It can be blank or follow the following formats. " . $this->phoneNumberFormatMessage());
        }

    }

         /*
     * NAME:    getPhoneNumb
     * PARAMS:  N/A
     * DESC:    returns the player's phone number
     *
     */
    public function getPhoneNumber(){
        //variable declaration
        $returnVal = "Login to view";

        //check to see if user has access to view the data
        if(hasAccessLevel(0)){
            //user has access, return value
            $returnVal = $this->c_phoneNumber;
        }

        //return the value
        return $returnVal;
    }

    /*
     * NAME:    getShortName
     * PARAMS:  N/A
     * DESC:    returns the player's short name
     */
    public function getShortName(){
        //return the full name format
        $returnName = "";

        //hack for J.P robin since there is a Jeremie robin
        if($this->c_firstName == "J.P"){
            $returnName = substr($this->c_firstName,0,3) . ". " . $this->c_lastName;
        }else{
            $returnName = substr($this->c_firstName,0,1) . ". " . $this->c_lastName;
        }

        return $returnName;
    }


    /*
     * NAME:    getFullNameReverse
     * PARAMS:  N/A
     * DESC:    returns the player's full name in reverse
     */
    public function getFullNameReverse(){
        //return the full name format
        return $this->c_lastName . ", " . $this->c_firstName;
    }


    /*
     * NAME:    getFullName
     * PARAMS:  N/A
     * DESC:    returns the player's full name
     */
    public function getFullName(){
        //return the full name format
        return $this->c_firstName . " " . $this->c_lastName;
    }



    /*
     * NAME:    setPlayerID
     * PARAMS:  $p_val = the value to set
     * DESC:    sets the player's ID
     */
    public function setPlayerID($p_val){
        //set the class variable
        $this->c_playerID = $p_val;
    }

    /*
     * NAME:    getPlayerID
     * PARAMS:  N/A
     * DESC:    returns the player's ID
     */
    public function getPlayerID(){
        //return class variable
        return $this->c_playerID;
    }


    
    /*
     * NAME:    setPro
     * PARAMS:  $p_val = the value to set
     * DESC:    sets the player's favorite pro
     */
    public function setPro($p_val){
        //set the class variable
        $this->c_favPro = $p_val;
    }

    /*
     * NAME:    getPro
     * PARAMS:  N/A
     * DESC:    returns the player's favorite pro
     */
    public function getPro(){
        //return class variable
        return $this->c_favPro;
    }        
    
      /*
     * NAME:    setFavTeam
     * PARAMS:  $p_val = the value to set
     * DESC:    sets the player's favorite team
     */
    public function setFavTeam($p_val){
        //set the class variable
        $this->c_favTeam = $p_val;
    }

    /*
     * NAME:    getFavTeam
     * PARAMS:  N/A
     * DESC:    returns the player's favorite team
     */
    public function getFavTeam(){
        //return class variable
        return $this->c_favTeam;
    }      
    
    
    
    
     /*
     * NAME:    setShoots
     * PARAMS:  $p_val = the value to set
     * DESC:    sets the player's shooting side
     */
    public function setShoots($p_val){
        //set the class variable
        $this->c_shoots = $p_val;
    }

    /*
     * NAME:    getShoots
     * PARAMS:  N/A
     * DESC:    returns the player's shooting side
     */
    public function getShoots(){
        //return class variable
        return $this->c_shoots;
    }   
    
    
    
    /*
     * NAME:    setHeight
     * PARAMS:  $p_val = the value to set
     * DESC:    sets the player's height name
     */
    public function setHeight($p_val){
        //set the class variable
        $this->c_height = $p_val;
    }

    /*
     * NAME:    getHeight
     * PARAMS:  N/A
     * DESC:    returns the player's height
     */
    public function getHeight(){
        //return class variable
        return $this->c_height;
    }

    /*
     * NAME:    setLastName
     * PARAMS:  $p_val = the value to set
     * DESC:    sets the player's last name
     */
    public function setLastName($p_val){
        //set the class variable
        if(strlen($p_val) > 0){
            //set the class variable
            $this->c_lastName = $p_val;
        }else{
            //first name is invalid, set error and error message
            $this->errorOccured();
            $this->addMessage("Last Name is required.");
        }

    }

    /*
     * NAME:    getLastName
     * PARAMS:  N/A
     * DESC:    returns the player's last name
     */
    public function getLastName(){
        //return class variable
        return $this->c_lastName;
    }
    
    /*
     * NAME:    setFirstName
     * PARAMS:  $p_val = the value to set
     * DESC:    sets the player's first name
     */
    public function setFirstName($p_val){
        //set the class variable
        if(strlen($p_val) > 0){
            //set the class variable
            $this->c_firstName = $p_val;
        }else{
            //first name is invalid, set error and error message
            $this->errorOccured();
            $this->addMessage("First Name is required.");
        }

    }

    /*
     * NAME:    getFirstName
     * PARAMS:  N/A
     * DESC:    returns the player's first name
     */
    public function getFirstName(){
        //return class variable
        return $this->c_firstName;
    }



    /*
     *      QUICK LOAD FUNCTIONS
     *     private $c_quickWins;
        private $c_quickLosses;
        private $c_quickGoals;
        private $c_quickAssists;
     *     private $c_quickGamePlayed;
     */

    /*
     * NAME:    quickLoad
     * PARAMS:  N/A
     * DESC:    does a quick summary load of wins, losses, goals and assists
     */
    public function quickLoad($p_seasonID, $p_playoff){
        //variable declaration

        //variable declaration
        $sqlCall = "
            SELECT  SUM(Goals) AS Goals,
                    SUM(Assists) AS Assists,
                    SUM(Wins) AS Wins,
                    SUM(Losses) AS Losses,
                    SUM(WinningGoals) AS WinningGoals,
                    SUM(TeamScores) AS TeamScores
            FROM QuickPlayerDataTable AS QPD
            WHERE QPD.PlayerID = " . db::fmt($this->c_playerID,1);
        if($p_playoff >= 0){
           $sqlCall = $sqlCall  . " AND QPD.Playoff = " . db::fmt($p_playoff,1);
        }  
        
        if($p_seasonID > 0){
            $sqlCall = $sqlCall  . " AND QPD.SeasonID = " . db::fmt($p_seasonID,1);
        }

        $sqlCall = $sqlCall  . "
            GROUP BY QPD.PlayerID
        ";

        
        
//        
//        $sqlCall = "CALL getQuickPlayerData(" . db::fmt($this->c_playerID,1) . ", " .
//                db::fmt($p_playoff,1) . ", " .
//                db::fmt($p_seasonID,1) . ")";
        
        //database connection
        $d = new db(0);

        //fetch the data
        $data = $d->fetch($sqlCall);
        
        //echo $d->log;

        //fill the data
        foreach($data as $row) {
            $this->c_quickGoals = $row['Goals'];
            $this->c_quickAssists = $row['Assists'];
            $this->c_quickWins = $row['Wins'];
            $this->c_quickLosses = $row['Losses'];
            $this->c_quickGWG = $row['WinningGoals'];
            $this->c_quickGamePlayed = $row['Wins'] + $row['Losses'];
            $this->c_quickPlayerTeamScores = $row['TeamScores'];
        }


    }



    /*
     * NAME:    quickGoalieLoad
     * PARAMS:  N/A
     * DESC:    does a quick summary load of wins, losses, goals and assists for this goalie
     */
    public function quickGoalieLoad($p_seasonID, $p_playoff){
        //variable declaration

        //variable declaration
        $sqlCall = "
            SELECT  SUM(Goals) AS Goals,
                    SUM(Assists) AS Assists,
                    SUM(Wins) AS Wins,
                    SUM(Losses) AS Losses,
                    SUM(GoalsAgainst) AS GoalsAgainst,
                    SUM(ShutOutCount) AS ShutOutCount,
                    SUM(TotalGameSeconds) AS TotalGameSeconds
            FROM QuickGoalieDataTable AS QGD
            WHERE QGD.PlayerID = " . db::fmt($this->c_playerID,1);
        if($p_playoff >= 0){
           $sqlCall = $sqlCall  . " AND QGD.Playoff = " . db::fmt($p_playoff,1);
        }

        if($p_seasonID > 0){
            $sqlCall = $sqlCall  . " AND QGD.SeasonID = " . db::fmt($p_seasonID,1);
        }

        $sqlCall = $sqlCall  . "
            GROUP BY QGD.PlayerID
        ";




        //database connection
        $d = new db(0);

        //fetch the data
        $data = $d->fetch($sqlCall);

        //echo $d->log;

        //fill the data
        foreach($data as $row) {
            $this->c_quickGGoals = $row['Goals'];
            $this->c_quickGAssists = $row['Assists'];
            $this->c_quickGWins = $row['Wins'];
            $this->c_quickGLosees = $row['Losses'];
            $this->c_quickGA = $row['GoalsAgainst'];
            $this->c_quickGGames = $row['Wins'] + $row['Losses'];
            $this->c_quickGShutOutCount = $row['ShutOutCount'];
            $this->c_quickGTotalGameSeconds = $row['TotalGameSeconds'];
        }


    }

      /*
     * NAME:    getQuickPlayerTeamScoresCount
     * PARAMS:  N/A
     * DESC:    will get the count of player team scores
     */
    public function getQuickPlayerTeamScoresCount(){

        //return the shutout count
        return $this->c_quickPlayerTeamScores;
    }   


    /*
     * NAME:    getQuickGTotalGameSeconds
     * PARAMS:  N/A
     * DESC:    will get the total game seconds this goalie has played
     */
    public function getQuickGTotalGameSeconds(){

        //return the game seconds
        return $this->c_quickGTotalGameSeconds;
    }    
    
    
      /*
     * NAME:    getQuickGTotalGameMinutes
     * PARAMS:  N/A
     * DESC:    will get the total game minutes this goalie has played
     */
    public function getQuickGTotalGameMinutes(){

        //return the game minutes
        return $this->getQuickGTotalGameSeconds()/60;
    }    
    
       /*
     * NAME:    getQuickGGPM
     * PARAMS:  N/A
     * DESC:    will get the goals against per minute
     */
    public function getQuickGGPM(){
        //variable declaration
        $ga = $this->getQuickGACount();
        $gTotalMins = $this->getQuickGTotalGameMinutes();
        $gpm = 0;
        if($gTotalMins != 0){
            $gpm = $ga / $gTotalMins;
        }else{
            $gpm = 0;
        }
        //return the gpm
        return number_format($gpm, 3, '.', '');
    }       
    
    
    
    
    
    
    /*
     * NAME:    getQuickGShutOutCount
     * PARAMS:  N/A
     * DESC:    will get the count of shut outs for this goalie
     */
    public function getQuickGShutOutCount(){

        //return the goal count
        return $this->c_quickGShutOutCount;
    }



    /*
     * NAME:    getQuickGAssistsCount
     * PARAMS:  N/A
     * DESC:    will get the count of assist for this goalie
     */
    public function getQuickGAssistsCount(){

        //return the goal count
        return $this->c_quickGAssists;
    }

    /*
     * NAME:    getQuickGGoalsCount
     * PARAMS:  N/A
     * DESC:    will get the count of goals for this goalie
     */
    public function getQuickGGoalsCount(){

        //return the goal count
        return $this->c_quickGGoals;
    }

    
    
    
    /*
     * NAME:    getQuickGGoalsCount
     * PARAMS:  N/A
     * DESC:    will get the count of goals for this goalie
     */
    public function getQuickGPointCount(){

        //return the goal count
        return $this->getQuickGGoalsCount() + $this->getQuickGAssistsCount();
    }


    /*
     * NAME:    getQuickGGamesCount
     * PARAMS:  N/A
     * DESC:    will get the count of games for this player's goalie season
     */
    public function getQuickGGamesCount(){

        //return the goal count
        return $this->c_quickGGames;
    }



    /*
     * NAME:    getQuickGLossCount
     * PARAMS:  N/A
     * DESC:    will get the count of losses for this player's goalie season
     */
    public function getQuickGLossCount(){

        //return the goal count
        return $this->c_quickGLosees;
    }

    /*
     * NAME:    getQuickGWinCount
     * PARAMS:  N/A
     * DESC:    will get the count of wins for this goalie player
     */
    public function getQuickGWinCount(){
        //return the goal count
        return $this->c_quickGWins;
    }


    /*
     * NAME:    getQuickGWinLossCount
     * PARAMS:  N/A
     * DESC:    will get the goalie +/-
     */
    public function getQuickGWinLossCount(){
        //return the goal count
        return $this->getQuickGWinCount() - $this->getQuickGLossCount();
    }


    /*
     * NAME:    getQuickGoalieWinPercent
     * PARAMS:  N/A
     * DESC:    will get the goalie win %
     */
    public function getQuickGoalieWinPercent(){
        //variable declaration
        $wins = $this->getQuickGWinCount();
        $games = $this->getQuickGGamesCount();
        $gwp = 0;

        if($games != 0){
            //calculate the goals per game
            $gwp = $wins / $games * 100;
        }else{
            $gwp = 0;
        }
        //return the value
        return number_format($gwp, 2, '.', '');
    }

      /*
     * NAME:    getQuickTeamPointPercent
     * PARAMS:  N/A
     * DESC:    will get the percent of team points
     */
    public function getQuickTeamPointPercent(){
        //variable declaration
        $ln_goals = $this->getQuickGoalCount();
        $ln_teamPoints = $this->getQuickPlayerTeamScoresCount();
        $ln_tpp = 0;
        
        if($ln_teamPoints != 0){
            //calculate the team point percentage
            $ln_tpp = $ln_goals / $ln_teamPoints * 100;
        }
        //return the value
        return number_format($ln_tpp, 2, '.', '');
    }   



      /*
     * NAME:    getQuickGACount
     * PARAMS:  N/A
     * DESC:    will get the count of goals against average for the goalie
     */
    public function getQuickGACount(){

        //return the goal count
        return $this->c_quickGA;
    }


    /*
     * NAME:    getQuickGAA
     * PARAMS:  N/A
     * DESC:    will get the goals against average
     */
    public function getQuickGAA(){
        //variable declaration
        $ga = $this->getQuickGACount();
        $games = $this->getQuickGGamesCount();
        $gaa = 0;

        if($games != 0){
            //calculate the goals per game
            $gaa = $ga / $games;
        }else{
            $gaa = 0;
        }
        //return the value
        return number_format($gaa, 2, '.', '');
    }









    /*
     * NAME:    getQuickGWGCount
     * PARAMS:  N/A
     * DESC:    will get the count of game winning goals for this player's season
     */
    public function getQuickGWGCount(){

        //return the goal count
        return $this->c_quickGWG;
    }

    /*
     * NAME:    getQuickGWGPercent
     * PARAMS:  N/A
     * DESC:    will get the game winning goal percent
     */
    public function getQuickGWGPercent(){
        //variable declaration
        $gwg = $this->getQuickGWGCount();
        $games = $this->getQuickGameCount();
        $gpg = 0;

        if($games != 0){
            //calculate the goals per game
            $gpg = $gwg / $games * 100;
        }else{
            $gpg = 0;
        }
        //return the value
        return number_format($gpg, 2, '.', '');
    }




    /*
     * NAME:    getQuickLossCount
     * PARAMS:  N/A
     * DESC:    will get the count of losses for this player's season
     */
    public function getQuickLossCount(){

        //return the goal count
        return $this->c_quickLosses;
    }

    /*
     * NAME:    getQuickWinCount
     * PARAMS:  N/A
     * DESC:    will get the count of wins for this player
     */
    public function getQuickWinCount(){
        //return the goal count
        return $this->c_quickWins;
    }


     /*
     * NAME:    getQuickWinLoss
     * PARAMS:  N/A
     * DESC:    will get the quick +/-
     */
    public function getQuickWinLoss(){
        //variable declaration
        $winloss = $this->getQuickWinCount() - $this->getQuickLossCount();

        //return the value
        return $winloss;
    }
     /*
     * NAME:    getQuickWinLossPG
     * PARAMS:  N/A
     * DESC:    will get the win loss per game
     */
    public function getQuickWinLossPG(){
        //variable declaration
        $winloss = $this->getQuickWinLoss();
        $games = $this->getQuickGameCount();
        $gpg = 0;

        if($games != 0){
            //calculate the goals per game
            $gpg = $winloss / $games;
        }else{
            $gpg = 0;
        }
        //return the value
        return number_format($gpg, 2, '.', '');
    }


    /*
     * NAME:    getQuickWinPercent
     * PARAMS:  N/A
     * DESC:    will get the count of wins for this player
     */
    public function getQuickWinPercent(){
        //variable declaration
        $win = $this->getQuickWinCount();
        $games = $this->getQuickGameCount();
        $gpg = 0;

        if($games != 0){
            //calculate the goals per game
            $gpg = $win / $games * 100;
        }else{
            $gpg = 0;
        }
        //return the value
        return number_format($gpg, 2, '.', '');
    }




    /*
     * NAME:    getQuickGoalCount
     * PARAMS:  N/A
     * DESC:    will get the count of goals for this player's season
     */
    public function getQuickGoalCount(){

        //return the goal count
        return $this->c_quickGoals;
    }

    /*
     * NAME:    getQuickAssistCount
     * PARAMS:  N/A
     * DESC:    will get the count of assists for this player
     */
    public function getQuickAssistCount(){
        //return the goal count
        return $this->c_quickAssists;
    }

     /*
     * NAME:    getQuickPointCount
     * PARAMS:  N/A
     * DESC:    will get the points
     */
    public function getQuickPointCount(){
        //variable declaration
        $points = $this->getQuickAssistCount() + $this->getQuickGoalCount();

        //return the value
        return $points;
    }


     /*
     * NAME:    getQuickGameCount
     * PARAMS:  N/A
     * DESC:    will get the count of unique games
     */
    public function getQuickGameCount(){
        //return the goal count
        return $this->c_quickGamePlayed;
    }



     /*
     * NAME:    getQuickGPG
     * PARAMS:  N/A
     * DESC:    will get the goals per game calculation
     */
    public function getQuickGPG(){
        //variable declaration
        $goals = $this->getQuickGoalCount();
        $games = $this->getQuickGameCount();
        $gpg = 0;

        if($games > 0){
            //calculate the goals per game
            $gpg = $goals / $games;
        }else{
            $gpg = 0;
        }
        //return the value
        return number_format($gpg, 2, '.', '');
    }

     /*
     * NAME:    getQuickAPG
     * PARAMS:  N/A
     * DESC:    will get the assists per game calculation
     */
    public function getQuickAPG(){
        //variable declaration
        $assists = $this->getQuickAssistCount();
        $games = $this->getQuickGameCount();
        $apg = 0;

        if($games > 0){
            //calculate the goals per game
            $apg = $assists / $games;
        }else{
            $apg = 0;
        }

        //return the value
        return number_format($apg, 2, '.', '');
    }


     /*
     * NAME:    getQuickPPG
     * PARAMS:  N/A
     * DESC:    will get the points per game calculation
     */
    public function getQuickPPG(){
        //variable declaration
        $points = $this->getQuickAssistCount() + $this->getQuickGoalCount();
        $games = $this->getQuickGameCount();
        $ppg = 0;

        if($games > 0){
            //calculate the goals per game
            $ppg = $points / $games;
        }else{
            $ppg = 0;
        }

        //return the value
        return number_format($ppg, 2, '.', '');
    }








}
?>
