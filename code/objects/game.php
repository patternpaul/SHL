<?php

/**
 * NAME:    game.php
 * AUTHOR:  Paul Everton
 * DATE:    Feb 18, 2011
 * DESCRIPTION: Object describing a game
 */


class game extends object implements iComparable {
    //class variables
    /**
     * The game id
     * @var int 
     */
    private $c_gameID;
    
    /**
     * The game date
     * @var date
     */
    private $c_gameDate;
    
    /**
     * The game end time
     * @var time 
     */
    private $c_gameEnd;
    
    /**
     * The game number
     * @var int
     */
    private $c_gameNum;
    
    /**
     * The game start time
     * @var time
     */
    private $c_gameStart;
    
    /**
     * Indicates playoff: 0 regular season, 1 playoff
     * @var bit
     */
    private $c_playoff;
    
    /**
     * The season ID
     * @var int
     */
    private $c_seasonID;
    
    /**
     * Indicates the previous season ID used in this game object.
     * This is used for when the game data is being modified and we need
     * to know if the game changed from one season to the other
     * @var int
     */
    private $c_previousSeasonID;
    
    /**
     * The collection of white team player
     * @var teamPlayerCollection 
     */
    private $c_teamWhite;
    
    /**
     * The collection of black team player
     * @var teamPlayerCollection 
     */
    private $c_teamBlack;
    
     /**
     * The teamScoreCollection for team white
     * @var teamScoreCollection 
     */
    private $c_whiteScores;
    
    /**
     * The teamScoreCollection for team black
     * @var teamScoreCollection 
     */
    private $c_blackScores;
    
    /**
     * The color identifier of the  game winner
     * @var int
     */
    private $c_winner;
    
    /**
     * The overall game time in seconds
     * @var int
     */
    private $c_gameSecondsTime;
    
    /**
     * The quick summary count of white goals
     * @var int 
     */
    private $c_quickWhiteScores;
    
    /**
     * The quick summary count of black goals
     * @var int
     */
    private $c_quickBlackScores;
    

    const TEAMBLACK = 1;
    const TEAMWHITE = 2;
    
    
    
    /**
     * The Game constructor
     * @param int $p_gameID The game ID
     */
    function game($p_gameID){
        //set the class variable
        $this->c_gameID = $p_gameID;
        $this->c_previousSeasonID = -1;
        $this->setObjectID($p_gameID);
        
        $this->c_seasonID = game::getSeasonToUse();
        $this->c_gameNum = game::getNextAvailableGameForSeason($this->c_seasonID);
        $this->c_playoff = game::getPlayoffForSeason($this->c_seasonID);
    
        //check to see if game should be loaded
        if($this->c_gameID > 0){
            //call the load function of the class
            $this->load();
        }
        
        $this->checkRep();
    }

    /**
     * Assert check to ensure the class is sane
     */
    private function checkRep(){
        assert("is_numeric($this->c_gameID)");
        assert("$this->c_gameID >= 0");
        assert("is_numeric($this->c_seasonID)");
        //assert that the current season ID is between 1 and the max season + 1. Anything else should be questionable
        assert("($this->c_seasonID > 0) && ($this->c_seasonID <= (game::getMaxSeason() + 1))");
        assert("is_numeric($this->c_playoff)");
        assert("($this->c_playoff >= 0) && ($this->c_playoff <= 1)");
        assert("(($this->c_playoff == 1) && ($this->c_gameNum >= 1) && ($this->c_gameNum <= 7)) ||
                (($this->c_playoff == 0) && ($this->c_gameNum >= 1) && ($this->c_gameNum <= 50))");
        
        //check if valid date time entries
        if(isset($this->c_gameDate)){
            assert("(strtotime('$this->c_gameDate'))");
        }
        if(isset($this->c_gameEnd)){
            assert("(strtotime('$this->c_gameEnd'))");
        }
        if(isset($this->c_gameStart)){
            assert("(strtotime('$this->c_gameStart'))");
        }
        
        if(isset($this->c_winner)){
            assert("(($this->c_winner >= 1) && ($this->c_winner <= 2))");
        }
        
        if(isset($this->c_gameSecondsTime)){
            assert("($this->c_gameSecondsTime > 0)");
        }
        
        if(isset($this->c_quickWhiteScores)){
            assert("($this->c_quickWhiteScores >= 0)");
            
            if(isset($this->c_whiteScores) && ($this->c_whiteScores->isFilled())){
                assert("($this->c_quickWhiteScores == $this->c_whiteScores->count())");
            }
        }
        
        if(isset($this->c_quickBlackScores)){
            assert("($this->c_quickBlackScores >= 0)");
            if(isset($this->c_blackScores) && ($this->c_blackScores->isFilled())){
                assert("($this->c_quickBlackScores == $this->c_blackScores->count())");
            }
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

        //assert the value we are returning is numeric and greater than 0
        assert("is_numeric($newestGame) && ($newestGame >= 1)");
        
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

        //assert the value we are returning is numeric and greater than 0
        assert("is_numeric($maxSeason) && ($maxSeason >= 1)");
        
        //return the max season
        return $maxSeason;
    }
    
    /**
     * Will try an determine the season we should be using
     * @return int season to use
     */
    public static function getSeasonToUse(){
        //variable declaration
        $li_seasonToUse = 1;
        $li_season = 1;
        $li_playoff = 0;
        $li_gameNum = 1;
        $la_params = array();
        $ls_sql = "
            SELECT  G.SeasonID, G.Playoff, G.GameNum
            FROM Game AS G
            ORDER BY G.SeasonID DESC, G.Playoff DESC, G.GameNum DESC
            LIMIT 1";
        
        //fetch the data
        $data = DBFac::getDB()->sql($ls_sql, $la_params);

        //fill the data
        foreach($data as $row) {
            $li_season = $row['SeasonID'];
            $li_playoff = $row['Playoff'];
            $li_gameNum = $row['GameNum'];
        }
        
        //check for playoffs
        if($li_playoff == 1){
            //since it's playoffs, we want to see if the game number is between 1-7
            if(($li_gameNum >= 1) && ($li_gameNum <= 7)){
                //use the current season, since we are still in playoffs
                $li_seasonToUse = $li_season;
            }else{
                //playoffs are done, next season! ... I hope I won
                $li_seasonToUse = $li_season + 1;
            }
        }else{
            //it's regular season. Does not really matter if we are at max games
            //since we will roll into playoffs. So we should still use this season
            $li_seasonToUse = $li_season; 
        }
        
        //assert that the season is between 1 and between the max season + 1 since
        //we could be done playoffs
        assert("is_numeric($li_seasonToUse) && ($li_seasonToUse >= 1) && ($li_seasonToUse <= game::getMaxSeason())");

        //return the max season
        return $li_seasonToUse;
    }
    

    /**
     * Returns the next available game for the passed Season.
     * If it's anything over 50, we assume the next available game
     * is a playoff game. If we are over 7 games in playoffs, return 1
     * as it should probably be the next season
     * @param int $pi_seasonID
     * @return int
     */
    public static function getNextAvailableGameForSeason($pi_seasonID){
        //variable declaration
        $li_maxGameNum = 1;
        $li_playoff = 1;
        $li_nextGame = 1;
        $la_params = array();
        $ls_sql = "
            SELECT  g.GameNum, g.Playoff
            FROM    Game AS g
            WHERE   g.SeasonID = :seasonID
            ORDER BY g.SeasonID DESC, g.Playoff DESC, g.GameNum DESC
            LIMIT 1";
        
        //assert the seasonID is something larger than 0
        assert("is_numeric($pi_seasonID) && ($pi_seasonID > 0)");
        
        
        
        $la_params["seasonID"] = $pi_seasonID;
        
        //fetch the data
        $data = DBFac::getDB()->sql($ls_sql, $la_params);

        //fill the data
        foreach($data as $row) {
            $li_maxGameNum = $row["GameNum"];
            $li_playoff = $row["Playoff"];
        }
        
        //check to playoffs
        if($li_playoff == 1){
            if($li_maxGameNum < 7){
                $li_nextGame = $li_maxGameNum + 1;
            }else{
                $li_nextGame = 1;
            }
        }else{
            if($li_maxGameNum < 50){
                $li_nextGame = $li_maxGameNum + 1;
            }else{
                $li_nextGame = 1;
            }
        }
        
        
        //assert the outgoing game number is between 1-50 for regular season games and between 1-7 for playoffs
        assert("is_numeric($li_nextGame) && ((($li_playoff == 0) && ($li_nextGame > 0)&& ($li_nextGame <= 50)) || (($li_playoff == 1) && ($li_nextGame > 0)&& ($li_nextGame <= 7)))");
        //return the max season
        return $li_maxGameNum;
    }

    
    /**
     * Returns the available playoff indicator for the passed Season.
     * If the current no playoff game is 50, we assume the next available
     * is playoff. If we are over 7 games in playoffs, return 0
     * as it should probably be the next regular season 
     * @param int $pi_seasonID
     * @return int
     */
    public static function getPlayoffForSeason($pi_seasonID){
        //variable declaration
        $li_playoffIndicator = 0;
        $li_gameNum = 1;
        $li_playoff = 1;
        $li_nextGame = 1;
        $la_params = array();
        $ls_sql = "
            SELECT  g.GameNum, g.Playoff
            FROM    Game AS g
            WHERE   g.SeasonID = :seasonID
            ORDER BY g.SeasonID DESC, g.Playoff DESC, g.GameNum DESC";
        
        //assert the seasonID is something larger than 0
        assert("is_numeric($pi_seasonID) && ($pi_seasonID > 0)");
        
        
        
        $la_params["seasonID"] = $pi_seasonID;
        
        //fetch the data
        $data = DBFac::getDB()->sql($ls_sql, $la_params);

        //fill the data
        foreach($data as $row) {
            $li_gameNum = $row["GameNum"];
            $li_playoff = $row["Playoff"];
        }
        
        //check to playoffs
        if($li_playoff == 1){
            if($li_gameNum < 7){
                $li_playoffIndicator = 1;
            }else{
                //we assume playoffs have finished after 7 games
                $li_playoffIndicator = 0;
            }
        }else{
            if($li_gameNum < 50){
                $li_playoffIndicator = 0;
            }else{
                //if we are at game 50 of regular season, we should
                //move on to playoffs
                $li_playoffIndicator = 1;
            }
        }
        
        
        //assert the outgoing playoff indicator
        assert("is_numeric($li_playoffIndicator)");
        assert("(($li_playoff == 1) && ($li_gameNum >= 1) && ($li_gameNum < 7) && ($li_playoffIndicator == 1)) ||
                (($li_playoff == 1) && ($li_gameNum >= 7) && ($li_playoffIndicator == 0)) ||
                (($li_playoff == 0) && ($li_gameNum >= 1) && ($li_gameNum < 50) && ($li_playoffIndicator == 0)) ||
                (($li_playoff == 0) && ($li_gameNum >= 50) && ($li_playoffIndicator == 1))");
        
        
        //return the playoff indicator
        return $li_playoffIndicator;
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






    /**
     * Does the full game insert flow
     */
    public function fullGameInsert(){
        //insert the game
        $this->insert();
        //insert white team collection
        $this->insertTeamCollection($this->getTeamWhite());
        //insert black team collection
        $this->insertTeamCollection($this->getTeamBlack());
        
        //update all the summary tables
        $this->UpdateMasterCall($this->c_seasonID);
        
        //refule the records
        $recordObj = new records();
        $recordObj->recordRefil();

    }


    /**
     * Inserts the collection of team players for this game
     * @param teamPlayerCollection $p_teamCollection the collection of team players
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


    /**
     * Returns a random team color
     * @return int team color 
     */
    public static function getRandomTeamColor(){
        $la_teamColors = array();
        $la_teamColors[] = game::TEAMBLACK;
        $la_teamColors[] = game::TEAMWHITE;
        
        return $la_teamColors[array_rand($la_teamColors)];
    }
    
    
   /**
    * Returns the collection of Team White's team players
    * @return teamPlayerCollection collection of white team players 
    */
    public function getTeamWhite(){
        //check to see if the collection has been created
        if(!isset($this->c_teamWhite)){
            //create the collection
            $this->c_teamWhite = $this->getTeamCollection(game::TEAMWHITE);
        }

        //return the team white collection
        return $this->c_teamWhite;
    }

    /**
     * Sets the game's white team player collection
     * @param teamPlayerCollection $p_passedTeam the passed collection of white team players
     */
    public function setTeamWhite($p_passedTeam){
        $this->c_teamWhite = $p_passedTeam;
    }

    /**
     * Returns this games collection of black team players
     * @return teamPlayerCollection the team player collection for team black
     */
    public function getTeamBlack(){
        //check to see if the collection has been created
        if(!isset($this->c_teamBlack)){
            //create the collection
            $this->c_teamBlack = $this->getTeamCollection(game::TEAMBLACK);
        }
        //return the team white collection
        return $this->c_teamBlack;
    }

    /**
     * Sets this game's team black team player collection
     * @param teamPlayerCollection $p_passedTeam the collection of black team players
     */
    public function setTeamBlack($p_passedTeam){
        $this->c_teamBlack = $p_passedTeam;
    }

    /**
     * Returns a team player collection based on the passed color for this game
     * @param int $p_colorID the color id
     * @return teamPlayerCollection the team player collection
     */
    public function getTeamCollection($p_colorID){
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

    /**
     * Returns the scoreCollection for the passed in color for this game
     * @param int $p_colorID
     * @return scoreCollection the score collection for a color in this game
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

    /**
     * Gets this game's black team scores
     * @return teamScoreCollection
     */
    public function getTeamBlackScores(){
        //check to see if the collection has been created
        if(!isset($this->c_blackScores)){
            //create the collection
            $this->c_blackScores = $this->getTeamScoreCollection(game::TEAMBLACK);
        }

        //return the team white collection
        return $this->c_blackScores;
    }


    /**
     * Returns this game's white team score collection
     * @return teamScoreCollection 
     */
    public function getTeamWhiteScores(){
        //check to see if the collection has been created
        if(!isset($this->c_whiteScores)){
            //create the collection
            $this->c_whiteScores = $this->getTeamScoreCollection(game::TEAMWHITE);
        }

        //return the team white collection
        return $this->c_whiteScores;
    }



    /**
     * Will compare the passed object to the current object
     * @param object $p_objectToCompare object to compare to
     * @return boolean comparison indicator
     */
     public function compareTo($p_objectToCompare){
        //variable declaration
        $is_comparable = false;

        //compare the objects
        if($this->isID($p_objectToCompare->c_gameID)){
            $is_comparable = true;
        }

        return $is_comparable;
     }

     /**
      * Compares game IDs
      * @param int $p_ID the ID to compare to
      * @return boolean comparison indicator
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

     /**
      * Returns the int indicator for a game winner
      * @return int
      */
    public function getWinner(){
        //return the value
        return $this->c_winner;
    }

    /**
     * Returns the season ID
     * @return int
     */
    public function getSeasonID(){
        //return the value
        return $this->c_seasonID;
    }

    /**
     * Sets the Season ID
     * @param int $p_passedVal the season ID
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


    /**
     * Returns the playoff indicator: 0 regular season, 1 playoffs
     * @return bit
     */
    public function getPlayoff(){
        //return the value
        return $this->c_playoff;
    }

    /**
     * Sets the playoff indicator: 0 regular season, 1 playoffs
     * @param bit $p_passedVal the playoff indicator
     */
    public function setPlayoff($p_passedVal){
        //set the value
        if(($p_passedVal == 0) || ($p_passedVal == 1)){
            $this->c_playoff = $p_passedVal;
        }else{
            $this->errorOccured();
            $this->addMessage("Playoffs should be 0 or 1");
        }
    }

    /**
     * Returns the game time in total seconds
     * @return int
     */
    public function getGameSeconds(){

        //return the shutout count
        return $this->c_gameSecondsTime;
    }  

    /**
     * Returns the total game time in Minutes
     * @return int 
     */
    public function getTotalGameMinutes(){

        //return the game minutes
        return $this->getGameSeconds()/60;
    }    
     
    
    /**
     * Returns the game start time properly formatted
     * @return string 
     */
    public function getGameStart(){
        //variable declaration
        $timeStampVal = strtotime($this->c_gameStart);
        $formatedTimeVal = strftime("%l:%M %p",$timeStampVal);
        //return the value
        return $formatedTimeVal;
    }

    /**
     * Returns the hour value of the formatted time
     * @return int
     */
    public function getGameStartHour(){
        //return the value
        return $this->hourValue($this->c_gameStart);
    }

    /**
     * Returns the Minute value of the game start time
     * @return int
     */
    public function getGameStartMinute(){
        //return the value
        return $this->minuteValue($this->c_gameStart);
    }

    /**
     * Returns the AM/PM value of the game start time
     * @return string
     */
    public function getGameStartAMPM(){
        //return the value
        return $this->AMPMValue($this->c_gameStart);
    }

    /**
     * Sets the game start time
     * @param string $p_passedVal 
     */
    public function setGameStart($p_passedVal){
        //set the value
        $this->c_gameStart = $p_passedVal;
    }

    /**
     * Returns the formatted game end time
     * @return string
     */
    public function getGameEnd(){
        //variable declaration
        $timeStampVal = strtotime($this->c_gameEnd);
        $formatedTimeVal = strftime("%l:%M %p",$timeStampVal);
        //return the value
        return $formatedTimeVal;
    }

    /**
     * Returns the game end time
     * @return string
     */
    public function getGameEndHour(){
        //return the value
        return $this->hourValue($this->c_gameEnd);
    }

     /**
      * Returns the Minute value of the game time
      * @return int
      */
    public function getGameEndMinute(){
        //return the value
        return $this->minuteValue($this->c_gameEnd);
    }

    /**
     * Returns the AM/PM value of the game end time
     * @return string
     */
    public function getGameEndAMPM(){
        //return the value
        return $this->AMPMValue($this->c_gameEnd);
    }

    /**
     * Sets the Game End time
     * @param string $p_passedVal 
     */
    public function setGameEnd($p_passedVal){
        //set the value
        $this->c_gameEnd = $p_passedVal;
    }

    /**
     * Returns the hour value of the passed in time string
     * @param string $p_passedVal
     * @return int 
     */
    private function hourValue($p_passedVal){
        //variable declaration
        $timeStampVal = strtotime($p_passedVal);
        $formatedTimeVal = strftime("%l",$timeStampVal);
        //return the value
        return $formatedTimeVal;
    }

    /**
     * Returns the minute value of the passed in time string
     * @param string $p_passedVal
     * @return int
     */
    private function minuteValue($p_passedVal){
        //variable declaration
        $timeStampVal = strtotime($p_passedVal);
        $formatedTimeVal = strftime("%M",$timeStampVal);
        //return the value
        return $formatedTimeVal;
    }

    /**
     * Returns the AM/PM value of the passed in string
     * @param string $p_passedVal
     * @return string
     */
    private function AMPMValue($p_passedVal){
        //variable declaration
        $timeStampVal = strtotime($p_passedVal);
        $formatedTimeVal = strftime("%p",$timeStampVal);
        //return the value
        return $formatedTimeVal;
    }

    /**
     * Returns the game number
     * @return int 
     */
    public function getGameNum(){
        //return the value
        return $this->c_gameNum;
    }

    /**
     * Sets the game number
     * @param int $p_passedVal 
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

    /**
     * Returns the game date value
     * @return string
     */
    public function getGameDate(){
        //return the value
        return $this->c_gameDate;
    }

    /**
     * Sets the game date value
     * @param string $p_passedVal 
     */
    public function setGameDate($p_passedVal){
        //set the value
        $this->c_gameDate = $p_passedVal;
    }

    /**
     * Return the gameID
     * @return int
     */
    public function getGameID(){
        //return the value
        return $this->c_gameID;
    }

    /**
     * Sets the Game ID
     * @param int $p_passedVal 
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
     * This gets the quick White Score listing. This is a precompiled
     * total from summary tables. This saves processing time instead of getting
     * the score from the collection objects
     * @return int
     */
    function getQuickWhiteScores(){
        return $this->c_quickWhiteScores;
    }
    
    /**
     * This gets the quick black Score listing. This is a precompiled
     * total from summary tables. This saves processing time instead of getting
     * the score from the collection objects
     * @return int
     */
    function getQuickBlackScores(){
        return $this->c_quickBlackScores;
    }
}
?>
