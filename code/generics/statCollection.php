<?php
/**
 * NAME:    statCollection.php
 * AUTHOR:  Paul Everton
 * DATE:    Feb 20, 2011
 * DESCRIPTION: a generic stat collection
 */

class statCollection extends collection {
    //class variables
    private $c_goalCount;
    private $c_assistCount;
    private $c_winCount;
    private $c_lossCount;
    private $c_gameCount;
    private $c_filled;
    private $c_GWGCount;
    private $c_goalAgainstCount;
    private $c_shutOut;
    private $c_goalSecondsPlayed;
    private $c_playerTeamScores;
    //constructor
    function statCollection(){
        parent::__construct();

        //default class attributes
        $this->c_goalCount = 0;
        $this->c_assistCount = 0;
        $this->c_gameCount = 0;
        $this->c_winCount = 0;
        $this->c_lossCount = 0;
        $this->c_GWGCount = 0;
        $this->c_goalAgainstCount = 0;
        $this->c_shutOut = 0;
        $this->c_goalSecondsPlayed = 0;
        $this->c_playerTeamScores = 0;

        $this->c_filled = false;
    }


    /*
     * NAME:    isFilled
     * PARAMS:  N/A
     * DESC:    will check to see if the values have been filled
     */
    public function isFilled(){
        //return the fill check
        return $this->c_filled;
    }

   /*
     * NAME:    hasBeenFilled
     * PARAMS:  N/A
     * DESC:    will notify that fill has happend
     */
    public function hasBeenFilled(){
        $this->c_filled = true;
    }

    /*
     * NAME:    addGoalCount
     * PARAMS:  N/A
     * DESC:    will add the passed count of goals
     */
    public function addGoalCount($p_count){
        $this->c_goalCount += $p_count;
    }

     /*
     * NAME:    addAssistCount
     * PARAMS:  N/A
     * DESC:    will add the passed count of assists
     */
    public function addAssistCount($p_count){
        $this->c_assistCount += $p_count;
    }

     /*
     * NAME:    addGameCount
     * PARAMS:  N/A
     * DESC:    will add the passed count of games
     */
    public function addGameCount($p_count){
        $this->c_gameCount += $p_count;
    }

     /*
     * NAME:    addWinCount
     * PARAMS:  N/A
     * DESC:    will add the passed count of wins
     */
    public function addWinCount($p_count){
        $this->c_winCount += $p_count;
    }


         /*
     * NAME:    addLossCount
     * PARAMS:  N/A
     * DESC:    will add the passed count of loss
     */
    public function addLossCount($p_count){
        $this->c_lossCount += $p_count;
    }

          /*
     * NAME:    addGWGCount
     * PARAMS:  N/A
     * DESC:    will add the passed count of game winning goals
     */
    public function addGWGCount($p_count){
        $this->c_GWGCount += $p_count;
    }



     /*
     * NAME:    addGoalAgainstCount
     * PARAMS:  N/A
     * DESC:    will add the passed count of goals against
     */
    public function addGoalAgainstCount($p_count){
        $this->c_goalAgainstCount += $p_count;
    }

     /*
     * NAME:    addShutOutCount
     * PARAMS:  N/A
     * DESC:    will add the passed count of shut outs
     */
    public function addShutOutCount($p_count){
        $this->c_shutOut += $p_count;
    }


     /*
     * NAME:    addSecondsPlayedCount
     * PARAMS:  N/A
     * DESC:    will add the passed count of seconds played
     */
    public function addSecondsPlayedCount($p_count){
        $this->c_goalSecondsPlayed += $p_count;
    }    
    
     /*
     * NAME:    addPlayerTeamScoresCount
     * PARAMS:  N/A
     * DESC:    will add the passed count of team scores
     */
    public function addPlayerTeamScoresCount($p_count){
        $this->c_playerTeamScores += $p_count;
    }    
    
      /*
     * NAME:    getPlayerTeamScoresCount
     * PARAMS:  N/A
     * DESC:    will get the count of player team scores
     */
    public function getPlayerTeamScoresCount(){
        //check to see if has been set
        if(!$this->isFilled()){
            //goals have not been set
            $this->fillScores();
        }
        //return the shutout count
        return $this->c_playerTeamScores;
    }   
    
      /*
     * NAME:    getSecondsPlayedCount
     * PARAMS:  N/A
     * DESC:    will get the count of seconds played
     */
    public function getSecondsPlayedCount(){
        //check to see if has been set
        if(!$this->isFilled()){
            //goals have not been set
            $this->fillScores();
        }
        //return the shutout count
        return $this->c_goalSecondsPlayed;
    }  
    
    
      /*
     * NAME:    getQuickGTotalGameMinutes
     * PARAMS:  N/A
     * DESC:    will get the total game minutes this goalie has played
     */
    public function getTotalGameMinutes(){

        //return the game minutes
        return $this->getSecondsPlayedCount()/60;
    }    
    
       /*
     * NAME:    getGPM
     * PARAMS:  N/A
     * DESC:    will get the goals against per minute
     */
    public function getGPM(){
        //variable declaration
        $ga = $this->getGoalsAgainstCount();
        $gTotalMins = $this->getTotalGameMinutes();
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
     * NAME:    getShutOutCount
     * PARAMS:  N/A
     * DESC:    will get the count of shut outs
     */
    public function getShutOutCount(){
        //check to see if has been set
        if(!$this->isFilled()){
            //goals have not been set
            $this->fillScores();
        }
        //return the shutout count
        return $this->c_shutOut;
    }


    /*
     * NAME:    getGoalsAgainstCount
     * PARAMS:  N/A
     * DESC:    will get the count of goals against
     */
    public function getGoalsAgainstCount(){
        //check to see if has been set
        if(!$this->isFilled()){
            //goals have not been set
            $this->fillScores();
        }
        //return the GA count
        return $this->c_goalAgainstCount;
    }


    /*
     * NAME:    getGWGCount
     * PARAMS:  N/A
     * DESC:    will get the count of game winning goals for this player's season
     */
    public function getGWGCount(){
        //check to see if has been set
        if(!$this->isFilled()){
            //goals have not been set
            $this->fillScores();
        }
        //return the gwg count
        return $this->c_GWGCount;
    }

    /*
     * NAME:    getGWGPercent
     * PARAMS:  N/A
     * DESC:    will get the game winning goal percent
     */
    public function getGWGPercent(){
        //variable declaration
        $gwg = $this->getGWGCount();
        $games = $this->getGameCount();
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
     * NAME:    getLossCount
     * PARAMS:  N/A
     * DESC:    will get the count of loss for this player's season
     */
    public function getLossCount(){
        //check to see if has been set
        if(!$this->isFilled()){
            //goals have not been set
            $this->fillScores();
        }
        //return the goal count
        return $this->c_lossCount;
    }

    /*
     * NAME:    getWinCount
     * PARAMS:  N/A
     * DESC:    will get the count of wins for this player's season
     */
    public function getWinCount(){
        //check to see if has been set
        if(!$this->isFilled()){
            //goals have not been set
            $this->fillScores();
        }
        //return the goal count
        return $this->c_winCount;
    }

     /*
     * NAME:    getWinLossCount
     * PARAMS:  N/A
     * DESC:    will get the +/-
     */
    public function getWinLossCount(){
        //variable declaration
        $points = $this->getWinCount() - $this->getLossCount();

        //return the value
        return $points;
    }



    /*
     * NAME:    getWinPercent
     * PARAMS:  N/A
     * DESC:    will get the win percentage
     */
    public function getWinPercent(){
        //variable declaration
        $win = $this->getWinCount();
        $games = $this->getGameCount();
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
     * NAME:    getTeamPointPercent
     * PARAMS:  N/A
     * DESC:    will get the percent of team points
     */
    public function getTeamPointPercent(){
        //variable declaration
        $ln_goals = $this->getGoalCount();
        $ln_teamPoints = $this->getPlayerTeamScoresCount();
        $ln_tpp = 0;
        
        if($ln_teamPoints != 0){
            //calculate the team point percentage
            $ln_tpp = $ln_goals / $ln_teamPoints * 100;
        }
        //return the value
        return number_format($ln_tpp, 2, '.', '');
    }   
    
    
    


    /*
     * NAME:    getGoalCount
     * PARAMS:  N/A
     * DESC:    will get the count of goals for this player's season
     */
    public function getGoalCount(){
        //check to see if has been set
        if(!$this->isFilled()){
            //goals have not been set
            $this->fillScores();
        }
        //return the goal count
        return $this->c_goalCount;
    }

    /*
     * NAME:    getAssistCount
     * PARAMS:  N/A
     * DESC:    will get the count of assists for this player's season
     */
    public function getAssistCount(){
        //check to see if has been set
        if(!$this->isFilled()){
            //goals have not been set
            $this->fillScores();
        }
        //return the goal count
        return $this->c_assistCount;
    }

     /*
     * NAME:    getPointCount
     * PARAMS:  N/A
     * DESC:    will get the points 
     */
    public function getPointCount(){
        //variable declaration
        $points = $this->getAssistCount() + $this->getGoalCount();

        //return the value
        return $points;
    }


     /*
     * NAME:    getGameCount
     * PARAMS:  N/A
     * DESC:    will get the count of unique games
     */
    public function getGameCount(){

        //check to see if has been set
        if(!$this->isFilled()){
            //goals have not been set
            $this->fillScores();
        }

        //return the goal count
        return $this->c_gameCount;
    }


     /*
     * NAME:    getGAA
     * PARAMS:  N/A
     * DESC:    will get the goals against average for a goalie
     */
    public function getGAA(){
        //variable declaration
        $goals = $this->getGoalsAgainstCount();
        $games = $this->getGameCount();
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
     * NAME:    getGPG
     * PARAMS:  N/A
     * DESC:    will get the goals per game calculation
     */
    public function getGPG(){
        //variable declaration
        $goals = $this->getGoalCount();
        $games = $this->getGameCount();
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
     * NAME:    getAPG
     * PARAMS:  N/A
     * DESC:    will get the assists per game calculation
     */
    public function getAPG(){
        //variable declaration
        $assists = $this->getAssistCount();
        $games = $this->getGameCount();
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
     * NAME:    getPPG
     * PARAMS:  N/A
     * DESC:    will get the points per game calculation
     */
    public function getPPG(){
        //variable declaration
        $points = $this->getAssistCount() + $this->getGoalCount();
        $games = $this->getGameCount();
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


    /*
     * NAME:    fillScores
     * PARAMS:  N/A
     * DESC:    skelleton for funtion
     */
    public function fillScores(){
        throw new Exception("fillScore has not been implemented");

    }


}
?>
