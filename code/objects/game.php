<?php

/**
 * NAME:    game.php
 * AUTHOR:  Paul Everton
 * DATE:    Feb 18, 2011
 * DESCRIPTION: Object describing a game
 */


class game extends object implements iComparable {
    //class variables
    private $c_gameID;
    private $c_gameDate;
    private $c_gameEnd;
    private $c_gameNum;
    private $c_gameStart;
    private $c_playoff;
    private $c_seasonID;
    private $c_previousSeasonID;
    private $c_teamWhite;
    private $c_teamBlack;
    private $c_whiteScores;
    private $c_blackScores;
    private $c_winner;
    private $c_gameSecondsTime;
    
    private $c_quickWhiteScores;
    private $c_quickBlackScores;
    

    /**
     * The Game constructor
     * @param int $p_gameID The game ID
     */
    function game($p_gameID){
        //set the class variable
        $this->c_gameID = $p_gameID;
        $this->c_previousSeasonID = -1;
        $this->setObjectID($p_gameID);
        
        //check to see if game should be loaded
        if($this->c_gameID > 0){
            //call the load function of the class
            $this->load();
        }
    }




    /**
     * Returns the latest game id
     * @return int The latest game ID 
     */
    public static function getNewestGame(){
        //variable declaration
        $newestGame = 1;
        $la_params = array();
        $ls_sql = '
            SELECT  g.GameID
            FROM    Game AS g
            ORDER BY g.SeasonID DESC, g.Playoff DESC, g.GameNum DESC';

        //fetch the data
        $data = DBFac::getDB()->sql($ls_sql, $la_params);

        $newestGame = $data[0]['GameID'];

        //return the max season
        return $newestGame;
    }
    
    /**
     * Returns the latest season
     * @return int the latest season
     */
    public static function getMaxSeason(){
        //variable declaration
        $maxSeason = 1;
        $la_params = array();
        $ls_sql = "
            SELECT  MAX(g.SeasonID) AS 'MaxSeason'
            FROM    Game AS g";
        
        //fetch the data
        $data = DBFac::getDB()->sql($ls_sql, $la_params);

        //fill the data
        foreach($data as $row) {
            $maxSeason = $row['MaxSeason'];
        }

        //return the max season
        return $maxSeason;
    }


    /**
     * Loads the object based off the SQL
     */
    public function load(){
        //variable declaration
        $la_params = array();
        $ls_sql = "
            SELECT  g.GameDate,
                    g.GameEnd,
                    g.GameID,
                    g.GameNum,
                    g.GameStart,
                    g.Playoff,
                    g.SeasonID,
                    gw.Winner,
                    gtl.GameSeconds,
                    qgs.BlackPoints,
                    qgs.WhitePoints
            FROM    Game AS g
            INNER JOIN QuickGameWinners AS gw ON g.GameID = gw.GameID
            INNER JOIN QuickGameTimeLength AS gtl ON g.GameID = gtl.GameID
            INNER JOIN QuickGameScores AS qgs ON g.GameID = qgs.GameID
            WHERE   g.GameID = :gameID";
        $la_params["gameID"] = $this->c_gameID;
        
        //get the data
        $data = DBFac::getDB()->sql($ls_sql, $la_params);

        //fill the data
        foreach($data as $row) {
            $this->c_gameID = $row['GameID'];
            $this->c_gameDate = $row['GameDate'];
            $this->c_gameEnd = $row['GameEnd'];
            $this->c_gameNum = $row['GameNum'];
            $this->c_gameStart = $row['GameStart'];
            $this->c_playoff = $row['Playoff'];
            $this->c_seasonID = $row['SeasonID'];
            $this->c_previousSeasonID = $row['SeasonID'];
            $this->c_winner = $row['Winner'];
            $this->c_gameSecondsTime = $row['GameSeconds'];
            $this->c_quickWhiteScores = $row['WhitePoints'];
            $this->c_quickBlackScores = $row['BlackPoints'];
        }
    }

    /**
     * Updates the Game
     */
    public function update(){
        //variable declaration
        $la_params = array();
        $ls_sql = "UPDATE  Game
                SET     GameDate = :gameDate,
                        GameEnd = :gameEnd,
                        GameNum = :gameNum,
                        GameStart = :gameStart,
                        Playoff = :gamePlayoff,
                        SeasonID = :seasonID,
                WHERE   GameID = :gameID";
        
        $la_params["gameDate"] = $this->c_gameDate;
        $la_params["gameEnd"] = $this->c_gameEnd;
        $la_params["gameNum"] = $this->c_gameNum;
        $la_params["gameStart"] = $this->c_gameStart;
        $la_params["gamePlayoff"] = $this->c_playoff;
        $la_params["seasonID"] = $this->c_seasonID;
        $la_params["gameID"] = $this->c_gameID;

            
        //check to ensure no errors occured
        if(!$this->hasError()){
            //validate to see if data should be entered
            $this->validate();
            
            //update
            DBFac::getDB()->sql($ls_sql, $la_params);

            //indicate that the game was updated
            $this->addMessage("Game Updated");
        }
    }


    /**
     * Validates to make sure the game has not already been added
     */
    public function validate(){
        //variable declaration
        $ls_sql = "";
        $la_params = array();
        //build the string
        $ls_sql = "
            SELECT  g.GameID
            FROM    Game AS g
            WHERE   SeasonID = :seasonID
            AND     GameNum = :gameNum
            AND     Playoff = :gamePlayoff";
        
        $la_params["gamePlayoff"] = $this->c_playoff;
        $la_params["seasonID"] = $this->c_seasonID;
        $la_params["gameNum"] = $this->c_gameNum;
        
        
        //do not include this game into the check
        if($this->c_gameID > 0){
            $sqlString = $sqlString . "
                AND GameID != :gameID";
            $la_params["gameID"] = $this->c_gameID;
        }

        //fetch the data
        $data = DBFac::getDB()->sql($ls_sql, $la_params);

        //check to see if any rows were returned
        if(count($data) > 0){
            //this game has already been created, throw error
            $this->errorOccured();
            $this->addMessage("This game has already been entered into SHL. Please modify this entry.");
        }
    }



    /**
     * Inserts a game
     */
    public function insert(){
        $ls_sql = "";
        $la_params = array();
        //build the string
        $ls_sql = "
            INSERT INTO  Game (GameDate, GameEnd, GameNum, GameStart, Playoff, SeasonID)
            VALUES (:gameDate,
                    :gameEnd,
                    :gameNum,
                    :gameStart,
                    :playoff,
                    :seasonID)";
        
        
        //check to ensure no errors occured
        if(!$this->hasError()){
            //validate to see if data should be entered
            $this->validate();
            
            //set the params
            $la_params["gameDate"] = $this->c_gameDate;
            $la_params["gameEnd"] = $this->c_gameEnd;
            $la_params["gameNum"] = $this->c_gameNum;
            $la_params["gameStart"] = $this->c_gameStart;
            $la_params["playoff"] = $this->c_playoff;
            $la_params["seasonID"] = $this->c_seasonID;

            //exec the querry on the DB
            DBFac::getDB()->sql($ls_sql, $la_params);

            //set the id inserted
            $this->c_gameID = DBFac::getDB()->pdo->lastinsertid();

            //indicate that the game was inserted
            $this->addMessage("Game Added");
        }
    }

    
    
    /**
     * Updates all summary data for the specific passed in season
     * @param int $p_seasonID the season id
     */
    public function UpdateMasterCall($p_seasonID){
        //variable declaration
        $ls_sql = "";
        $la_params = array();
        //build the string
        $ls_sql = "
            CALL masterUpdateProc(:seasonID)";
        
        //check to ensure no errors occured
        if(!$this->hasError()){
            $la_params["seasonID"] = $p_seasonID;
            //call the master update proc
            DBFac::getDB()->sql($ls_sql, $la_params);
        }
    }    


    /**
     * The full game update flow
     */
    public function fullGameUpdate(){
        //variable declaration
        $ls_sql = "";
        $la_params = array();

        
        //delete all points and players associated with the game
        //check to ensure no errors occured
        if(!$this->hasError()){
            //build the string
            $ls_sql = "
                DELETE FROM Point
                WHERE TeamPlayerID IN (
                    Select TeamPlayerID
                    FROM TeamPlayer
                    WHERE GameID = :gameID)";
            $la_params["gameID"] = $this->c_gameID;
            
            //Delete the points
            $data = DBFac::getDB()->sql($ls_sql, $la_params);
            

            //delete any team players related to the game
            $ls_sql = "
                DELETE FROM TeamPlayer
                WHERE GameID = :gameID)";
            $la_params = array();
            $la_params["gameID"] = $this->c_gameID;
            
            $data = DBFac::getDB()->sql($ls_sql, $la_params);


            //update the game
            $this->update();

            //insert white team collection
            $this->insertTeamCollection($this->getTeamWhite());
            //insert black team collection
            $this->insertTeamCollection($this->getTeamBlack());
            
           
            //Update 2 seasons if the game was changing seasons, if not only update 1
            if(($this->c_previousSeasonID != -1) && ($this->c_previousSeasonID != $this->c_seasonID)){
                $this->UpdateMasterCall($this->c_seasonID);
                $this->UpdateMasterCall($this->c_previousSeasonID);
            }else{
                $this->UpdateMasterCall($this->c_seasonID);
            }
            
            //refil the records
            $recordObj = new records();
            $recordObj->recordRefil();
        }
    }






    /*
     * NAME:    fullGameInsert
     * PARAMS:  N/A
     * DESC:    logic to do a full game insert
     *
     */
    public function fullGameInsert(){
        //insert the game
        $this->insert();
        //insert white team collection
        $this->insertTeamCollection($this->getTeamWhite());
        //insert black team collection
        $this->insertTeamCollection($this->getTeamBlack());
        
            //refresh the quick table
            //$this->UpdateQuickDataTable();
            //$this->UpdateQuickGoalieDataTable();
            
            $this->UpdateMasterCall($this->c_seasonID);
            
            $recordObj = new records();
            $recordObj->recordRefil();

    }


    /*
     * NAME:    insertTeamCollection
     * PARAMS:  $p_teamCollection
     * DESC:    logic to do insert a team collection
     *
     */
    public function insertTeamCollection($p_teamCollection){
        //variable declaration
        $teamCol;
        $teamPlayer;
        $pointCol;
        $point;
        
        $teamCol = $p_teamCollection;
        for ( $alpha = 0; $alpha < $teamCol->count(); $alpha += 1) {
            //get the team player
            $teamPlayer = $teamCol->get($alpha);
            //set the teamplayer game id
            $teamPlayer->c_gameID = $this->c_gameID;

            //insert the player
            $teamPlayer->insert();
            //get the team player points
            $pointCol = $teamPlayer->getTeamPlayerPoints();
            //loop over points to insert
            for ( $beta = 0; $beta < $pointCol->count(); $beta += 1) {
                $point = $pointCol->get($beta);
                //set the team player ID
                $point->c_teamPlayerID = $teamPlayer->c_teamPlayerID;
                //do the point insert
                $point->insert();
            }
        }
    }







        



   /*
     * NAME:    getTeamWhite
     * PARAMS:  N/A
     * DESC:    gets the white team player collection
     *
     */
    public function getTeamWhite(){
        //check to see if the collection has been created
        if(!isset($this->c_teamWhite)){
            //create the collection
            $this->c_teamWhite = $this->getTeamCollection(2);
        }

        //return the team white collection
        return $this->c_teamWhite;
    }

   /*
     * NAME:    setTeamWhite
     * PARAMS:  $p_passedTeam = team to set
     * DESC:    sets the white team player collection
     *
     */
    public function setTeamWhite($p_passedTeam){
        $this->c_teamWhite = $p_passedTeam;
    }

   /*
     * NAME:    getTeamBlack
     * PARAMS:  N/A
     * DESC:    gets the white team player collection
     *
     */
    public function getTeamBlack(){



        //check to see if the collection has been created
        if(!isset($this->c_teamBlack)){
            //create the collection
            $this->c_teamBlack = $this->getTeamCollection(1);
        }

        //return the team white collection
        return $this->c_teamBlack;
    }


   /*
     * NAME:    setTeamBlack
     * PARAMS:  $p_passedTeam = team to set
     * DESC:    sets the black team player collection
     *
     */
    public function setTeamBlack($p_passedTeam){
        $this->c_teamBlack = $p_passedTeam;
    }


   /*
     * NAME:    getTeamCollection
     * PARAMS:  $p_colorID =  the color of the team
     * DESC:    gets collection of team players for that color
     *
     */
    private function getTeamCollection($p_colorID){
        //variable declaration
        $returnCollection;

        //create the collection
        $returnCollection = new teamPlayerCollection();
        //give it the SQL call
        $returnCollection->getGameTeam($this->c_gameID, $p_colorID);
        //load the collection
        $returnCollection->load();
        

        //return the team white collection
        return $returnCollection;
    }



   /*
     * NAME:    getTeamScoreCollection
     * PARAMS:  $p_colorID =  the color of the team
     * DESC:    gets collection of scores for that color
     *
     */
    public function getTeamScoreCollection($p_colorID){
        //variable declaration
        $returnCollection;

        //create the collection
        $returnCollection = new scoreCollection($this->c_gameID, $p_colorID);
        
        //load the collection
        $returnCollection->load();


        //return the team white collection
        return $returnCollection;
    }



   /*
     * NAME:    getTeamBlackScores
     * PARAMS:  N/A
     * DESC:    gets the black team score collection
     *
     */
    public function getTeamBlackScores(){

        //check to see if the collection has been created
        if(!isset($this->c_blackScores)){
            //create the collection
            $this->c_blackScores = $this->getTeamScoreCollection(1);
        }

        //return the team white collection
        return $this->c_blackScores;
    }


   /*
     * NAME:    getTeamWhiteScores
     * PARAMS:  N/A
     * DESC:    gets the white team score collection
     *
     */
    public function getTeamWhiteScores(){
        //check to see if the collection has been created
        if(!isset($this->c_whiteScores)){
            //create the collection
            $this->c_whiteScores = $this->getTeamScoreCollection(2);
        }

        //return the team white collection
        return $this->c_whiteScores;
    }







    /*
     * NAME:    getWhiteScores
     * PARAMS:  N/A
     * DESC:    gets the white team scores
     *
     */
    public function getWhiteScores(){
        //check to see if the collection has been created
        if(!isset($this->c_whiteScores)){
            //create the collection
            $this->c_whiteScores = new scoreCollection($this->c_gameID,2);
            $this->c_whiteScores->load();
        }

        //return the team white score collection
        return $this->c_whiteScores;
    }

   /*
     * NAME:    getBlackScores
     * PARAMS:  N/A
     * DESC:    gets the black team scores
     *
     */
    public function getBlackScores(){
        //check to see if the collection has been created
        if(!isset($this->c_blackScores)){
            //create the collection
            $this->c_blackScores = new scoreCollection($this->c_gameID,1);
            $this->c_blackScores->load();

        }

        //return the team white collection
        return $this->c_blackScores;
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
        if($this->c_gameID == $p_objectToCompare->c_gameID){
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
        if($this->c_gameID == $p_ID){
            $is_id = true;
        }
        
        return $is_id;
     }

  /*
  GET SET FUNCTIONS BELOW THIS POINT
  ------------------------------------------------------------------------------
  */

     



      /*
     * NAME:    getWinner
     * PARAMS:  N/A
     * DESC:    returns the winner
     */
    public function getWinner(){
        //return the value
        return $this->c_winner;
    }




     /*
     * NAME:    getSeasonID
     * PARAMS:  N/A
     * DESC:    returns the season id
     */
    public function getSeasonID(){
        //return the value
        return $this->c_seasonID;
    }

     /*
     * NAME:    setSeasonID
     * PARAMS:  $p_passedVal = the value to set
     * DESC:    set the season id
     */
    public function setSeasonID($p_passedVal){
        //set the value
        if(is_numeric($p_passedVal)){
            if($p_passedVal > 0){
                $this->c_seasonID = $p_passedVal;
            }else{
                //season id is wrong, add error message
                $this->errorOccured();
                $this->addMessage("The Season ID must be larger than 0.");

            }
        }  else {
            //season id is wrong, add error message
            $this->errorOccured();
            $this->addMessage("The Season ID must be numeric.");
        }
        
    }


     /*
     * NAME:    getPlayoff
     * PARAMS:  N/A
     * DESC:    returns the playoff indicator
     */
    public function getPlayoff(){
        //return the value
        return $this->c_playoff;
    }

     /*
     * NAME:    setPlayoff
     * PARAMS:  $p_passedVal = the value to set
     * DESC:    set the playoff indicator
     */
    public function setPlayoff($p_passedVal){
        //set the value
        $this->c_playoff = $p_passedVal;
    }

    
    
    

      /*
     * NAME:    getGameSeconds
     * PARAMS:  N/A
     * DESC:    will get the count of seconds played
     */
    public function getGameSeconds(){

        //return the shutout count
        return $this->c_gameSecondsTime;
    }  
    
    
      /*
     * NAME:    getTotalGameMinutes
     * PARAMS:  N/A
     * DESC:    will get the total game minutes 
     */
    public function getTotalGameMinutes(){

        //return the game minutes
        return $this->getGameSeconds()/60;
    }    
     
    
    
    
     /*
     * NAME:    getGameStart
     * PARAMS:  N/A
     * DESC:    returns the game start
     */
    public function getGameStart(){
        //variable declaration
        $timeStampVal = strtotime($this->c_gameStart);
        $formatedTimeVal = strftime("%l:%M %p",$timeStampVal);
        //return the value
        return $formatedTimeVal;
    }

    
    /*
     * NAME:    getGameStartHour
     * PARAMS:  N/A
     * DESC:    returns the game Start hour value
     */
    public function getGameStartHour(){
        //return the value
        return $this->hourValue($this->c_gameStart);
    }

     /*
     * NAME:    getGameStartMinute
     * PARAMS:  N/A
     * DESC:    returns the game Start minute value
     */
    public function getGameStartMinute(){
        //return the value
        return $this->minuteValue($this->c_gameStart);
    }

     /*
     * NAME:    getGameStartAMPM
     * PARAMS:  N/A
     * DESC:    returns the game Start minute AM/PM val
     */
    public function getGameStartAMPM(){
        //return the value
        return $this->AMPMValue($this->c_gameStart);
    }




     /*
     * NAME:    setGameStart
     * PARAMS:  $p_passedVal = the value to set
     * DESC:    set the game start
     */
    public function setGameStart($p_passedVal){
        //set the value
        $this->c_gameStart = $p_passedVal;
    }

     /*
     * NAME:    getGameEnd
     * PARAMS:  N/A
     * DESC:    returns the game end
     */
    public function getGameEnd(){
        //variable declaration
        $timeStampVal = strtotime($this->c_gameEnd);
        $formatedTimeVal = strftime("%l:%M %p",$timeStampVal);
        //return the value
        return $formatedTimeVal;
    }

     /*
     * NAME:    getGameEndHour
     * PARAMS:  N/A
     * DESC:    returns the game end hour value
     */
    public function getGameEndHour(){
        //return the value
        return $this->hourValue($this->c_gameEnd);
    }

     /*
     * NAME:    getGameEndMinute
     * PARAMS:  N/A
     * DESC:    returns the game end minute value
     */
    public function getGameEndMinute(){
        //return the value
        return $this->minuteValue($this->c_gameEnd);
    }

     /*
     * NAME:    getGameEndAMPM
     * PARAMS:  N/A
     * DESC:    returns the game end minute AM/PM val
     */
    public function getGameEndAMPM(){
        //return the value
        return $this->AMPMValue($this->c_gameEnd);
    }

     /*
     * NAME:    setGameEnd
     * PARAMS:  $p_passedVal = the value to set
     * DESC:    set the game end
     */
    public function setGameEnd($p_passedVal){
        //set the value
        $this->c_gameEnd = $p_passedVal;
    }

     /*
     * NAME:    hourValue
     * PARAMS:  $p_passedVal = a time value
     * DESC:    return the hour value
     */
    private function hourValue($p_passedVal){
        //variable declaration
        $timeStampVal = strtotime($p_passedVal);
        $formatedTimeVal = strftime("%l",$timeStampVal);
        //return the value
        return $formatedTimeVal;
    }

     /*
     * NAME:    minuteValue
     * PARAMS:  $p_passedVal = a time value
     * DESC:    return the minute value
     */
    private function minuteValue($p_passedVal){
        //variable declaration
        $timeStampVal = strtotime($p_passedVal);
        $formatedTimeVal = strftime("%M",$timeStampVal);
        //return the value
        return $formatedTimeVal;
    }

     /*
     * NAME:    AMPMValue
     * PARAMS:  $p_passedVal = a time value
     * DESC:    return the am/pm value
     */
    private function AMPMValue($p_passedVal){
        //variable declaration
        $timeStampVal = strtotime($p_passedVal);
        $formatedTimeVal = strftime("%p",$timeStampVal);
        //return the value
        return $formatedTimeVal;
    }











    
     /*
     * NAME:    getGameNum
     * PARAMS:  N/A
     * DESC:    returns the game number
     */
    public function getGameNum(){
        //return the value
        return $this->c_gameNum;
    }

     /*
     * NAME:    setGameNum
     * PARAMS:  $p_passedVal = the value to set
     * DESC:    set the game number
     */
    public function setGameNum($p_passedVal){
        if(is_numeric($p_passedVal)){
            //set the value
            $this->c_gameNum = $p_passedVal;
        } else {
            //game number is wrong, add error message
            $this->errorOccured();
            $this->addMessage("The Game Number must be a numeric.");
        }
    }





     /*
     * NAME:    getGameDate
     * PARAMS:  N/A
     * DESC:    returns the game date
     */
    public function getGameDate(){
        //return the value
        return $this->c_gameDate;
    }

     /*
     * NAME:    setGameDate
     * PARAMS:  $p_passedVal = the value to set
     * DESC:    set the game date
     */
    public function setGameDate($p_passedVal){
        //set the value
        $this->c_gameDate = $p_passedVal;
    }


     /*
     * NAME:    getGameID
     * PARAMS:  N/A
     * DESC:    returns the game ID
     */
    public function getGameID(){
        //return the value
        return $this->c_gameID;
    }

     /*
     * NAME:    setGameID
     * PARAMS:  $p_passedVal = the value to set
     * DESC:    set the game ID
     */
    public function setGameID($p_passedVal){
        if(is_numeric($p_passedVal)){
            //set the value
            $this->c_gameID = $p_passedVal;
        } else {
            //game number is wrong, add error message
            $this->errorOccured();
            $this->addMessage("The Game ID must be a numeric.");
        }
    }

   
    /**
     * Returns the sumarized count of goals
     * @return numeric 
     */
    function getQuickWhiteScores(){
        return $this->c_quickWhiteScores;
    }
    /**
     * Returns the sumarized count of goals
     * @return numeric 
     */
    function getQuickBlackScores(){
        return $this->c_quickBlackScores;
    }
}
?>
