<?php

/**
 * NAME:    records.php
 * AUTHOR:  Paul Everton
 * DATE:    March 30, 2011
 * DESCRIPTION: Object to hold records
 */

//requires



class records {
    //class variables
    public $c_seasonIDType;


    //Constructors
    function records(){
        $this->c_seasonIDType = 1;
        $this->c_playerIDType = 2;
    }


    
 /*******************************************************
  * 
  * 
  *     RECORD CREATION
  * 
  * 
  **********************************************************/  
    
    

    
      /*
     * NAME:    recordRefil
     * PARAMS:  N/A
     * DESC:    function to refil records
     *
     */
    public function recordRefil(){
        //variable declaration
        //database connection
        $d = new db(0);

        //Gather the IDs before deleting
        $pre_delete = $d->fetch("
            SELECT CONCAT(CAST(r.RecordID AS CHAR), CAST(RHI.RepresentativeID AS CHAR), CAST(RHI.IDTypeID AS CHAR), RH.Value) AS RecordSearch
FROM Record AS r
INNER JOIN RecordHolder AS RH ON r.RecordID = RH.RecordID
INNER JOIN RecordHolderID AS RHI ON RH.RecordHolderID = RHI.RecordHolderID
WHERE IDTypeID = 2
ORDER BY r.RecordID, RHI.RepresentativeID, RHI.IDTypeID
");
        //this should be cleaned up.
        $record_comma_list = "";
        
        //fill the data
        foreach($pre_delete as $row) {
            if($record_comma_list == ""){
                $record_comma_list = "'" . $row['RecordSearch'] . "'";
            }else{
                $record_comma_list = $record_comma_list . ", '" . $row['RecordSearch'] . "'";
            }
        }
        
        
        
        //insert the record
        $data = $d->exec("
            DELETE FROM RecordHolder"); 

        $data = $d->exec("
            DELETE FROM RecordHolderID");



        //call individual functions
        
        $this->teamRegularSeasonRecords();
        
        $this->regularSeasonOvertimeGames();
        
        $this->regularSeasonLongestGames();
        
        $this->regularSeasonShortestGames();
        
        $this->regularSeasonMostGoals();
        
        $this->regularSeasonLeastGoals();
        
        $this->regularSeasonMostGoalsByPlayerInSeason();
        
        $this->regularSeasonMostAssistsByPlayerInSeason();
        
        $this->regularSeasonMostPointsByPlayerInSeason();
        
        /* SLOW */
        $this->regularSeasonFiveHundredPointClub();
        
        $this->regularSeasonThousandPointClub();
        
        $this->regularSeasonTwoFiftyGoalClub();
        
        $this->regularSeasonFiveHundredGoalClub();
        
        $this->regularSeasonTwoFiftyAssistClub();
        
        $this->regularSeasonFiveHundredAssistClub();
        /* SLOW */
        
        
        
        
        
        $this->regularSeasonShutoutClub();
        
        $this->allSeasonCupWinners();
        
        $this->getMostCupWins();
        
        $this->regularSeasonBestGoalieGAA();
        
        $this->regularSeasonMostGoalieWins();
        
        $this->regularSeasongGoalieHundredWinsClub();
        
        $this->regularSeasongGoaliePlusMinus();
        
        $this->regularSeasongGoaliePlusMinusInSeason();
        
        $this->regularSeasongGoalieMostGamesInSeason();
        
        
        $this->regulargetBestPlayerPPG(); 
        
        $this->regularSeasonMostPlayerWins(); 
        
        $this->regularSeasongPlayerPlusMinus(); 
        
        $this->regularSeasongPlayerPlusMinusInSeason(); 
        
        $this->regularSeasongPlayerMostGamesInSeason();
        
        
        $this->regularSeasongMostGoalsInGame();
        
        $this->regularSeasongMostAssistsInGame();
        
        $this->regularSeasongMostPointsInGame();
        
        
        $this->regularSeasongGoalieWinStreak();
        
        $this->regularSeasongPlayerWinStreak();
        
        
        $this->regularSeasongGoalieWinStreakInOneSeason();
        
        $this->regularSeasongPlayerWinStreakInOneSeason();
        
        
        $this->regularSeasongPlayerAssistStreak();
        
        $this->regularSeasongPlayerGoalStreak();
        
        $this->regularSeasonBestMatchUp();
        $this->regularSeasonAllTimeBestMatchUp();
        $this->regularSeasonMostHundredPointSeasons();
        $this->regularSeasonMostConsecutiveHundredPointSeasons();
        
        $this->regularSeasonMostFiftyGoalSeasons();
        $this->regularSeasonMostConsecutiveFiftyGoalSeasons();
        
        $this->regularSeasonMostFiftyAssistSeasons();
        $this->regularSeasonMostConsecutiveFiftyAssistSeasons();
        
        $this->regularSeasonNoOvertimeLongestGames();
        $this->playoffLongestGames(); 
        $this->playoffNoOTLongestGames();
        
        
        //get all changed records
        $record_change = $d->fetch("
            SELECT DISTINCT r.RecordID, RHI.RepresentativeID, RHI.IDTypeID
FROM Record AS r
INNER JOIN RecordHolder AS RH ON r.RecordID = RH.RecordID
INNER JOIN RecordHolderID AS RHI ON RH.RecordHolderID = RHI.RecordHolderID
WHERE CONCAT(CAST(r.RecordID AS CHAR), CAST(RHI.RepresentativeID AS CHAR), CAST(RHI.IDTypeID AS CHAR), RH.Value) NOT IN (" . 
                $record_comma_list
                .
")
AND IDTypeID = 2");
        //create the collection
        $change_recordCol = new recordCollection();
        
        
        //loop over and add records
        foreach($record_change as $row) {
            //ensure it's a player record as I am not handling other types right now
            if($row['IDTypeID'] == 2){
                
                $recordCreated = new record($row['RecordID']);
                $recordCreated->loadPlayerRecords($row['RepresentativeID']);
                //add the record to the collection
                $change_recordCol->add($recordCreated);
            }
        }
        
        
        if($change_recordCol->count() > 0){
            $record_change_text = "The following records have either been broken or have been tied <br />";
            $recordName = "";
            for($alpha = 0; $alpha < $change_recordCol->count(); $alpha += 1){
                $currentRecord = $change_recordCol->get($alpha);
                
                if($recordName != $currentRecord->c_name){
                
                    $recordName = $currentRecord->c_name;
                    $record_change_text = $record_change_text . '<div class="row">
                            <div class="span10">
                            <h3>' . $recordName . '</h3></div></div>';
                }
                $recordName = $currentRecord->c_name;
                for($beta = 0; $beta < $currentRecord->count(); $beta += 1){
                                $currentHolder = $currentRecord->get($beta);
                                $record_change_text = $record_change_text . '<div class="row">
                            <div class="span10">' . $currentHolder->getValue() . '</div></div>'; 
                }

                //$record_change_text = $record_change_text . '';

            }

            //create the post
            $postUpdate = new post(0);
            //set the class params
            $postUpdate->setTitle("Records Change");
            $postUpdate->setContent($record_change_text);
            
            $postUpdate->setDateCreated($postUpdate->getDateCreated());
            //change this to a system account or set the post object to system default
            $postUpdate->setPoster(35);
            //run the exec
            $postUpdate->exec();
        }
    }  
   /*
     * NAME:    regularSeasonMostConsecutiveFiftyAssistSeasons
     * PARAMS:  N/A
     * DESC:    get the most consecutive 50 assist seasons record
     */
    public function regularSeasonMostConsecutiveFiftyAssistSeasons(){
        //variable declaration
        return $this->getMostConsecutivePointSeasons(0,2,50,48);
    }   
    
    /*
     * NAME:    regularSeasonMostFiftyAssistSeasons
     * PARAMS:  N/A
     * DESC:    get the most 50 assist seasons record
     */
    public function regularSeasonMostFiftyAssistSeasons(){
        //variable declaration
        return $this->getMostPointSeasons(0,2,50,47);
    }    
    
   /*
     * NAME:    regularSeasonMostConsecutiveFiftyGoalSeasons
     * PARAMS:  N/A
     * DESC:    get the most consecutive 50 goal seasons record
     */
    public function regularSeasonMostConsecutiveFiftyGoalSeasons(){
        //variable declaration
        return $this->getMostConsecutivePointSeasons(0,1,50,46);
    }   
    
    /*
     * NAME:    regularSeasonMostFiftyGoalSeasons
     * PARAMS:  N/A
     * DESC:    get the most 50 goal seasons record
     */
    public function regularSeasonMostFiftyGoalSeasons(){
        //variable declaration
        return $this->getMostPointSeasons(0,1,50,45);
    }  
    

    
    
   /*
     * NAME:    regularSeasonMostConsecutiveHundredPointSeasons
     * PARAMS:  N/A
     * DESC:    get the most consecutive hundred point seasons record
     */
    public function regularSeasonMostConsecutiveHundredPointSeasons(){
        //variable declaration
        return $this->getMostConsecutivePointSeasons(0,0,100,44);
    }  
    
    
    /*
     * NAME:    regularSeasonMostHundredPointSeasons
     * PARAMS:  N/A
     * DESC:    get the most hundred point seasons record
     */
    public function regularSeasonMostHundredPointSeasons(){
        //variable declaration
        return $this->getMostPointSeasons(0,0,100,43);
    }  
    
     /*
     * NAME:    getMostConsecutivePointSeasons
     * PARAMS:  N/A
     * DESC:    creates the consecutive point seasons records
     *
     */
    public function getMostConsecutivePointSeasons($p_playoff, $p_type, $p_count, $p_record){   
        //variable declaration
        $sql = "";    
        $playerAr = array();
        $topCount = 0;
        $d = new db(0);
        
        $ln_curPlayer = 0;
        $ln_prevSeason = 0;
        $ln_curCount = 0;
        $ln_maxConsecutive = 0;
        
        
        $sql = "
            SELECT SeasonID, Playoff, PlayerID, Goals, Assists, Wins, Losses, WinningGoals 
              FROM SHL.QuickPlayerDataTable
              WHERE ";
        
        if($p_type == 1){
            $sql = $sql . "(Goals) >= " .  $p_count;
        }elseif($p_type == 2){
            $sql = $sql . "(Assists) >= " .  $p_count;
        }else{
            $sql = $sql . "(Goals+Assists) >= " .  $p_count;
        }
        

                
        $sql = $sql . "
              AND Playoff = " .  db::fmt($p_playoff,1) . "
              ORDER BY PlayerID, SeasonID, Playoff";
        

        
        //fetch the data
        $data = $d->fetch($sql);
        
        
        
        //loop and store the top players
        foreach($data as $row) {
            if($row['PlayerID'] != $ln_curPlayer){
                //reset
                $ln_curPlayer = $row['PlayerID'];
                $ln_prevSeason = 0;
                $ln_curCount = 0;
            }
            
            if($row['SeasonID'] > ($ln_prevSeason+1)){
                $ln_curCount = 1;
            }else{
                $ln_curCount++;
            }
            
            if($ln_curCount == $ln_maxConsecutive){
                $playerAr[] = $row['PlayerID'];
            }elseif($ln_curCount > $ln_maxConsecutive){
                $ln_maxConsecutive = $ln_curCount;
                $playerAr = array();
                $playerAr[] = $row['PlayerID'];
            }
            $ln_prevSeason = $row['SeasonID'];
        }
        
        
        //create the records
       foreach($playerAr as $recordHolder){
            $textValue = "";
            
            $player = new player($recordHolder);
            $keyCollection = new collection();
            $playerIDArray = array();
            $playerIDArray[] = $this->c_playerIDType;
            $playerIDArray[] = $player->getPlayerID();
            $keyCollection->add($playerIDArray);
            

            $textValue = "<a href=\"http://www.shl-wpg.ca/playerDetails.php?playerid=" . $player->getPlayerID() . "\">" . $player->getFullName() . "</a>: " . $ln_maxConsecutive . " consecutive seasons.";

            $this->addRecordHolder($textValue, $p_record, $keyCollection);

        }
    }  
    
    
    
     /*
     * NAME:    getMostPointSeasons
     * PARAMS:  N/A
     * DESC:    creates the point seasons records
     *
     */
    public function getMostPointSeasons($p_playoff, $p_type, $p_count, $p_record){   
        //variable declaration
        $sql = "";    
        $playerAr = array();
        $topCount = 0;
        $d = new db(0);
        
        
        
        
        $sql = "
            SELECT PlayerID, COUNT(SeasonID) AS SeasonCount
            FROM (
              SELECT SeasonID, Playoff, PlayerID, Goals, Assists, Wins, Losses, WinningGoals 
              FROM SHL.QuickPlayerDataTable
              WHERE ";
        
        if($p_type == 1){
            $sql = $sql . "(Goals) >= " .  $p_count;
        }elseif($p_type == 2){
            $sql = $sql . "(Assists) >= " .  $p_count;
        }else{
            $sql = $sql . "(Goals+Assists) >= " .  $p_count;
        }
        

                
        $sql = $sql . "
              AND Playoff = " .  db::fmt($p_playoff,1) . "
              ORDER BY PlayerID, SeasonID, Playoff
            ) AS HundredSeason
            GROUP BY PlayerID";

        //fetch the data
        $data = $d->fetch($sql);
        
        
        
        //loop and store the top players
        foreach($data as $row) {
            if($row['SeasonCount'] > $topCount){
               $topCount = $row['SeasonCount'];
               $playerAr = array();
            }
            
            if($row['SeasonCount'] == $topCount){
                $playerAr[] = $row['PlayerID'];
            }
        }
        
        
        //create the records
       foreach($playerAr as $recordHolder){
            $textValue = "";
            
            $player = new player($recordHolder);
            $keyCollection = new collection();
            $playerIDArray = array();
            $playerIDArray[] = $this->c_playerIDType;
            $playerIDArray[] = $player->getPlayerID();
            $keyCollection->add($playerIDArray);
            

            $textValue = "<a href=\"http://www.shl-wpg.ca/playerDetails.php?playerid=" . $player->getPlayerID() . "\">" . $player->getFullName() . "</a>: " . $topCount . " seasons.";

            $this->addRecordHolder($textValue, $p_record, $keyCollection);

        }
    }  
        
    
    
    
    
    
    
    /*
     * NAME:    regularSeasonAllTimeBestMatchUp
     * PARAMS:  N/A
     * DESC:    get the best regular season matchup
     */
    public function regularSeasonAllTimeBestMatchUp(){
        //variable declaration
        return $this->getMatchUpAllTime(0, 42);
    }  
    
    
     /*
     * NAME:    getMatchUpAllTime
     * PARAMS:  N/A
     * DESC:    creates the best player matchup record
     *
     */
    public function getMatchUpAllTime($p_playoff, $p_record){   
        //variable declaration
        $sql = "";    
        $playerAr = array();
        $d = new db(0);
        $sql = "
            SELECT *
            FROM (
              SELECT FirstPlayerID, SecondPlayerID, SUM(Hookup) TotalHookups
              FROM SHL.QuickHookupSummary
              WHERE Playoff = " .  db::fmt($p_playoff,1) . " 
              GROUP BY FirstPlayerID, SecondPlayerID
            ) AS TotalCount
            WHERE TotalHookups = (
              SELECT TotalHookups
              FROM (
                SELECT FirstPlayerID, SecondPlayerID, SUM(Hookup) TotalHookups
                FROM SHL.QuickHookupSummary
                WHERE Playoff = " .  db::fmt($p_playoff,1) . " 
                GROUP BY FirstPlayerID, SecondPlayerID
              ) AS CheckTotalCount
              ORDER BY TotalHookups DESC
              LIMIT 1
            )";
        //fetch the data
        $data = $d->fetch($sql);

        //fill the data
        foreach($data as $row) {
            $found = false;
            $tempArr = array();
            foreach($playerAr as $playerData){
                if(($playerData[0] == trim($row['SecondPlayerID'])) && ($playerData[1] == trim($row['FirstPlayerID']))){
                    $found = true;
                }
            }
            if(!$found){
                $tempArr[] = trim($row['FirstPlayerID']);
                $tempArr[] = trim($row['SecondPlayerID']);
                $tempArr[] = trim($row['TotalHookups']);
                $playerAr[] = $tempArr;
            }
        
        }
        
        //create the records
       foreach($playerAr as $recordHolder){
            $textValue = "";
            
            $player = new player($recordHolder[0]);
            $playerTwo = new player($recordHolder[1]);
            $keyCollection = new collection();
            $playerIDArray = array();
            $playerIDArray[] = $this->c_playerIDType;
            $playerIDArray[] = $player->getPlayerID();
            $keyCollection->add($playerIDArray);
            $playerIDArray = array();
            $playerIDArray[] = $this->c_playerIDType;
            $playerIDArray[] = $playerTwo->getPlayerID();
            $keyCollection->add($playerIDArray);
            $textValue = "<a href=\"http://www.shl-wpg.ca/playerDetails.php?playerid=" . $player->getPlayerID() . "\">" . $player->getFullName() . "</a> and  <a href=\"http://www.shl-wpg.ca/playerDetails.php?playerid=" . $playerTwo->getPlayerID() . "\">" . $playerTwo->getFullName() . "</a>: " . $recordHolder[2] . " game points.";

            $this->addRecordHolder($textValue, $p_record, $keyCollection);

        }
    }  
    
    
    
    
    
    
    
    
    /*
     * NAME:    regularSeasonBestMatchUp
     * PARAMS:  N/A
     * DESC:    get the best regular season matchup
     */
    public function regularSeasonBestMatchUp(){
        //variable declaration
        return $this->getMatchUpInSeason(0, 41);
    }  
    
    
    
    
    
     /*
     * NAME:    getMatchUpInSeason
     * PARAMS:  N/A
     * DESC:    creates the best player matchup record
     *
     */
    public function getMatchUpInSeason($p_playoff, $p_record){   
        //variable declaration
        $sql = "";    
        $playerAr = array();
        $d = new db(0);
        $sql = "
            SELECT SeasonID, Playoff, FirstPlayerID, SecondPlayerID, Hookup 
            FROM SHL.QuickHookupSummary
            WHERE Playoff = " .  db::fmt($p_playoff,1) . " 
            AND Hookup = (
              SELECT QHS.Hookup
              FROM QuickHookupSummary AS QHS
              WHERE QHS.Playoff = " .  db::fmt($p_playoff,1) . " 
              ORDER BY QHS.Hookup DESC
              LIMIT 1
            )";
        //fetch the data
        $data = $d->fetch($sql);

        //fill the data
        foreach($data as $row) {
            $found = false;
            $tempArr = array();
            foreach($playerAr as $playerData){
                if(($playerData[0] == trim($row['SecondPlayerID'])) && ($playerData[1] == trim($row['FirstPlayerID'])) && ($playerData[2] == trim($row['SeasonID'])) && ($playerData[3] == trim($row['Playoff']))){
                    $found = true;
                }
            }
            if(!$found){
                $tempArr[] = trim($row['FirstPlayerID']);
                $tempArr[] = trim($row['SecondPlayerID']);
  
                $tempArr[] = trim($row['SeasonID']);
                $tempArr[] = trim($row['Playoff']);
                $tempArr[] = trim($row['Hookup']);
                $playerAr[] = $tempArr;
            }
        
        }
        
        //create the records
       foreach($playerAr as $recordHolder){
            $textValue = "";
            
            $player = new player($recordHolder[0]);
            $playerTwo = new player($recordHolder[1]);
            $keyCollection = new collection();
            $playerIDArray = array();
            $playerIDArray[] = $this->c_playerIDType;
            $playerIDArray[] = $player->getPlayerID();
            $keyCollection->add($playerIDArray);
            $playerIDArray = array();
            $playerIDArray[] = $this->c_playerIDType;
            $playerIDArray[] = $playerTwo->getPlayerID();
            $keyCollection->add($playerIDArray);
            $textValue = "<a href=\"http://www.shl-wpg.ca/playerDetails.php?playerid=" . $player->getPlayerID() . "\">" . $player->getFullName() . "</a> and  <a href=\"http://www.shl-wpg.ca/playerDetails.php?playerid=" . $playerTwo->getPlayerID() . "\">" . $playerTwo->getFullName() . "</a>: " . $recordHolder[4] . " game points <small>(Season " . $recordHolder[2] . ")</small>";

            $this->addRecordHolder($textValue, $p_record, $keyCollection);

        }
    } 
    
    
    
    
        /*
     * NAME:    getCurrentRegSeasonPlayerHotStreak
     * PARAMS:  N/A
     * DESC:    get an array of current goalie hot streak
     */
    public function getCurrentRegSeasonPlayerHotStreak(){
        //variable declaration
        return $this->getCurrentSeasonPlayerWinStreak(0, 2);
    }  
    
        /*
     * NAME:    getCurrentRegSeasonGoalieHotStreak
     * PARAMS:  N/A
     * DESC:    get an array of current goalie hot streak
     */
    public function getCurrentRegSeasonGoalieHotStreak(){
        //variable declaration
        return $this->getCurrentSeasonPlayerWinStreak(0, 1);
    }  
    
     /*
     * NAME:    getCurrentSeasonPlayerWinStreak
     * PARAMS:  N/A
     * DESC:    get a collection of the current win streaks
     *
     */
    public function getCurrentSeasonPlayerWinStreak($p_playoff, $p_position){
        //variable declaration
        $sql = "";    
        $d = new db(0);
        $playerAr = array();
        $curID = 0;
        $curCount = 1;
        $storedCount = 0;
        $curRecord = 1;
        $curSeason = 0;
        $highestGame = 0;
        $quickFlip = false;
        $shouldStore = false;
        $seasonList = "";
        $seasonText = "";
        $storedSeason = "";
        
        
        $sql = "
            SELECT MAX(GameNum) AS Highest
            FROM    Game
            WHERE   SeasonID = " . game::getMaxSeason();
        if($p_playoff >= 0){
           $sql = $sql . " AND Playoff = " .  db::fmt($p_playoff,1);
        }
        
        //fetch the data
        $data = $d->fetch($sql); 

        foreach($data as $row) {
            $highestGame = $row["Highest"];
        }
        
        
        $sql = "
            SELECT PGR.PlayerID, PGR.SeasonID, PGR.Playoff, PGR.GameNum, GameResult, Player.FName, Player.LName
            FROM QuickPlayerGameResult AS PGR
            INNER JOIN Player ON PGR.PlayerID = Player.PlayerID
            WHERE PGR.SeasonID = " . game::getMaxSeason();
        if($p_playoff >= 0){
           $sql = $sql . " AND PGR.Playoff = " .  db::fmt($p_playoff,1);
        }
        if($p_position >= 1){
           $sql = $sql . " AND PGR.Position = " .  db::fmt($p_position,1);
        }
       
        
        $sql = $sql . " ORDER BY PGR.PlayerID, PGR.SeasonID, PGR.Playoff, PGR.GameNum ASC";
  
        
        //fetch the data
        $data1 = $d->fetch($sql);


  
        
        //fill the data
        foreach($data1 as $row) {
            if($curID != $row["PlayerID"]){
                
                if($quickFlip){
                    
                    if($curCount > $curRecord){

                        
                        $curRecord = $curCount;
                        $playerAr = array();
                        $playerVals = array();
                        $playerVals[] = $curID;
                        $playerVals[] = $curCount;
                        $playerAr[] = $playerVals;
                    }elseif($curCount == $curRecord){

                        $playerVals = array();
                        $playerVals[] = $curID;
                        $playerVals[] = $curCount;
                        $playerAr[] = $playerVals;
                    }
                }else{
                    $quickFlip = true;
                }
                $curID = $row["PlayerID"];
                $curCount = 1;
                $shouldStore = false;
            }
            
            if($row["GameNum"] >= ($highestGame - 6)){
                $shouldStore = true;
            }
 
            if($shouldStore){
                if($row["GameResult"] == "W"){
                    $curCount++;
                }else{
                   $curCount = 0;
                }   
            }
     
        }
       if($curCount > $curRecord){


            $curRecord = $curCount;
            $playerAr = array();
            $playerVals = array();
            $playerVals[] = $curID;
            $playerVals[] = $curCount;
            $playerAr[] = $playerVals;
        }elseif($curCount == $curRecord){

            $playerVals = array();
            $playerVals[] = $curID;
            $playerVals[] = $curCount;
            $playerAr[] = $playerVals;
        }
                    
                    
        return $playerAr;
    }
    
    
        
    
    
    
    
    
    
    
    
    
        /*
     * NAME:    regularSeasongPlayerGoalStreak
     * PARAMS:  N/A
     * DESC:    creates the regular season player goal streak
     */
    public function regularSeasongPlayerGoalStreak(){
        //variable declaration
        $data = $this->getPlayerPointStreak(0, 1, 40);
    }     
    
        /*
     * NAME:    regularSeasongPlayerAssistStreak
     * PARAMS:  N/A
     * DESC:    creates the regular season player assist streak
     */
    public function regularSeasongPlayerAssistStreak(){
        //variable declaration
        $data = $this->getPlayerPointStreak(0, 2, 39);
    }  
    
     /*
     * NAME:    getPlayerPointStreak
     * PARAMS:  N/A
     * DESC:    the record for player point streak
     *
     */
    public function getPlayerPointStreak($p_playoff, $p_pointType, $p_record){ 
        //variable declaration
        $sql = "";    
        $d = new db(0);
        $quickFlip = false;
        $curGameID = 0;
        $curPlayerID = 0;
        $curCount = 0;
        $curRecord = 0;
        $playerAr = array();
        $playerVals = array();

        $sql = "
            SELECT      G.SeasonID, G.GameNum, G.GameID, P.PlayerID, P.FName, P.LName
            FROM        Player AS P
            INNER JOIN  TeamPlayer AS TP ON P.PlayerID = TP.PlayerID
            INNER JOIN  Point AS PT ON TP.TeamPlayerID = PT.TeamPlayerID
            INNER JOIN  Game AS G ON TP.GameID = G.GameID
            WHERE       1 = 1
            AND         PT.PointType = " . $p_pointType;

        if($p_playoff >= 0){
           $sql = $sql . " AND G.Playoff = " .  db::fmt($p_playoff,1);
        }        
        
        $sql = $sql . "  ORDER BY    G.SeasonID, G.Playoff, G.GameID, TP.Color, PT.PointNum";
        
        //fetch the data
        $data = $d->fetch($sql);

        //fill the data
        foreach($data as $row) {
            if(($curGameID != $row["GameID"]) || ($curPlayerID != $row["PlayerID"])){
                
                if($quickFlip){
                    if($curCount > $curRecord){
                        $playerAr = array();
                        $playerVals = array();
                        $playerVals[] = $curPlayerID;
                        $playerVals[] = $curCount;
                        $playerVals[] = $curGameID;
                        $playerAr[] = $playerVals;
                        $curRecord = $curCount;
                    }elseif($curCount == $curRecord){
                        $playerVals = array();
                        $playerVals[] = $curPlayerID;
                        $playerVals[] = $curCount;
                        $playerVals[] = $curGameID;
                        $playerAr[] = $playerVals;
                    }

                }else{
                    $quickFlip = true;
                }
                $curGameID = $row["GameID"];
                $curPlayerID = $row["PlayerID"];
                $curCount = 0;
            }


            $curCount++;
        }






        foreach($playerAr as $recordHolder){
            $textValue = "";
            $player = new player($recordHolder[0]);
            $keyCollection = new collection();
            $game = new game($recordHolder[2]);
            $playerIDArray = array();
            $playerIDArray[] = $this->c_playerIDType;
            $playerIDArray[] = $player->getPlayerID();
            $keyCollection->add($playerIDArray);
            $gameText = " <small>(Game <a href=\"http://www.shl-wpg.ca/gameDetails.php?gameid=" . $game->getGameID() . "\">". $game->getGameNum() . "</a> Season " . $game->getSeasonID() . ")</small>";
            $pointText = "";
            if($p_pointType == 1){
                $pointText = "goals";
            }else{
                $pointText = "assists";
            }
            $textValue = "<a href=\"http://www.shl-wpg.ca/playerDetails.php?playerid=" . $player->getPlayerID() . "\">" . $player->getFullName() . "</a>: " . $recordHolder[1] . " " . $pointText . " " . $gameText;

            $this->addRecordHolder($textValue, $p_record, $keyCollection);

        }

    }   
    
    
    
    
        /*
     * NAME:    regularSeasongPlayerWinStreakInOneSeason
     * PARAMS:  N/A
     * DESC:    creates the regular season player win streak
     */
    public function regularSeasongPlayerWinStreakInOneSeason(){
        //variable declaration
        $data = $this->getPlayerWinStreak(0, 2, true, 38);
    }  
    
        /*
     * NAME:    regularSeasongGoalieWinStreakInOneSeason
     * PARAMS:  N/A
     * DESC:    creates the regular season goalie win streak
     */
    public function regularSeasongGoalieWinStreakInOneSeason(){
        //variable declaration
        $data = $this->getPlayerWinStreak(0, 1, true, 37);
    }
        /*
     * NAME:    regularSeasongPlayerWinStreak
     * PARAMS:  N/A
     * DESC:    creates the regular season player win streak
     */
    public function regularSeasongPlayerWinStreak(){
        //variable declaration
        $data = $this->getPlayerWinStreak(0, 2, false, 36);
    }  
    
        /*
     * NAME:    regularSeasongGoalieWinStreak
     * PARAMS:  N/A
     * DESC:    creates the regular season goalie win streak
     */
    public function regularSeasongGoalieWinStreak(){
        //variable declaration
        $data = $this->getPlayerWinStreak(0, 1, false, 35);
    }
    
    
    
     /*
     * NAME:    getPlayerWinStreak
     * PARAMS:  N/A
     * DESC:    the record for player win streak
     *
     */
    public function getPlayerWinStreak($p_playoff, $p_position, $p_perSeason, $p_record){
        //variable declaration
        $sql = "";    
        $d = new db(0);
        $goalieArray = array();
        $curID = 0;
        $curCount = 0;
        $storedCount = 0;
        $curRecord = 0;
        $curSeason = 0;
        $quickFlip = false;
        $seasonList = "";
        $seasonText = "";
        $storedSeason = "";
        
        $sql = "
            SELECT QuickPlayerGameResult.PlayerID, SeasonID, Playoff, GameNum, GameResult, Player.FName, Player.LName
            FROM QuickPlayerGameResult
            INNER JOIN Player ON QuickPlayerGameResult.PlayerID = Player.PlayerID
            WHERE 1 = 1 ";
        if($p_playoff >= 0){
           $sql = $sql . " AND Playoff = " .  db::fmt($p_playoff,1);
        }
        if($p_position >= 1){
           $sql = $sql . " AND Position = " .  db::fmt($p_position,1);
        }
        
        $sql = $sql . " ORDER BY PlayerID, SeasonID, Playoff, GameNum";
        
        
        //fetch the data
        $data1 = $d->fetch($sql);

        //fill the data
        foreach($data1 as $row) {
            if(($curID != $row["PlayerID"]) || (($p_perSeason == true) && ($curSeason != $row["SeasonID"]))){
                if($quickFlip){
                    if($storedCount > $curRecord){
                        $curRecord = $storedCount;
                        $goalieArray = array();
                        $goalieVals = array();
                        $goalieVals[] = $curID;
                        $goalieVals[] = $storedCount;
                        $goalieVals[] = $storedSeason;
                        $goalieArray[] = $goalieVals;
                    }elseif($storedCount == $curRecord){
                        $goalieVals = array();
                        $goalieVals[] = $curID;
                        $goalieVals[] = $storedCount;
                        $goalieVals[] = $storedSeason;
                        $goalieArray[] = $goalieVals;
                    }
                }else{
                    $quickFlip = true;
                }
                $curID = $row["PlayerID"];
                $curSeason = $row["SeasonID"];
                $curCount = 0;
                $storedCount = 0;
                $seasonList = "";
            }else{
                
                
            }
          
            
            
            
            if($row["GameResult"] == "W"){
                if(($curID == $row["PlayerID"]) && ((strstr($seasonList, trim($row["SeasonID"])) == false))){
                    if(strlen(trim($seasonList)) == 0 ){
                        $seasonList = $row["SeasonID"];
                    }else{
                        $seasonList = $seasonList . ", " . $row["SeasonID"];
                    }
                    
                }
                $curCount++;
            }else{
               if($curCount > $storedCount){
                    $storedCount =  $curCount;
                    $storedSeason = $seasonList;
               }
               $seasonList = "";
               $curCount = 0;
            }   
        }
        foreach($goalieArray as $recordHolder){
            $player = new player($recordHolder[0]);
            $keyCollection = new collection();

            $playerIDArray = array();
            $playerIDArray[] = $this->c_playerIDType;
            $playerIDArray[] = $player->getPlayerID();
            $keyCollection->add($playerIDArray);
            $seasonText = "";
            if(strlen(trim($seasonList)) <= 2){
                $seasonText = "during Season " . $recordHolder[2];
            }else{
                $seasonText = "during Seasons " . $recordHolder[2];
            }
            $textValue = "<a href=\"http://www.shl-wpg.ca/playerDetails.php?playerid=" . $player->getPlayerID() . "\">" . $player->getFullName() . "</a>: " . $recordHolder[1] . " wins " . $seasonText;


            $this->addRecordHolder($textValue, $p_record, $keyCollection);

        }
        
        
    }
    
    
    
    
    
        /*
     * NAME:    regularSeasongMostPointsInGame
     * PARAMS:  N/A
     * DESC:    creates the regular season most points in one game
     */
    public function regularSeasongMostPointsInGame(){
        //variable declaration
        $data = $this->getPointCountInGame(0, 0, 34);
    }

        /*
     * NAME:    regularSeasongMostAssistsInGame
     * PARAMS:  N/A
     * DESC:    creates the regular season most assists in one game
     */
    public function regularSeasongMostAssistsInGame(){
        //variable declaration
        $data = $this->getPointCountInGame(0, 2, 33);
    }
    
        /*
     * NAME:    regularSeasongMostGoalsInGame
     * PARAMS:  N/A
     * DESC:    creates the regular season most goals in one game
     */
    public function regularSeasongMostGoalsInGame(){
        //variable declaration
        $data = $this->getPointCountInGame(0, 1, 32);
    }
    
     /*
     * NAME:    getPointCountInGame
     * PARAMS:  N/A
     * DESC:    creates the point count in game record
     *
     */
    public function getPointCountInGame($p_playoff, $p_pointType, $p_record){
        //variable declaration
        $sql = "";
        $textValue = "";
        $goalieName = "";


        $keyCollection = new collection();

        //database connection
        $d = new db(0);


       $sql = " SELECT TeamPlayerID, PlayerID, GameNum, GameID, SeasonID,  PointCount
                FROM (
                    SELECT TP.TeamPlayerID, TP.PlayerID, G.GameNum, G.GameID, G.SeasonID,  COUNT(PointType) AS PointCount
                    FROM Point 
                    INNER JOIN TeamPlayer AS TP ON Point.TeamPlayerID = TP.TeamPlayerID
                    INNER JOIN Game AS G ON TP.GameID = G.GameID
                    WHERE 1 = 1 ";
        if($p_playoff >= 0){
           $sql = $sql . " AND G.Playoff = " .  db::fmt($p_playoff,1);
        }
        
        if($p_pointType > 0){
           $sql = $sql . " AND Point.PointType = " .  db::fmt($p_pointType,1);
        }
        
        $sql = $sql . " GROUP BY TP.TeamPlayerID, TP.PlayerID, G.GameNum, G.GameID, G.SeasonID
                ) AS PointCounter
                WHERE PointCount = (";

       $sql = $sql . " SELECT MAX(PointCount) As PointCount
                FROM (
                    SELECT TP.TeamPlayerID, TP.PlayerID, G.GameNum, G.GameID, G.SeasonID,  COUNT(PointType) AS PointCount
                    FROM Point 
                    INNER JOIN TeamPlayer AS TP ON Point.TeamPlayerID = TP.TeamPlayerID
                    INNER JOIN Game AS G ON TP.GameID = G.GameID
                    WHERE 1 = 1 ";
        if($p_playoff >= 0){
           $sql = $sql . " AND G.Playoff = " .  db::fmt($p_playoff,1);
        }
        
        if($p_pointType > 0){
           $sql = $sql . " AND Point.PointType = " .  db::fmt($p_pointType,1);
        }
        
        $sql = $sql . " GROUP BY TP.TeamPlayerID, TP.PlayerID, G.GameNum, G.GameID, G.SeasonID
                ) AS PointCounter2)";
        

        //fetch the data
        $data = $d->fetch($sql);

        //echo $d->log;
        //fill the data
        foreach($data as $row) {

            $player = new player($row['PlayerID']);
            $keyCollection = new collection();
            /*
            $this->c_seasonIDType = 1;
            $this->c_playerIDType = 2;
            */

            $playerIDArray = array();
            $playerIDArray[] = $this->c_playerIDType;
            $playerIDArray[] = $player->getPlayerID();
            $keyCollection->add($playerIDArray);
            $textValue = "<a href=\"http://www.shl-wpg.ca/playerDetails.php?playerid=" . $player->getPlayerID() . "\">" . $player->getFullName() . "</a>: " . $row['PointCount'] . " <small>(Game <a href=\"http://www.shl-wpg.ca/gameDetails.php?gameid=" . $row['GameID'] . "\">". $row['GameNum'] . "</a> Season " . $row['SeasonID'] . ")</small>";


            $this->addRecordHolder($textValue, $p_record, $keyCollection);
        }
    }    
   
    
    
    
    
    
    
    
    
    
    
    
    
        /*
     * NAME:    regularSeasongPlayerMostGamesInSeason
     * PARAMS:  N/A
     * DESC:    creates the regular season most games played as player
     */
    public function regularSeasongPlayerMostGamesInSeason(){
        //variable declaration
        $data = $this->getPlayerMostGamesInSeason(0, 30);
    }
 
    
     /*
     * NAME:    getPlayerMostGamesInSeason
     * PARAMS:  N/A
     * DESC:    creates the most player played games
     *
     */
    public function getPlayerMostGamesInSeason($p_playoff, $p_record){
        //variable declaration
        $sql = "";
        $textValue = "";
        $goalieName = "";


        $keyCollection = new collection();

        //database connection
        $d = new db(0);


       $sql = "
            SELECT PlayerID, SeasonID, (TotalWins+TotalLosses) AS GamesPlayed
            FROM(
                SELECT PlayerID, SeasonID, SUM(Wins) AS TotalWins, SUM(Losses) AS TotalLosses
                FROM QuickPlayerDataTable
                WHERE 1 = 1 ";
        if($p_playoff >= 0){
           $sql = $sql . " AND Playoff = " .  db::fmt($p_playoff,1);
        }
        $sql = $sql . " GROUP BY PlayerID, SeasonID
            ) AS TotalWinCount
            WHERE (TotalWins+TotalLosses) = (

                SELECT MAX(TotalWins+TotalLosses)
                FROM(
                    SELECT PlayerID, SeasonID, SUM(Wins) AS TotalWins, SUM(Losses) AS TotalLosses
                    FROM QuickPlayerDataTable
                    WHERE 1 = 1 ";
        if($p_playoff >= 0){
           $sql = $sql . " AND Playoff = " .  db::fmt($p_playoff,1);
        }
        $sql = $sql . " GROUP BY PlayerID, SeasonID
                ) AS TotalWinSubCount)"; 

        //fetch the data
        $data = $d->fetch($sql);

        //echo $d->log;
        //fill the data
        foreach($data as $row) {

            $player = new player($row['PlayerID']);
            $keyCollection = new collection();
            /*
            $this->c_seasonIDType = 1;
            $this->c_playerIDType = 2;
            */

            $playerIDArray = array();
            $playerIDArray[] = $this->c_playerIDType;
            $playerIDArray[] = $player->getPlayerID();
            $keyCollection->add($playerIDArray);
            $textValue = "<a href=\"http://www.shl-wpg.ca/playerDetails.php?playerid=" . $player->getPlayerID() . "\">" . $player->getFullName() . "</a>: " . $row['GamesPlayed'] . " games played <small>(Season " . $row['SeasonID'] . ")</small>";


            $this->addRecordHolder($textValue, $p_record, $keyCollection);
        }
    }        
            
    
        
    

    
        /*
     * NAME:    regularSeasongPlayerPlusMinusInSeason
     * PARAMS:  N/A
     * DESC:    creates the regular season best player +/-
     */
    public function regularSeasongPlayerPlusMinusInSeason(){
        //variable declaration
        $data = $this->getPlayerPlusMinusInSeason(0, 29);
    }
 
    
    
    
     /*
     * NAME:    getPlayerPlusMinusInSeason
     * PARAMS:  N/A
     * DESC:    creates the best player +/- record
     *
     */
    public function getPlayerPlusMinusInSeason($p_playoff, $p_record){
        //variable declaration
        $sql = "";
        $textValue = "";
        $goalieName = "";


        $keyCollection = new collection();

        //database connection
        $d = new db(0);


       $sql = "
            SELECT PlayerID, SeasonID, (TotalWins-TotalLosses) AS PlusMinus
            FROM(
                SELECT PlayerID, SeasonID, SUM(Wins) AS TotalWins, SUM(Losses) AS TotalLosses
                FROM QuickPlayerDataTable
                WHERE 1 = 1 ";
        if($p_playoff >= 0){
           $sql = $sql . " AND Playoff = " .  db::fmt($p_playoff,1);
        }
        $sql = $sql . " GROUP BY PlayerID, SeasonID
            ) AS TotalWinCount
            WHERE (TotalWins-TotalLosses) = (

                SELECT MAX(TotalWins-TotalLosses)
                FROM(
                    SELECT PlayerID, SeasonID, SUM(Wins) AS TotalWins, SUM(Losses) AS TotalLosses
                    FROM QuickPlayerDataTable
                    WHERE 1 = 1 ";
        if($p_playoff >= 0){
           $sql = $sql . " AND Playoff = " .  db::fmt($p_playoff,1);
        }
        $sql = $sql . " GROUP BY PlayerID, SeasonID
                ) AS TotalWinSubCount)"; 

        //fetch the data
        $data = $d->fetch($sql);

        //echo $d->log;
        //fill the data
        foreach($data as $row) {

            $player = new player($row['PlayerID']);
            $keyCollection = new collection();
            /*
            $this->c_seasonIDType = 1;
            $this->c_playerIDType = 2;
            */

            $playerIDArray = array();
            $playerIDArray[] = $this->c_playerIDType;
            $playerIDArray[] = $player->getPlayerID();
            $keyCollection->add($playerIDArray);
            $textValue = "<a href=\"http://www.shl-wpg.ca/playerDetails.php?playerid=" . $player->getPlayerID() . "\">" . $player->getFullName() . "</a>: " . $row['PlusMinus'] . " <small>(Season " . $row['SeasonID'] . ")</small>";


            $this->addRecordHolder($textValue, $p_record, $keyCollection);
        }
    }        
            
    
    
    
    
    
    
    
        /*
     * NAME:    regularSeasongPlayerPlusMinus
     * PARAMS:  N/A
     * DESC:    creates the regular season best player +/-
     */
    public function regularSeasongPlayerPlusMinus(){
        //variable declaration
        $data = $this->getPlayerPlusMinus(0, 28);
    }
 
    
    
    
     /*
     * NAME:    getGoaliePlusMinus
     * PARAMS:  N/A
     * DESC:    creates the best player +/- record
     *
     */
    public function getPlayerPlusMinus($p_playoff, $p_record){
        //variable declaration
        $sql = "";
        $textValue = "";
        $goalieName = "";


        $keyCollection = new collection();

        //database connection
        $d = new db(0);

        //set up the sql
           
       $sql = "
            SELECT PlayerID, (TotalWins-TotalLosses) AS PlusMinus
            FROM(
                SELECT PlayerID, SUM(Wins) AS TotalWins, SUM(Losses) AS TotalLosses
                FROM QuickPlayerDataTable
                WHERE 1 = 1 ";
        if($p_playoff >= 0){
           $sql = $sql . " AND Playoff = " .  db::fmt($p_playoff,1);
        }
        $sql = $sql . " GROUP BY PlayerID
            ) AS TotalWinCount
            WHERE (TotalWins-TotalLosses) = (

                SELECT MAX(TotalWins-TotalLosses)
                FROM(
                    SELECT PlayerID, SUM(Wins) AS TotalWins, SUM(Losses) AS TotalLosses
                    FROM QuickPlayerDataTable
                    WHERE 1 = 1 ";
        if($p_playoff >= 0){
           $sql = $sql . " AND Playoff = " .  db::fmt($p_playoff,1);
        }
        $sql = $sql . " GROUP BY PlayerID
                ) AS TotalWinSubCount)"; 

        //fetch the data
        $data = $d->fetch($sql);



        //fill the data
        foreach($data as $row) {

            $player = new player($row['PlayerID']);
            $keyCollection = new collection();
            /*
            $this->c_seasonIDType = 1;
            $this->c_playerIDType = 2;
            */

            $playerIDArray = array();
            $playerIDArray[] = $this->c_playerIDType;
            $playerIDArray[] = $player->getPlayerID();
            $keyCollection->add($playerIDArray);
            $textValue = "<a href=\"http://www.shl-wpg.ca/playerDetails.php?playerid=" . $player->getPlayerID() . "\">" . $player->getFullName() . "</a>: " . $row['PlusMinus'];


            $this->addRecordHolder($textValue, $p_record, $keyCollection);
        }
    }        
          
    
    
    
    
    
  

        /*
     * NAME:    regularSeasonMostPlayerWins
     * PARAMS:  N/A
     * DESC:    creates the regular season most player wins
     */
    public function regularSeasonMostPlayerWins(){
        //variable declaration
        $data = $this->getMostPlayerWins(0, 27);
    }
 
    
    
    
     /*
     * NAME:    getMostPlayerWins
     * PARAMS:  N/A
     * DESC:    creates the most player wins record
     *
     */
    public function getMostPlayerWins($p_playoff, $p_record){
        //variable declaration
        $sql = "";
        $textValue = "";
        $goalieName = "";


        $keyCollection = new collection();

        //database connection
        $d = new db(0);

           
       $sql = "
            SELECT PlayerID, TotalWins
            FROM(
                SELECT PlayerID, SUM(Wins) AS TotalWins
                FROM QuickPlayerDataTable
                WHERE 1 = 1 ";
        if($p_playoff >= 0){
           $sql = $sql . " AND Playoff = " .  db::fmt($p_playoff,1);
        }
        $sql = $sql . " GROUP BY PlayerID
            ) AS TotalWinCount
            WHERE TotalWins = (

                SELECT MAX(TotalWins)
                FROM(
                    SELECT PlayerID, SUM(Wins) AS TotalWins
                    FROM QuickPlayerDataTable
                    WHERE 1 = 1 ";
        if($p_playoff >= 0){
           $sql = $sql . " AND Playoff = " .  db::fmt($p_playoff,1);
        }
        $sql = $sql . " GROUP BY PlayerID
                ) AS TotalWinSubCount)"; 
           
           

        //fetch the data
        $data = $d->fetch($sql);



        //fill the data
        foreach($data as $row) {

            $player = new player($row['PlayerID']);
            $keyCollection = new collection();
            /*
            $this->c_seasonIDType = 1;
            $this->c_playerIDType = 2;
            */

            $playerIDArray = array();
            $playerIDArray[] = $this->c_playerIDType;
            $playerIDArray[] = $player->getPlayerID();
            $keyCollection->add($playerIDArray);
            $textValue = "<a href=\"http://www.shl-wpg.ca/playerDetails.php?playerid=" . $player->getPlayerID() . "\">" . $player->getFullName() . "</a>: " . $row['TotalWins'] . " Player wins.";


            $this->addRecordHolder($textValue, $p_record, $keyCollection);
        }
    }
        
    
    

        /*
     * NAME:    regulargetBestPlayerPPG
     * PARAMS:  N/A
     * DESC:    creates the regular season best PPG
     */
    public function regulargetBestPlayerPPG(){
        //variable declaration
        $data = $this->getBestPlayerPPG(0, 26);
    }
 
    
    
    
     /*
     * NAME:    getBestPlayerPPG
     * PARAMS:  N/A
     * DESC:    creates the best player PPG
     *
     */
    public function getBestPlayerPPG($p_playoff, $p_record){
        //variable declaration
        $sql = "";
        $textValue = "";
        $goalieName = "";


        $keyCollection = new collection();

        //database connection
        $d = new db(0);
//
//        //set up the sql
//        $sql = "
//            SELECT PlayerID, (P/GP) AS PPG, GP, P
//            FROM (
//
//                SELECT PlayerID, SUM(Goals+Assists) AS P, SUM(Wins + Losses) AS GP
//                FROM QuickPlayerDataTable 
//                WHERE 1 = 1";
//        if($p_playoff >= 0){
//           $sql = $sql . " AND Playoff = " .  db::fmt($p_playoff,1);
//        }
//        
//           $sql = $sql .  "   GROUP BY PlayerID
//            ) AS PlayerGAACalc
//            WHERE GP > 20
//            ORDER BY (P/GP) DESC
//            LIMIT 1";

           
       $sql = "
            SELECT PlayerID, (P/(GP)) AS PPG, GP, P
            FROM(
                SELECT PlayerID, SUM(Wins+Losses) AS GP, SUM(Goals+Assists) AS P
                FROM QuickPlayerDataTable
                WHERE 1 = 1 ";
        if($p_playoff >= 0){
           $sql = $sql . " AND Playoff = " .  db::fmt($p_playoff,1);
        }
        $sql = $sql . " GROUP BY PlayerID
            ) AS TotalWinCount
            WHERE (P/GP) = (

                SELECT MAX(P/GP)
                FROM(
                    SELECT PlayerID, SUM(Wins+Losses) AS GP, SUM(Goals+Assists) AS P
                    FROM QuickPlayerDataTable
                    WHERE 1 = 1 ";
        if($p_playoff >= 0){
           $sql = $sql . " AND Playoff = " .  db::fmt($p_playoff,1);
        }
        $sql = $sql . " GROUP BY PlayerID
                ) AS TotalWinSubCount
                    WHERE GP > 20
                )
                AND GP > 20";               
           
           
           
        //fetch the data
        $data = $d->fetch($sql);



        //fill the data
        foreach($data as $row) {

            $player = new player($row['PlayerID']);
            $keyCollection = new collection();
            /*
            $this->c_seasonIDType = 1;
            $this->c_playerIDType = 2;
            */

            $playerIDArray = array();
            $playerIDArray[] = $this->c_playerIDType;
            $playerIDArray[] = $player->getPlayerID();
            $keyCollection->add($playerIDArray);
            $textValue = "<a href=\"http://www.shl-wpg.ca/playerDetails.php?playerid=" . $player->getPlayerID() . "\">" . $player->getFullName() . "</a>: " . number_format($row['PPG'], 2, '.', '') . " PPG. <small>(" . $row['P'] . " points in " . $row['GP'] . " games played.)</small>";


            $this->addRecordHolder($textValue, $p_record, $keyCollection);
        }
    }
  
    
    
        
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
        /*
     * NAME:    regularSeasongGoalieMostGamesInSeason
     * PARAMS:  N/A
     * DESC:    creates the regular season most games played as goalie
     */
    public function regularSeasongGoalieMostGamesInSeason(){
        //variable declaration
        $data = $this->getGoalieMostGamesInSeason(0, 25);
    }
 
    
     /*
     * NAME:    getGoalieMostGamesInSeason
     * PARAMS:  N/A
     * DESC:    creates the most goalie played games
     *
     */
    public function getGoalieMostGamesInSeason($p_playoff, $p_record){
        //variable declaration
        $sql = "";
        $textValue = "";
        $goalieName = "";


        $keyCollection = new collection();

        //database connection
        $d = new db(0);


       $sql = "
            SELECT PlayerID, SeasonID, (TotalWins+TotalLosses) AS GamesPlayed
            FROM(
                SELECT PlayerID, SeasonID, SUM(Wins) AS TotalWins, SUM(Losses) AS TotalLosses
                FROM QuickGoalieDataTable
                WHERE 1 = 1 ";
        if($p_playoff >= 0){
           $sql = $sql . " AND Playoff = " .  db::fmt($p_playoff,1);
        }
        $sql = $sql . " GROUP BY PlayerID, SeasonID
            ) AS TotalWinCount
            WHERE (TotalWins+TotalLosses) = (

                SELECT MAX(TotalWins+TotalLosses)
                FROM(
                    SELECT PlayerID, SeasonID, SUM(Wins) AS TotalWins, SUM(Losses) AS TotalLosses
                    FROM QuickGoalieDataTable
                    WHERE 1 = 1 ";
        if($p_playoff >= 0){
           $sql = $sql . " AND Playoff = " .  db::fmt($p_playoff,1);
        }
        $sql = $sql . " GROUP BY PlayerID, SeasonID
                ) AS TotalWinSubCount)"; 

        //fetch the data
        $data = $d->fetch($sql);

        //echo $d->log;
        //fill the data
        foreach($data as $row) {

            $player = new player($row['PlayerID']);
            $keyCollection = new collection();
            /*
            $this->c_seasonIDType = 1;
            $this->c_playerIDType = 2;
            */

            $playerIDArray = array();
            $playerIDArray[] = $this->c_playerIDType;
            $playerIDArray[] = $player->getPlayerID();
            $keyCollection->add($playerIDArray);
            $textValue = "<a href=\"http://www.shl-wpg.ca/playerDetails.php?playerid=" . $player->getPlayerID() . "\">" . $player->getFullName() . "</a>: " . $row['GamesPlayed'] . " games played <small>(Season " . $row['SeasonID'] . ")</small>";


            $this->addRecordHolder($textValue, $p_record, $keyCollection);
        }
    }        
            
    
        
    

    
        /*
     * NAME:    regularSeasongGoaliePlusMinusInSeason
     * PARAMS:  N/A
     * DESC:    creates the regular season best goalie +/-
     */
    public function regularSeasongGoaliePlusMinusInSeason(){
        //variable declaration
        $data = $this->getGoaliePlusMinusInSeason(0, 24);
    }
 
    
    
    
     /*
     * NAME:    getGoaliePlusMinusInSeason
     * PARAMS:  N/A
     * DESC:    creates the best goalie +/- record
     *
     */
    public function getGoaliePlusMinusInSeason($p_playoff, $p_record){
        //variable declaration
        $sql = "";
        $textValue = "";
        $goalieName = "";


        $keyCollection = new collection();

        //database connection
        $d = new db(0);


       $sql = "
            SELECT PlayerID, SeasonID, (TotalWins-TotalLosses) AS PlusMinus
            FROM(
                SELECT PlayerID, SeasonID, SUM(Wins) AS TotalWins, SUM(Losses) AS TotalLosses
                FROM QuickGoalieDataTable
                WHERE 1 = 1 ";
        if($p_playoff >= 0){
           $sql = $sql . " AND Playoff = " .  db::fmt($p_playoff,1);
        }
        $sql = $sql . " GROUP BY PlayerID, SeasonID
            ) AS TotalWinCount
            WHERE (TotalWins-TotalLosses) = (

                SELECT MAX(TotalWins-TotalLosses)
                FROM(
                    SELECT PlayerID, SeasonID, SUM(Wins) AS TotalWins, SUM(Losses) AS TotalLosses
                    FROM QuickGoalieDataTable
                    WHERE 1 = 1 ";
        if($p_playoff >= 0){
           $sql = $sql . " AND Playoff = " .  db::fmt($p_playoff,1);
        }
        $sql = $sql . " GROUP BY PlayerID, SeasonID
                ) AS TotalWinSubCount)"; 

        //fetch the data
        $data = $d->fetch($sql);

        //echo $d->log;
        //fill the data
        foreach($data as $row) {

            $player = new player($row['PlayerID']);
            $keyCollection = new collection();
            /*
            $this->c_seasonIDType = 1;
            $this->c_playerIDType = 2;
            */

            $playerIDArray = array();
            $playerIDArray[] = $this->c_playerIDType;
            $playerIDArray[] = $player->getPlayerID();
            $keyCollection->add($playerIDArray);
            $textValue = "<a href=\"http://www.shl-wpg.ca/playerDetails.php?playerid=" . $player->getPlayerID() . "\">" . $player->getFullName() . "</a>: " . $row['PlusMinus'] . " <small>(Season " . $row['SeasonID'] . ")</small>";


            $this->addRecordHolder($textValue, $p_record, $keyCollection);
        }
    }        
            
    
    
    
    
    
    
    
        /*
     * NAME:    regularSeasongGoaliePlusMinus
     * PARAMS:  N/A
     * DESC:    creates the regular season best goalie +/-
     */
    public function regularSeasongGoaliePlusMinus(){
        //variable declaration
        $data = $this->getGoaliePlusMinus(0, 23);
    }
 
    
    
    
     /*
     * NAME:    getGoaliePlusMinus
     * PARAMS:  N/A
     * DESC:    creates the best goalie +/- record
     *
     */
    public function getGoaliePlusMinus($p_playoff, $p_record){
        //variable declaration
        $sql = "";
        $textValue = "";
        $goalieName = "";


        $keyCollection = new collection();

        //database connection
        $d = new db(0);

        //set up the sql
           
       $sql = "
            SELECT PlayerID, (TotalWins-TotalLosses) AS PlusMinus
            FROM(
                SELECT PlayerID, SUM(Wins) AS TotalWins, SUM(Losses) AS TotalLosses
                FROM QuickGoalieDataTable
                WHERE 1 = 1 ";
        if($p_playoff >= 0){
           $sql = $sql . " AND Playoff = " .  db::fmt($p_playoff,1);
        }
        $sql = $sql . " GROUP BY PlayerID
            ) AS TotalWinCount
            WHERE (TotalWins-TotalLosses) = (

                SELECT MAX(TotalWins-TotalLosses)
                FROM(
                    SELECT PlayerID, SUM(Wins) AS TotalWins, SUM(Losses) AS TotalLosses
                    FROM QuickGoalieDataTable
                    WHERE 1 = 1 ";
        if($p_playoff >= 0){
           $sql = $sql . " AND Playoff = " .  db::fmt($p_playoff,1);
        }
        $sql = $sql . " GROUP BY PlayerID
                ) AS TotalWinSubCount)"; 

        //fetch the data
        $data = $d->fetch($sql);



        //fill the data
        foreach($data as $row) {

            $player = new player($row['PlayerID']);
            $keyCollection = new collection();
            /*
            $this->c_seasonIDType = 1;
            $this->c_playerIDType = 2;
            */

            $playerIDArray = array();
            $playerIDArray[] = $this->c_playerIDType;
            $playerIDArray[] = $player->getPlayerID();
            $keyCollection->add($playerIDArray);
            $textValue = "<a href=\"http://www.shl-wpg.ca/playerDetails.php?playerid=" . $player->getPlayerID() . "\">" . $player->getFullName() . "</a>: " . $row['PlusMinus'];


            $this->addRecordHolder($textValue, $p_record, $keyCollection);
        }
    }        
          
    
    
    
    
    
    
    
    
    
    
        /*
     * NAME:    regularSeasongGoalieHundredWinsClub
     * PARAMS:  N/A
     * DESC:    creates the regular season 100 win club
     */
    public function regularSeasongGoalieHundredWinsClub(){
        //variable declaration
        $data = $this->getGoalieHundredWinsClub(0, 22);
    }
 
    
    
    
     /*
     * NAME:    getGoalieHundredWinsClub
     * PARAMS:  N/A
     * DESC:    creates the 100 goalie win record
     *
     */
    public function getGoalieHundredWinsClub($p_playoff, $p_record){
        //variable declaration
        $sql = "";
        $textValue = "";
        $goalieName = "";


        $keyCollection = new collection();

        //database connection
        $d = new db(0);

        //set up the sql
        $sql = "
            SELECT PlayerID, TotalWins, TotalLosses
                FROM(
                SELECT PlayerID, SUM(Wins) AS TotalWins, SUM(Losses) AS TotalLosses
                FROM QuickGoalieDataTable
                WHERE 1 = 1";
        if($p_playoff >= 0){
           $sql = $sql . " AND Playoff = " .  db::fmt($p_playoff,1);
        }
        
           $sql = $sql .  "   GROUP BY PlayerID
                ) AS TotalWinCount
                WHERE TotalWins > 100
                ORDER BY TotalWins DESC
                ";

           
           

           
           
           
           
           
           
        //fetch the data
        $data = $d->fetch($sql);



        //fill the data
        foreach($data as $row) {

            $player = new player($row['PlayerID']);
            $keyCollection = new collection();
            /*
            $this->c_seasonIDType = 1;
            $this->c_playerIDType = 2;
            */

            $playerIDArray = array();
            $playerIDArray[] = $this->c_playerIDType;
            $playerIDArray[] = $player->getPlayerID();
            $keyCollection->add($playerIDArray);
            //Need to fix later to properly count the number of games.
            //$textValue = "<a href=\"http://www.shl-wpg.ca/playerDetails.php?playerid=" . $player->getPlayerID() . "\">" . $player->getFullName() . "</a>: " . $row['TotalWins'] . " Goalie wins in " . ($row['TotalWins']+$row['TotalLosses']) . " games.";
            $textValue = "<a href=\"http://www.shl-wpg.ca/playerDetails.php?playerid=" . $player->getPlayerID() . "\">" . $player->getFullName() . "</a>: 100 Goalie wins.";


            $this->addRecordHolder($textValue, $p_record, $keyCollection);
        }
    }        
      

        /*
     * NAME:    regularSeasonMostGoalieWins
     * PARAMS:  N/A
     * DESC:    creates the regular season most goalie wins
     */
    public function regularSeasonMostGoalieWins(){
        //variable declaration
        $data = $this->getMostGoalieWins(0, 21);
    }
 
    
    
    
     /*
     * NAME:    getMostGoalieWins
     * PARAMS:  N/A
     * DESC:    creates the most goalie wins record
     *
     */
    public function getMostGoalieWins($p_playoff, $p_record){
        //variable declaration
        $sql = "";
        $textValue = "";
        $goalieName = "";


        $keyCollection = new collection();

        //database connection
        $d = new db(0);

           
       $sql = "
            SELECT PlayerID, TotalWins
            FROM(
                SELECT PlayerID, SUM(Wins) AS TotalWins
                FROM QuickGoalieDataTable
                WHERE 1 = 1 ";
        if($p_playoff >= 0){
           $sql = $sql . " AND Playoff = " .  db::fmt($p_playoff,1);
        }
        $sql = $sql . " GROUP BY PlayerID
            ) AS TotalWinCount
            WHERE TotalWins = (

                SELECT MAX(TotalWins)
                FROM(
                    SELECT PlayerID, SUM(Wins) AS TotalWins
                    FROM QuickGoalieDataTable
                    WHERE 1 = 1 ";
        if($p_playoff >= 0){
           $sql = $sql . " AND Playoff = " .  db::fmt($p_playoff,1);
        }
        $sql = $sql . " GROUP BY PlayerID
                ) AS TotalWinSubCount)"; 
           
           

        //fetch the data
        $data = $d->fetch($sql);



        //fill the data
        foreach($data as $row) {

            $player = new player($row['PlayerID']);
            $keyCollection = new collection();
            /*
            $this->c_seasonIDType = 1;
            $this->c_playerIDType = 2;
            */

            $playerIDArray = array();
            $playerIDArray[] = $this->c_playerIDType;
            $playerIDArray[] = $player->getPlayerID();
            $keyCollection->add($playerIDArray);
            $textValue = "<a href=\"http://www.shl-wpg.ca/playerDetails.php?playerid=" . $player->getPlayerID() . "\">" . $player->getFullName() . "</a>: " . $row['TotalWins'] . " Goalie wins.";


            $this->addRecordHolder($textValue, $p_record, $keyCollection);
        }
    }
        
    
    

        /*
     * NAME:    regularSeasonBestGoalieGAA
     * PARAMS:  N/A
     * DESC:    creates the regular season best GAA
     */
    public function regularSeasonBestGoalieGAA(){
        //variable declaration
        $data = $this->getBestGoalieGoalsAgainstAverage(0, 20);
    }
 
    
    
    
     /*
     * NAME:    getBestGoalieGoalsAgainstAverage
     * PARAMS:  N/A
     * DESC:    creates the best goalie GAA record
     *
     */
    public function getBestGoalieGoalsAgainstAverage($p_playoff, $p_record){
        //variable declaration
        $sql = "";
        $textValue = "";
        $goalieName = "";


        $keyCollection = new collection();

        //database connection
        $d = new db(0);

//        //set up the sql
//        $sql = "
//            SELECT PlayerID, (GA/GP) AS GAA, GP, GA
//            FROM (
//
//                SELECT PlayerID, SUM(GoalsAgainst) AS GA, SUM(Wins + Losses) AS GP
//                FROM QuickGoalieDataTable 
//                WHERE 1 = 1";
//        if($p_playoff >= 0){
//           $sql = $sql . " AND Playoff = " .  db::fmt($p_playoff,1);
//        }
//        
//           $sql = $sql .  "   GROUP BY PlayerID
//            ) AS PlayerGAACalc
//            WHERE GP > 20
//            ORDER BY (GA/GP) ASC
//            LIMIT 1";
//           
           
           
       $sql = "
            SELECT PlayerID, (GA/(GP)) AS GAA, GP, GA
            FROM(
                SELECT PlayerID, SUM(Wins+Losses) AS GP, SUM(GoalsAgainst) AS GA
                FROM QuickGoalieDataTable
                WHERE 1 = 1 ";
        if($p_playoff >= 0){
           $sql = $sql . " AND Playoff = " .  db::fmt($p_playoff,1);
        }
        $sql = $sql . " GROUP BY PlayerID
            ) AS TotalWinCount
            WHERE (GA/GP) = (

                SELECT MIN(GA/GP)
                FROM(
                    SELECT PlayerID, SUM(Wins+Losses) AS GP, SUM(GoalsAgainst) AS GA
                    FROM QuickGoalieDataTable
                    WHERE 1 = 1 ";
        if($p_playoff >= 0){
           $sql = $sql . " AND Playoff = " .  db::fmt($p_playoff,1);
        }
        $sql = $sql . " GROUP BY PlayerID
                ) AS TotalWinSubCount
                    WHERE GP > 20
                )
                AND GP > 20";            
           
           

        //fetch the data
        $data = $d->fetch($sql);



        //fill the data
        foreach($data as $row) {

            $player = new player($row['PlayerID']);
            $keyCollection = new collection();
            /*
            $this->c_seasonIDType = 1;
            $this->c_playerIDType = 2;
            */

            $playerIDArray = array();
            $playerIDArray[] = $this->c_playerIDType;
            $playerIDArray[] = $player->getPlayerID();
            $keyCollection->add($playerIDArray);
            $textValue = "<a href=\"http://www.shl-wpg.ca/playerDetails.php?playerid=" . $player->getPlayerID() . "\">" . $player->getFullName() . "</a>: " . number_format($row['GAA'], 2, '.', '') . " GAA. <small>(" . $row['GA'] . " goals against in " . $row['GP'] . " games played.)</small>";


            $this->addRecordHolder($textValue, $p_record, $keyCollection);
        }
    }
  
    
    
    
    
    
    
    
    
    
    
    
    
    
    
     /*
     * NAME:    getMostCupWins
     * PARAMS:  N/A
     * DESC:    creates the most goals in a game records
     *
     */
    public function getMostCupWins(){
        //variable declaration
        $sql = "";
        $textValue = "";
        $goalieName = "";


        $keyCollection = new collection();

        //database connection
        $d = new db(0);

        //set up the sql
        $sql = "
            SELECT RepresentativeID, CUPWins
            FROM (
                SELECT RHI.RepresentativeID, COUNT(RHI.RepresentativeID) As CUPWins
                FROM RecordHolderID AS RHI
                INNER JOIN RecordHolder AS RH ON RHI.RecordHolderID = RH.RecordHolderID
                WHERE RH.RecordID = 18
                AND RHI.IDTypeID = 2
                GROUP BY RHI.RepresentativeID
            ) AS CupWinsSelect
            WHERE CupWinsSelect.CUPWins = (
                SELECT MAX(CUPWins) AS MaxCupWins
                FROM (
                    SELECT RHI.RepresentativeID, COUNT(RHI.RepresentativeID) As CUPWins
                    FROM RecordHolderID AS RHI
                    INNER JOIN RecordHolder AS RH ON RHI.RecordHolderID = RH.RecordHolderID
                    WHERE RH.RecordID = 18
                    AND RHI.IDTypeID = 2
                    GROUP BY RHI.RepresentativeID
                ) AS CupWinsSelect
            )";

        //fetch the data
        $data = $d->fetch($sql);



        //fill the data
        foreach($data as $row) {

            $player = new player($row['RepresentativeID']);
            $keyCollection = new collection();
            /*
            $this->c_seasonIDType = 1;
            $this->c_playerIDType = 2;
            */

            $playerIDArray = array();
            $playerIDArray[] = $this->c_playerIDType;
            $playerIDArray[] = $player->getPlayerID();
            $keyCollection->add($playerIDArray);
            $textValue = "<a href=\"http://www.shl-wpg.ca/playerDetails.php?playerid=" . $player->getPlayerID() . "\">" . $player->getFullName() . "</a>: " . $row['CUPWins'] . " cup wins.";


            $this->addRecordHolder($textValue, 19, $keyCollection);
        }
    }















     /*
     * NAME:    allSeasonCupWinners
     * PARAMS:  N/A
     * DESC:    creates the cup winner records
     *
     */
    public function allSeasonCupWinners(){
        //variable declaration
        $startSeason = 1;
        $maxSeason = game::getMaxSeason() - 1;
        $teamWinner = 0;
        $ls_winText = '';
        $ln_teamOne = 0;
        $ln_teamTwo = 0;
        

        //loop and make the records
         for($alpha = $startSeason; $alpha <= $maxSeason; $alpha += 1){
             $ls_winText = '';

            $ln_teamOne = $this->getPlayoffTeamWins($alpha, 1);

            $ln_teamTwo = $this->getPlayoffTeamWins($alpha, 2);

            //check to see who won
            if($ln_teamOne > $ln_teamTwo){
                $teamWinner = 1;
                $ls_winText = $ln_teamOne . '-' . $ln_teamTwo;
            }else{
                 $teamWinner = 2;
                 $ls_winText = $ln_teamTwo . '-' . $ln_teamOne;
            }
            $this->addSeasonCupWinner($alpha, $teamWinner, 18, $ls_winText);

         }


    }






     /*
     * NAME:    addSeasonCupWinner
     * PARAMS:  $p_season = season, $p_teamNum = initial team color
     * DESC:    adds the cup winner records
     *
     */
    public function addSeasonCupWinner($p_season, $p_teamNum, $p_recordID, $p_winText){
        //variable declaration
        $sql = "";
        $playerObj;
        $playerID = 0;
        $recordText = "";
        $playerName = "";
        $count = 0;

//        $sql = "
//            SELECT DISTINCT TP.PlayerID
//            FROM TeamPlayer AS TP
//            INNER JOIN (
//            SELECT DISTINCT G.GameID, TP.Color
//                        FROM TeamPlayer AS TP
//                        INNER JOIN Game AS G ON TP.GameID = G.GameID
//                        INNER JOIN (
//                            SELECT STP.PlayerID, SG.SeasonID, SG.Playoff
//                            FROM TeamPlayer AS STP
//                            INNER JOIN Game AS SG ON STP.GameID = SG.GameID
//                            WHERE SG.SeasonID = " . db::fmt($p_season,1)  . "
//                            AND SG.Playoff = 1
//                            AND SG.GameNum = 1
//                            AND STP.Color = " . db::fmt($p_teamNum,1)  . "
//                        ) AS KeyPlayers ON TP.PlayerID = KeyPlayers.PlayerID AND G.SeasonID = KeyPlayers.SeasonID AND G.Playoff = KeyPlayers.Playoff
//            ) AS WT ON TP.GameID = WT.GameID AND TP.Color = WT.Color
//            ";

        $sql = "            SELECT DISTINCT TP.PlayerID
            FROM TeamPlayer AS TP
            INNER JOIN (
            SELECT GameID, Color
FROM (
SELECT DISTINCT G.GameID, TP.Color, COUNT(TP.Color) As ColorCount
                        FROM TeamPlayer AS TP
                        INNER JOIN Game AS G ON TP.GameID = G.GameID
                        INNER JOIN (
                            SELECT STP.PlayerID, SG.SeasonID, SG.Playoff
                            FROM TeamPlayer AS STP
                            INNER JOIN Game AS SG ON STP.GameID = SG.GameID
                            WHERE SG.SeasonID = " . db::fmt($p_season,1)  . "
                            AND SG.Playoff = 1
                            AND SG.GameNum = 1
                            AND STP.Color = " . db::fmt($p_teamNum,1)  . "
                        ) AS KeyPlayers ON TP.PlayerID = KeyPlayers.PlayerID AND G.SeasonID = KeyPlayers.SeasonID AND G.Playoff = KeyPlayers.Playoff
GROUP BY G.GameID, TP.Color
) AS ColorSelector
WHERE ColorSelector.ColorCount > 1
            ) AS WT ON TP.GameID = WT.GameID AND TP.Color = WT.Color";

        //database connection
        $d = new db(0);
        //fetch the data
        $data = $d->fetch($sql);



        //fill the data
        foreach($data as $row) {
            $playerID = $row['PlayerID'];
            $playerObj = new player($playerID);
            $playerName = "<a href=\"http://www.shl-wpg.ca/playerDetails.php?playerid=" . $playerObj->getPlayerID() . "\">" . $playerObj->getFullName() . "</a>";

            $keyCollection = new collection();
            /*
            $this->c_seasonIDType = 1;
            $this->c_playerIDType = 2;
            */

            if($count == 0){
                $textValue = "<strong>Season <a href=\"http://www.shl-wpg.ca/seasonDetails.php?seasonid=" . $p_season . "\">" .$p_season . "</a></strong>";
                $this->addRecordHolder($textValue, $p_recordID, $keyCollection);
            }
            $playerIDArray = array();
            $playerIDArray[] = $this->c_playerIDType;
            $playerIDArray[] = $playerID;
            $keyCollection->add($playerIDArray);
            //$textValue = "<strong>Season <a href=\"http://www.shl-wpg.ca/seasonDetails.php?seasonid=" . $p_season . "\">" .$p_season . "</a></strong>: ". $playerName;
            
            
            $textValue = $playerName . " <small>(Season " .$p_season . ", " . $p_winText . ")</small>";


            $this->addRecordHolder($textValue, $p_recordID, $keyCollection);
            $count++;
        }
        $keyCollection = new collection();
        $this->addRecordHolder("<br />", $p_recordID, $keyCollection);
    }


     /*
     * NAME:    getPlayoffTeamWins
     * PARAMS:  $p_season = season, $p_teamNum = initial team color
     * DESC:    returns the win count for a team
     *
     */
    public function getPlayoffTeamWins($p_season, $p_teamNum){
        //variable declaration
        $sql = "";
        $winCount = 0;
        $ls_players =  "";
        
        
        //set up the sql
        $sql = "
            SELECT STP.PlayerID
            FROM Game AS SG
            INNER JOIN TeamPlayer AS STP ON SG.GameID = STP.GameID
            WHERE SG.SeasonID = " . db::fmt($p_season,1)  . "
            AND   SG.Playoff = 1
            AND   SG.GameNum = 1
            AND   STP.Color = " . db::fmt($p_teamNum,1);


        //database connection
        $d = new db(0);
        //fetch the data
        $data = $d->fetch($sql);
        
        //fill the data
        foreach($data as $row) {
            if(trim($ls_players) == ""){
                $ls_players = $row['PlayerID'];
            }else{
                $ls_players = $ls_players . ", " . $row['PlayerID'];
            }
        }

        
        //pull the wins
        $sql = "
            SELECT COUNT(ColorEnd.GameID) AS Wins
            FROM (
                SELECT G.GameID, TP.Color, COUNT(TP.Color) AS ColorCount
                FROM Game AS G
                INNER JOIN TeamPlayer AS TP ON G.GameID = TP.GameID
                WHERE G.SeasonID = " . db::fmt($p_season,1)  . "
                AND   G.Playoff = 1
                AND TP.PlayerID IN (" . $ls_players  . ")
                GROUP BY G.GameID, TP.Color
            ) AS ColorEnd
            INNER JOIN (
                SELECT GameID, MAX(ColorCount) AS MaxCount
                FROM (
                    SELECT G.GameID, TP.Color, COUNT(TP.Color) AS ColorCount
                    FROM Game AS G
                    INNER JOIN TeamPlayer AS TP ON G.GameID = TP.GameID
                    WHERE G.SeasonID = " . db::fmt($p_season,1)  . "
                    AND   G.Playoff = 1
                    AND TP.PlayerID IN (" . $ls_players . ")
                    GROUP BY G.GameID, TP.Color
                ) AS ColorData
                GROUP BY GameID
            ) AS ColorMatch ON ColorEnd.GameID = ColorMatch.GameID AND ColorEnd.ColorCount = ColorMatch.MaxCount
            INNER JOIN QuickGameWinners AS QGW ON ColorEnd.GameID = QGW.GameID AND ColorEnd.Color = QGW.Winner
        ";

        //database connection
        $d = new db(0);
        //fetch the data
        $data = $d->fetch($sql);
        //fill the data
        foreach($data as $row) {
            $winCount = $row['Wins'];
        }
        

        
        //return the win count
        return $winCount;
    }








        /*
     * NAME:    regularSeasonShutoutClub
     * PARAMS:  N/A
     * DESC:    creates the regular season shut out club
     *
     */
    public function regularSeasonShutoutClub(){
        //variable declaration
        $data = $this->getShutoutClub(0, 17);
    }



     /*
     * NAME:    getShutoutClub
     * PARAMS:  N/A
     * DESC:    creates the most goals in a game records
     *
     */
    public function getShutoutClub($p_playoff, $p_recordID){
        //variable declaration
        $sql = "";
        $textValue = "";
        $goalieName = "";


        $keyCollection = new collection();

        //database connection
        $d = new db(0);

        //set up the sql
        $sql = "  SELECT G.GameID, G.SeasonID, G.GameNum, GS.BlackPoints, GS.WhitePoints, BTP.PlayerID AS BlackGoalieID, WTP.PlayerID AS WhiteGoalieID
                FROM Game AS G
                INNER JOIN QuickGameScores AS GS ON G.GameID = GS.GameID
                INNER JOIN TeamPlayer AS BTP ON G.GameID = BTP.GameID
                INNER JOIN TeamPlayer AS WTP ON G.GameID = WTP.GameID
                WHERE BTP.Position = 1
                AND WTP.Position = 1
                AND WTP.Color = 2
                AND BTP.Color = 1 ";
        if($p_playoff >= 0){
            $sql = $sql . " AND G.Playoff = " . db::fmt($p_playoff,1);
        }

        $sql = $sql . " AND ( GS.BlackPoints = (
                    SELECT MIN(CASE WHEN BlackPoints < WhitePoints THEN BlackPoints ELSE WhitePoints END) AS LowestScore
                    FROM QuickGameScores
                    INNER JOIN Game ON  QuickGameScores.GameID = Game.GameID";
        if($p_playoff >= 0){
            $sql = $sql . " WHERE Game.Playoff = " . db::fmt($p_playoff,1);
        }

        $sql = $sql . " )
            OR GS.WhitePoints = (
                    SELECT MIN(CASE WHEN BlackPoints < WhitePoints THEN BlackPoints ELSE WhitePoints END) AS LowestScore
                    FROM QuickGameScores
                    INNER JOIN Game ON  QuickGameScores.GameID = Game.GameID";
        if($p_playoff >= 0){
            $sql = $sql . " WHERE Game.Playoff = " . db::fmt($p_playoff,1);
        }
        $sql = $sql . " ))";










        //fetch the data
        $data = $d->fetch($sql);



        //fill the data
        foreach($data as $row) {

            $blackPlayer = new player($row['BlackGoalieID']);
            $whitePlayer = new player($row['WhiteGoalieID']);




            if($row['BlackPoints'] > $row['WhitePoints']){
                $goalieName = "<a href=\"http://www.shl-wpg.ca/playerDetails.php?playerid=" . $blackPlayer->getPlayerID() . "\">" . $blackPlayer->getFullName() . "</a>";

                $keyCollection = new collection();
                /*
                $this->c_seasonIDType = 1;
                $this->c_playerIDType = 2;
                */

                $playerIDArray = array();
                $playerIDArray[] = $this->c_playerIDType;
                $playerIDArray[] = $row['BlackGoalieID'];
                $keyCollection->add($playerIDArray);
            }else{
                $goalieName = "<a href=\"http://www.shl-wpg.ca/playerDetails.php?playerid=" . $whitePlayer->getPlayerID() . "\">" .$whitePlayer->getFullName() . "</a>";

                $keyCollection = new collection();
                /*
                $this->c_seasonIDType = 1;
                $this->c_playerIDType = 2;
                */
                $playerIDArray = array();
                $playerIDArray[] = $this->c_playerIDType;
                $playerIDArray[] = $row['WhiteGoalieID'];
                $keyCollection->add($playerIDArray);
            }
          
            $textValue = "<strong>Season <a href=\"http://www.shl-wpg.ca/seasonDetails.php?seasonid=" . $row['SeasonID'] . "\">" . $row['SeasonID'] . "</a></strong>: " . $goalieName . " (game <a href=\"http://www.shl-wpg.ca/gameDetails.php?gameid=" . $row['GameID'] . "\">". $row['GameNum'] . "</a>)";
            $this->addRecordHolder($textValue, $p_recordID, $keyCollection);
        }
    }










        /*
     * NAME:    regularSeasonFiveHundredAssistClub
     * PARAMS:  N/A
     * DESC:    creates the regular season most points in a games
     *
     */
    public function regularSeasonFiveHundredAssistClub(){
        //variable declaration
        $data = $this->getPointClubRecord(2, 500, 16);
    }


     /*
     * NAME:    regularSeasonTwoFiftyAssistClub
     * PARAMS:  N/A
     * DESC:    creates the regular season most points in a games
     *
     */
    public function regularSeasonTwoFiftyAssistClub(){
        //variable declaration
        $data = $this->getPointClubRecord(2, 250, 15);
    }

        /*
     * NAME:    regularSeasonFiveHundredGoalClub
     * PARAMS:  N/A
     * DESC:    creates the regular season most points in a games
     *
     */
    public function regularSeasonFiveHundredGoalClub(){
        //variable declaration
        $data = $this->getPointClubRecord(1, 500, 14);
    }


     /*
     * NAME:    regularSeasonTwoFiftyGoalClub
     * PARAMS:  N/A
     * DESC:    creates the regular season most points in a games
     *
     */
    public function regularSeasonTwoFiftyGoalClub(){
        //variable declaration
        $data = $this->getPointClubRecord(1, 250, 13);
    }

     /*
     * NAME:    regularSeasonThousandPointClub
     * PARAMS:  N/A
     * DESC:    creates the regular season most points in a games
     *
     */
    public function regularSeasonThousandPointClub(){
        //variable declaration
        $data = $this->getPointClubRecord(0, 1000, 12);
    }
    
        /*
     * NAME:    regularSeasonFiveHundredPointClub
     * PARAMS:  N/A
     * DESC:    creates the regular season most points in a games
     *
     */
    public function regularSeasonFiveHundredPointClub(){
        //variable declaration
        $data = $this->getPointClubRecord(0, 500, 11);
    }
    
    
    
    
    
    

    
    
    
    
    
    
    
    
    
     /*
     * NAME:    getPointClubRecord
     * PARAMS:  N/A
     * DESC:    creates the most points by a player in a season records
     *
     */
    public function getPointClubRecord($p_pointType, $p_pointCount, $p_recordID){
        //variable declaration
        $sql = "";
        $textValue = "";
        $pointTypeText = "";
        $player;
        $points = 0;
        $gameCount = 0;
        $keyCollection = new collection();
        
        
        
        $testgameCount = 0;
        $testMaxID = 0;
        
        

        
        
        

        //database connection
        $d = new db(0);
        
             $sqlString = "
            SELECT PlayerID, FName, LName, Assists, Goals
            FROM (
              SELECT P.PlayerID, P.FName, P.LName, SUM(Assists) AS Assists, SUM(Goals) AS Goals
              FROM QuickPlayerDataTable AS QPDT
              INNER JOIN Player AS P ON QPDT.PlayerID = P.PlayerID
              WHERE Playoff = 0
              GROUP BY P.PlayerID, P.FName, P.LName
            ) AS PlayerPoints ";
         if($p_pointType == 1){
             $sqlString = $sqlString . " WHERE Goals >= " . $p_pointCount;
         }elseif($p_pointType == 2){
             $sqlString = $sqlString . " WHERE Assists >= " . $p_pointCount;
         }else{
             $sqlString = $sqlString . " WHERE (Goals + Assists) >= " . $p_pointCount;
         }
         
        
        
        //fetch the data
        $data = $d->fetch($sqlString);

        //echo $d->log;

        //fill the data
        foreach($data as $playerRow) {


//            //database connection
//            $d = new db(0);
//
//            //set up the sql
//            $sql = "SELECT COUNT(GameID) AS GameCount
//                    FROM (SELECT DISTINCT GameID
//                    FROM (SELECT G.GameID
//                    FROM TeamPlayer AS TP
//                    INNER JOIN Game AS G ON TP.GameID = G.GameID
//                    INNER JOIN Point AS P ON TP.TeamPlayerID = P.TeamPlayerID
//                    WHERE TP.PlayerID = " . $currentPlayer->getPlayerID();
//                if($p_pointType > 0){
//                    $sql = $sql . " AND P.PointType = " . $p_pointType;
//                }
//            $sql = $sql .  " AND G.Playoff = 0 ORDER BY G.SeasonID ASC, G.Playoff ASC, G.GameNum ASC
//                    LIMIT " . $p_pointCount . "
//                    ) AS PointClub) As CountClub ";
//
//
//
//            //fetch the data
//            $data = $d->fetch($sql);
//            //echo $d->log;
//            foreach($data as $row) {
//                $gameCount = $row['GameCount'];
//            }

            
            
            
            //database connection
            $d = new db(0);
            /* TEST!!!!!!!!!!!!!!!!!!!!!!!!! */ 
            //set up the sql
            $sql = "SELECT MAX(GameID) AS MaxGameID
                    FROM (SELECT G.GameID
                    FROM TeamPlayer AS TP
                    INNER JOIN Game AS G ON TP.GameID = G.GameID
                    INNER JOIN Point AS P ON TP.TeamPlayerID = P.TeamPlayerID
                    WHERE TP.PlayerID = " . $playerRow['PlayerID'];
                if($p_pointType > 0){
                    $sql = $sql . " AND P.PointType = " . $p_pointType;
                }
            $sql = $sql .  " AND TP.Position = 2 AND G.Playoff = 0 ORDER BY G.SeasonID ASC, G.Playoff ASC, G.GameNum ASC
                    LIMIT " . $p_pointCount . "
                    ) AS PointClub";



            //fetch the data
            $data = $d->fetch($sql);
  
            
            //echo $d->log;
            foreach($data as $row) {
                $testMaxID = $row['MaxGameID'];
   
            }
            
            //database connection
            $d = new db(0);
//            //set up the sql
//            $sql = "SELECT COUNT(GameID) AS GameCount
//                    FROM (SELECT DISTINCT GameID
//                    FROM (SELECT G.GameID
//                    FROM TeamPlayer AS TP
//                    INNER JOIN Game AS G ON TP.GameID = G.GameID
//                    WHERE TP.PlayerID = " . $currentPlayer->getPlayerID() .
//                    "   AND G.GameID <= " . $testMaxID .
//                    " AND G.Playoff = 0 ORDER BY G.SeasonID ASC, G.Playoff ASC, G.GameNum ASC) AS PointClub) As CountClub ";


            $sql = "SELECT COUNT(G.GameID) AS GameCount
                    FROM TeamPlayer AS TP
                    INNER JOIN Game AS G ON TP.GameID = G.GameID
                    WHERE TP.PlayerID = " . $playerRow['PlayerID'] .
                    " AND TP.Position = 2  AND G.GameID <= " . $testMaxID .
                    " AND G.Playoff = 0";

            //fetch the data
            $data = $d->fetch($sql);
            
  
            
            //echo $d->log;
            foreach($data as $row) {
                $testgameCount = $row['GameCount'];
            }
   
                $textValue = "<a href=\"http://www.shl-wpg.ca/playerDetails.php?playerid=" . $playerRow['PlayerID'] . "\">" . $playerRow['FName'] . " " . $playerRow['LName'] . "</a>: ";
                if($p_pointType == 0){
                    //point
                    $textValue = $textValue . $p_pointCount . " Points in " . $testgameCount . " games";
                }elseif($p_pointType == 1){
                    //goal
                    $textValue = $textValue . $p_pointCount . " Goals in " . $testgameCount . " games";
                }else{
                    //assist
                    $textValue = $textValue . $p_pointCount . " Assists in " . $testgameCount . " games";
                }

                
                $keyCollection = new collection();
                /*
                $this->c_seasonIDType = 1;
                $this->c_playerIDType = 2;
                */
                $playerIDArray = array();
                $playerIDArray[] = $this->c_playerIDType;
                $playerIDArray[] = $playerRow['PlayerID'];
                $keyCollection->add($playerIDArray);


                $this->addRecordHolder($textValue, $p_recordID, $keyCollection);      
            
            
            
            
          

        }
                    

    }       
    
    
    
    
    
    
    
    
    
//    
//     /*
//     * NAME:    getPointClubRecord
//     * PARAMS:  N/A
//     * DESC:    creates the most points by a player in a season records
//     *
//     */
//    public function getPointClubRecord($p_pointType, $p_pointCount, $p_recordID){
//        //variable declaration
//        $sql = "";
//        $textValue = "";
//        $pointTypeText = "";
//        $player;
//        $points = 0;
//        $gameCount = 0;
//        $keyCollection = new collection();
//        
//        
//        
//        $testgameCount = 0;
//        $testMaxID = 0;
//        
//        
//        //$p_pointType = point type, 0=point,1=goal,2=assist
//        $playerCol = $this->getPointClub($p_pointType, $p_pointCount);
//        
//        for($alpha = 0; $alpha < $playerCol->count(); $alpha += 1){
//            $currentPlayer = $playerCol->get($alpha);
//
//
////            //database connection
////            $d = new db(0);
////
////            //set up the sql
////            $sql = "SELECT COUNT(GameID) AS GameCount
////                    FROM (SELECT DISTINCT GameID
////                    FROM (SELECT G.GameID
////                    FROM TeamPlayer AS TP
////                    INNER JOIN Game AS G ON TP.GameID = G.GameID
////                    INNER JOIN Point AS P ON TP.TeamPlayerID = P.TeamPlayerID
////                    WHERE TP.PlayerID = " . $currentPlayer->getPlayerID();
////                if($p_pointType > 0){
////                    $sql = $sql . " AND P.PointType = " . $p_pointType;
////                }
////            $sql = $sql .  " AND G.Playoff = 0 ORDER BY G.SeasonID ASC, G.Playoff ASC, G.GameNum ASC
////                    LIMIT " . $p_pointCount . "
////                    ) AS PointClub) As CountClub ";
////
////
////
////            //fetch the data
////            $data = $d->fetch($sql);
////            //echo $d->log;
////            foreach($data as $row) {
////                $gameCount = $row['GameCount'];
////            }
//
//            
//            
//            
//            //database connection
//            $d = new db(0);
//            /* TEST!!!!!!!!!!!!!!!!!!!!!!!!! */ 
//            //set up the sql
//            $sql = "SELECT MAX(GameID) AS MaxGameID
//                    FROM (SELECT G.GameID
//                    FROM TeamPlayer AS TP
//                    INNER JOIN Game AS G ON TP.GameID = G.GameID
//                    INNER JOIN Point AS P ON TP.TeamPlayerID = P.TeamPlayerID
//                    WHERE TP.PlayerID = " . $currentPlayer->getPlayerID();
//                if($p_pointType > 0){
//                    $sql = $sql . " AND P.PointType = " . $p_pointType;
//                }
//            $sql = $sql .  " AND G.Playoff = 0 ORDER BY G.SeasonID ASC, G.Playoff ASC, G.GameNum ASC
//                    LIMIT " . $p_pointCount . "
//                    ) AS PointClub";
//
//
//
//            //fetch the data
//            $data = $d->fetch($sql);
//            //echo $d->log;
//            foreach($data as $row) {
//                $testMaxID = $row['MaxGameID'];
//   
//            }
//            
//            //database connection
//            $d = new db(0);
////            //set up the sql
////            $sql = "SELECT COUNT(GameID) AS GameCount
////                    FROM (SELECT DISTINCT GameID
////                    FROM (SELECT G.GameID
////                    FROM TeamPlayer AS TP
////                    INNER JOIN Game AS G ON TP.GameID = G.GameID
////                    WHERE TP.PlayerID = " . $currentPlayer->getPlayerID() .
////                    "   AND G.GameID <= " . $testMaxID .
////                    " AND G.Playoff = 0 ORDER BY G.SeasonID ASC, G.Playoff ASC, G.GameNum ASC) AS PointClub) As CountClub ";
//
//
//            $sql = "SELECT COUNT(G.GameID) AS GameCount
//                    FROM TeamPlayer AS TP
//                    INNER JOIN Game AS G ON TP.GameID = G.GameID
//                    WHERE TP.PlayerID = " . $currentPlayer->getPlayerID() .
//                    "   AND G.GameID <= " . $testMaxID .
//                    " AND G.Playoff = 0";
//
//            //fetch the data
//            $data = $d->fetch($sql);
//            //echo $d->log;
//            foreach($data as $row) {
//                $testgameCount = $row['GameCount'];
//            }
//   
//                $textValue = "<a href=\"http://www.shl-wpg.ca/playerDetails.php?playerid=" . $currentPlayer->getPlayerID() . "\">" . $currentPlayer->getFullName() . "</a>: ";
//                if($p_pointType == 0){
//                    //point
//                    $textValue = $textValue . $p_pointCount . " Points in " . $testgameCount . " games";
//                }elseif($p_pointType == 1){
//                    //goal
//                    $textValue = $textValue . $p_pointCount . " Goals in " . $testgameCount . " games";
//                }else{
//                    //assist
//                    $textValue = $textValue . $p_pointCount . " Assists in " . $testgameCount . " games";
//                }
//
//                
//                $keyCollection = new collection();
//                /*
//                $this->c_seasonIDType = 1;
//                $this->c_playerIDType = 2;
//                */
//                $playerIDArray = array();
//                $playerIDArray[] = $this->c_playerIDType;
//                $playerIDArray[] = $currentPlayer->getPlayerID();
//                $keyCollection->add($playerIDArray);
//
//
//                $this->addRecordHolder($textValue, $p_recordID, $keyCollection);      
//            
//            
//            
//            
//            
////            //database connection
////            $d = new db(0);
////
////            //set up the sql
////            $sql = "  SELECT PlayerID, SUM(CASE WHEN PointType = 1 THEN 1 ELSE 0 END) AS Goals, SUM(CASE WHEN PointType = 2 THEN 1 ELSE 0 END) AS Assists
////                        
////                    FROM (SELECT TP.PlayerID, G.GameID, P.PointType
////                    FROM TeamPlayer AS TP
////                    INNER JOIN Game AS G ON TP.GameID = G.GameID
////                    INNER JOIN Point AS P ON TP.TeamPlayerID = P.TeamPlayerID
////                    WHERE TP.PlayerID = " . $currentPlayer->getPlayerID();
////                    if($p_pointType > 0){
////                        $sql = $sql . " AND P.PointType = " . $p_pointType;
////                    }
//// 
////                    $sql = $sql .  " AND G.Playoff = 0 ORDER BY G.SeasonID ASC, G.Playoff ASC, G.GameNum ASC
////                    LIMIT " . $p_pointCount . " 
////                    ) AS PointClub
////                    GROUP BY PlayerID ";
////
////
////
////            $textValue = "<a href=\"http://www.shl-wpg.ca/playerDetails.php?playerid=" . $currentPlayer->getPlayerID() . "\">" . $currentPlayer->getFullName() . "</a>: ";
////
////
////            //fetch the data
////            $data = $d->fetch($sql);
////            
////            
////            
////            
////            
////
////            //echo $d->log;
////
////            //fill the data
////            foreach($data as $row) {
////                
////                if($p_pointType == 0){
////                    //point
////                    $textValue = $textValue . ($row['Goals'] + $row['Assists'] ) . " Points in " . $testgameCount . " games (" . $row['Goals'] . " goals and " . $row['Assists'] . " assists)";
////                }elseif($p_pointType == 1){
////                    //goal
////                    $textValue = $textValue . $row['Goals'] . " Goals in " . $testgameCount . " games";
////                }else{
////                    //assist
////                    $textValue = $textValue . $row['Assists'] . " Assists in " . $testgameCount . " games";
////                }
////
////                
////                $keyCollection = new collection();
////                /*
////                $this->c_seasonIDType = 1;
////                $this->c_playerIDType = 2;
////                */
////                $playerIDArray = array();
////                $playerIDArray[] = $this->c_playerIDType;
////                $playerIDArray[] = $currentPlayer->getPlayerID();
////                $keyCollection->add($playerIDArray);
////
////
////                $this->addRecordHolder($textValue, $p_recordID, $keyCollection);
////            } 
//
//        }
//                    
//
//    }       
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
 
        /*
     * NAME:    regularSeasonMostPointsByPlayerInSeason
     * PARAMS:  N/A
     * DESC:    creates the regular season most points in a games
     *
     */
    public function regularSeasonMostPointsByPlayerInSeason(){
        //variable declaration
        $data = $this->getMostPointsByPlayerInSeason(0, 10);
    }
    
    
    
     /*
     * NAME:    getMostPointsByPlayerInSeason
     * PARAMS:  N/A
     * DESC:    creates the most points by a player in a season records
     *
     */
    public function getMostPointsByPlayerInSeason($p_playoff, $p_recordID){
        //variable declaration
        $sql = "";
        $textValue = "";
        $player;
        $points = 0;
        $keyCollection = new collection();
        
        //database connection
        $d = new db(0);

        //set up the sql
        $sql = "  SELECT PlayerID, SeasonID, Assists, Goals
                  FROM QuickPlayerGoalsAssists
                  WHERE (Assists + Goals) = (
                    SELECT MAX(Assists + Goals)
                    FROM QuickPlayerGoalsAssists ";
        if($p_playoff >= 0){
            $sql = $sql . " WHERE Playoff = " . db::fmt($p_playoff,1);
        }
        
        $sql = $sql . " ) ";
        if($p_playoff >= 0){
            $sql = $sql . " AND Playoff = " . db::fmt($p_playoff,1);
        }
        
 
        
        
        
        //fetch the data
        $data = $d->fetch($sql);


        
        //fill the data
        foreach($data as $row) {
            
            $player = new player($row['PlayerID']);
            $points = $row['Assists'] + $row['Goals'];

            $keyCollection = new collection();
            /*
            $this->c_seasonIDType = 1;
            $this->c_playerIDType = 2;
            */
            $playerIDArray = array();
            $playerIDArray[] = $this->c_playerIDType;
            $playerIDArray[] = $row['PlayerID'];
            $keyCollection->add($playerIDArray);


            $textValue = "<strong>Season <a href=\"http://www.shl-wpg.ca/seasonDetails.php?seasonid=" . $row['SeasonID'] . "\">" . $row['SeasonID'] . "</a></strong>: <a href=\"http://www.shl-wpg.ca/playerDetails.php?playerid=" . $row['PlayerID'] . "\">" . $player->getFullName() . "</a>  " . $points . " Points.";
            $this->addRecordHolder($textValue, $p_recordID, $keyCollection);
        } 
    }       
    
    
        /*
     * NAME:    regularSeasonMostAssistsByPlayerInSeason
     * PARAMS:  N/A
     * DESC:    creates the regular season most assists in a games
     *
     */
    public function regularSeasonMostAssistsByPlayerInSeason(){
        //variable declaration
        $data = $this->getMostAssistsByPlayerInSeason(0, 9);
    }
    
    
    
     /*
     * NAME:    getMostAssistsByPlayerInSeason
     * PARAMS:  N/A
     * DESC:    creates the most assists by a player in a season records
     *
     */
    public function getMostAssistsByPlayerInSeason($p_playoff, $p_recordID){
        //variable declaration
        $sql = "";
        $textValue = "";
        $player;
        $keyCollection = new collection();
        
        //database connection
        $d = new db(0);

        //set up the sql
        $sql = "  SELECT PlayerID, SeasonID, Assists
                  FROM QuickPlayerGoalsAssists
                  WHERE Assists = (
                    SELECT MAX(Assists)
                    FROM QuickPlayerGoalsAssists ";
        if($p_playoff >= 0){
            $sql = $sql . " WHERE Playoff = " . db::fmt($p_playoff,1);
        }
        
        $sql = $sql . " ) ";
        if($p_playoff >= 0){
            $sql = $sql . " AND Playoff = " . db::fmt($p_playoff,1);
        }
        
 
        
        
        
        //fetch the data
        $data = $d->fetch($sql);

        
        //fill the data
        foreach($data as $row) {
            
            $player = new player($row['PlayerID']);
            $keyCollection = new collection();
            /*
            $this->c_seasonIDType = 1;
            $this->c_playerIDType = 2;
            */
            $playerIDArray = array();
            $playerIDArray[] = $this->c_playerIDType;
            $playerIDArray[] = $row['PlayerID'];
            $keyCollection->add($playerIDArray);
            $textValue = "<strong>Season <a href=\"http://www.shl-wpg.ca/seasonDetails.php?seasonid=" . $row['SeasonID'] . "\">" . $row['SeasonID'] . "</a></strong>: <a href=\"http://www.shl-wpg.ca/playerDetails.php?playerid=" . $row['PlayerID'] . "\">" . $player->getFullName() . "</a>  ". $row['Assists'] . " Assists.";
            $this->addRecordHolder($textValue, $p_recordID, $keyCollection);
        } 
    }      
    
    
    
    
        /*
     * NAME:    regularSeasonMostGoalsByPlayerInSeason
     * PARAMS:  N/A
     * DESC:    creates the regular season most goals in a games
     *
     */
    public function regularSeasonMostGoalsByPlayerInSeason(){
        //variable declaration
        $data = $this->getMostGoalsByPlayerInSeason(0, 8);
    }
    
    
    
     /*
     * NAME:    getMostGoalsByPlayerInSeason
     * PARAMS:  N/A
     * DESC:    creates the most goals by a player in a season records
     *
     */
    public function getMostGoalsByPlayerInSeason($p_playoff, $p_recordID){
        //variable declaration
        $sql = "";
        $textValue = "";
        $player;
        $keyCollection = new collection();

        
        //database connection
        $d = new db(0);

        //set up the sql
        $sql = "  SELECT PlayerID, SeasonID, Goals
                  FROM QuickPlayerGoalsAssists
                  WHERE Goals = (
                    SELECT MAX(Goals)
                    FROM QuickPlayerGoalsAssists ";
        if($p_playoff >= 0){
            $sql = $sql . " WHERE Playoff = " . db::fmt($p_playoff,1);
        }
        
        $sql = $sql . " ) ";
        if($p_playoff >= 0){
            $sql = $sql . " AND Playoff = " . db::fmt($p_playoff,1);
        }
        
 
        
        
        
        //fetch the data
        $data = $d->fetch($sql);

        
        //fill the data
        foreach($data as $row) {
            
            $player = new player($row['PlayerID']);
            $keyCollection = new collection();
            /*
            $this->c_seasonIDType = 1;
            $this->c_playerIDType = 2;
            */
            $playerIDArray = array();
            $playerIDArray[] = $this->c_playerIDType;
            $playerIDArray[] = $row['PlayerID'];
            $keyCollection->add($playerIDArray);
            $textValue = "<strong>Season <a href=\"http://www.shl-wpg.ca/seasonDetails.php?seasonid=" . $row['SeasonID'] . "\">" . $row['SeasonID'] . "</a></strong>: <a href=\"http://www.shl-wpg.ca/playerDetails.php?playerid=" . $row['PlayerID'] . "\">" . $player->getFullName() . "</a>  ". $row['Goals'] . " Goals.";
            $this->addRecordHolder($textValue, $p_recordID, $keyCollection);
        } 
    }  
     
    
        
    
    
    
    
    
    
    
    
    
    
    
        /*
     * NAME:    regularSeasonMostGoals
     * PARAMS:  N/A
     * DESC:    creates the regular season most goals in a games
     *
     */
    public function regularSeasonLeastGoals(){
        //variable declaration
        $data = $this->getLeastGoals(0, 7);
    }
    
    
    
     /*
     * NAME:    getMostGoals
     * PARAMS:  N/A
     * DESC:    creates the most goals in a game records
     *
     */
    public function getLeastGoals($p_playoff, $p_recordID){
        //variable declaration
        $sql = "";
        $textValue = "";
        $blackPlayer;
        $whitePlayer;
        $finalScore;
        $goalieText;
        $scoreText;
        $keyCollection = new collection();
        
        //database connection
        $d = new db(0);

        //set up the sql
        $sql = "  SELECT G.GameID, G.SeasonID, G.GameNum, GS.BlackPoints, GS.WhitePoints, BTP.PlayerID AS BlackGoalieID, WTP.PlayerID AS WhiteGoalieID
                FROM Game AS G
                INNER JOIN QuickGameScores AS GS ON G.GameID = GS.GameID
                INNER JOIN TeamPlayer AS BTP ON G.GameID = BTP.GameID
                INNER JOIN TeamPlayer AS WTP ON G.GameID = WTP.GameID
                WHERE BTP.Position = 1
                AND WTP.Position = 1 
                AND WTP.Color = 2
                AND BTP.Color = 1 ";
        if($p_playoff >= 0){
            $sql = $sql . " AND G.Playoff = " . db::fmt($p_playoff,1);
        }
        
        $sql = $sql . " AND ( GS.BlackPoints = (
                    SELECT MIN(CASE WHEN BlackPoints < WhitePoints THEN BlackPoints ELSE WhitePoints END) AS LowestScore
                    FROM QuickGameScores
                    INNER JOIN Game ON  QuickGameScores.GameID = Game.GameID";
        if($p_playoff >= 0){
            $sql = $sql . " WHERE Game.Playoff = " . db::fmt($p_playoff,1);
        }
        
        $sql = $sql . " )
            OR GS.WhitePoints = (
                    SELECT MIN(CASE WHEN BlackPoints < WhitePoints THEN BlackPoints ELSE WhitePoints END) AS LowestScore
                    FROM QuickGameScores
                    INNER JOIN Game ON  QuickGameScores.GameID = Game.GameID";
        if($p_playoff >= 0){
            $sql = $sql . " WHERE Game.Playoff = " . db::fmt($p_playoff,1);
        }
        $sql = $sql . " ))";
        
        

        
        
        
        
        
        
        
        //fetch the data
        $data = $d->fetch($sql);

        
        
        //fill the data
        foreach($data as $row) {
            
            $blackPlayer = new player($row['BlackGoalieID']);
            $whitePlayer = new player($row['WhiteGoalieID']);




            if($row['BlackPoints'] > $row['WhitePoints']){
                $finalScore = $row['BlackPoints'] . "-" . $row['WhitePoints'] . " for Colored Team.";
                $scoreText = $row['WhitePoints'];

                $keyCollection = new collection();
                /*
                $this->c_seasonIDType = 1;
                $this->c_playerIDType = 2;
                */

//                $playerIDArray = array();
//                $playerIDArray[] = $this->c_playerIDType;
//                $playerIDArray[] = $row['BlackGoalieID'];
//                $keyCollection->add($playerIDArray);
            }else{
                $finalScore = $row['WhitePoints'] . "-" . $row['BlackPoints'] . " for White Team.";
                $scoreText = $row['BlackPoints'];

                $keyCollection = new collection();
                /*
                $this->c_seasonIDType = 1;
                $this->c_playerIDType = 2;
                */
//                $playerIDArray = array();
//                $playerIDArray[] = $this->c_playerIDType;
//                $playerIDArray[] = $row['WhiteGoalieID'];
//                $keyCollection->add($playerIDArray);
            }
            $goalieText = "Goalies: (<a href=\"http://www.shl-wpg.ca/playerDetails.php?position=1&playerid=" . $row['WhiteGoalieID'] . "\">" . $whitePlayer->getFullName() . "</a> white vs. <a href=\"http://www.shl-wpg.ca/playerDetails.php?position=1&playerid=" . $row['BlackGoalieID'] . "\">" . $blackPlayer->getFullName() . "</a> colored)";
            
            $textValue = "<strong>Season <a href=\"http://www.shl-wpg.ca/seasonDetails.php?seasonid=" . $row['SeasonID'] . "\">" . $row['SeasonID'] . "</a></strong>: Game <a href=\"http://www.shl-wpg.ca/gameDetails.php?gameid=" . $row['GameID'] . "\">". $row['GameNum'] . "</a> - " . $scoreText . " Goals. " . $finalScore . "<br />" . $goalieText . "<br />";
            $this->addRecordHolder($textValue, $p_recordID, $keyCollection);
        } 
    }  
     
    
    
    
    
    
    
    
    
    
    
     /*
     * NAME:    regularSeasonMostGoals
     * PARAMS:  N/A
     * DESC:    creates the regular season most goals in a games
     *
     */
    public function regularSeasonMostGoals(){
        //variable declaration
        $data = $this->getMostGoals(0, 5);
    }
    
    
    
     /*
     * NAME:    getMostGoals
     * PARAMS:  N/A
     * DESC:    creates the most goals in a game records
     *
     */
    public function getMostGoals($p_playoff, $p_recordID){
        //variable declaration
        $sql = "";
        $textValue = "";
        $blackPlayer;
        $whitePlayer;
        $finalScore;
        $goalieText;
        $keyCollection = new collection();

        //database connection
        $d = new db(0);

        //set up the sql
        $sql = "  SELECT G.GameID, G.SeasonID, TS.Score, G.GameNum, GS.BlackPoints, GS.WhitePoints, BTP.PlayerID AS BlackGoalieID, WTP.PlayerID AS WhiteGoalieID
                FROM QuickTeamScores AS TS
                INNER JOIN Game AS G ON TS.GameID = G.GameID
                INNER JOIN QuickGameScores AS GS ON G.GameID = GS.GameID
                INNER JOIN TeamPlayer AS BTP ON G.GameID = BTP.GameID
                INNER JOIN TeamPlayer AS WTP ON G.GameID = WTP.GameID
                WHERE BTP.Position = 1
                AND WTP.Position = 1 
                AND WTP.Color = 2
                AND BTP.Color = 1 ";
        if($p_playoff >= 0){
            $sql = $sql . " AND G.Playoff = " . db::fmt($p_playoff,1);
        }
        
        $sql = $sql . " AND TS.Score = (
                    SELECT MAX(Score)
                    FROM QuickTeamScores
                    INNER JOIN Game ON  QuickTeamScores.GameID = Game.GameID";
        if($p_playoff >= 0){
            $sql = $sql . " WHERE Game.Playoff = " . db::fmt($p_playoff,1);
        }
        
        $sql = $sql . " )";
        
        if($p_playoff >= 0){
            $sql = $sql . " AND G.Playoff = " . db::fmt($p_playoff,1);
        }
        
        //fetch the data
        $data = $d->fetch($sql);

        
        
        //fill the data
        foreach($data as $row) {
            
            $blackPlayer = new player($row['BlackGoalieID']);
            $whitePlayer = new player($row['WhiteGoalieID']);
            
            if($row['BlackPoints'] > $row['WhitePoints']){
                $finalScore = $row['BlackPoints'] . "-" . $row['WhitePoints'] . " for Colored Team.";
            }else{
                $finalScore = $row['WhitePoints'] . "-" . $row['BlackPoints'] . " for White Team.";
            }
            $goalieText = "Goalies: (<a href=\"http://www.shl-wpg.ca/playerDetails.php?position=1&playerid=" . $row['WhiteGoalieID'] . "\">" . $whitePlayer->getFullName() . "</a> white vs. <a href=\"http://www.shl-wpg.ca/playerDetails.php?position=1&playerid=" . $row['BlackGoalieID'] . "\">" . $blackPlayer->getFullName() . "</a> colored)";
            
            $textValue = "<strong>Season <a href=\"http://www.shl-wpg.ca/seasonDetails.php?seasonid=" . $row['SeasonID'] . "\">" . $row['SeasonID'] . "</a></strong>: Game <a href=\"http://www.shl-wpg.ca/gameDetails.php?gameid=" . $row['GameID'] . "\">". $row['GameNum'] . "</a> - " . $row['Score'] . " Goals. " . $finalScore . "<br />" . $goalieText . "<br />";
            $this->addRecordHolder($textValue, $p_recordID, $keyCollection);
        } 
    }  
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
     /*
     * NAME:    addRecordHolder
     * PARAMS:  N/A
     * DESC:    adds a record holder
     *
     */
    public function addRecordHolder($p_value, $p_recordID, $p_keys){
        //variable declaration
        $recordHolderID = 0;
        //database connection
        $d = new db(0);


        //insert the record
        $data = $d->exec("
            INSERT INTO  RecordHolder (Value, RecordID)
            VALUES (" . db::fmt($p_value,0) . ",
                    " . db::fmt($p_recordID,1) . ")");

        //set the id inserted
        $recordHolderID = $d->last_id;

        //loop over the keys for insert
        for($alpha = 0; $alpha < $p_keys->count(); $alpha += 1){
            $currentKey = $p_keys->get($alpha);
            //insert the record
            $data = $d->exec("
            INSERT INTO  RecordHolderID (RepresentativeID, IDTypeID, RecordHolderID)
                VALUES (" . db::fmt($currentKey[1],1) . ",
                        " . db::fmt($currentKey[0],1) . ",
                        " . db::fmt($recordHolderID,1) . ")");
        }
        
    }   
    
     /*
     * NAME:    teamRegularSeasonRecords
     * PARAMS:  N/A
     * DESC:    creates the team regular season records entries
     *
     */
    public function teamRegularSeasonRecords(){
        //variable declaration
        $data = $this->getTeamRecords(0, 1);
    }
    
     /*
     * NAME:    getTeamRecords
     * PARAMS:  N/A
     * DESC:    creates the team regular season records entries
     *
     */
    public function getTeamRecords($p_playoff, $p_recordID){
        //variable declaration
        $sql = "";
        $textValue = "";
        $keyCollection = new collection();
        //database connection
        $d = new db(0);

        //set up the sql
        $sql = " SELECT G.SeasonID, SUM(CASE Winner WHEN 2 THEN 1 ELSE 0 END) AS WhiteWins, SUM(CASE Winner WHEN 1 THEN 1 ELSE 0 END) AS ColorWins
            FROM QuickGameWinners AS GW
            INNER JOIN Game AS G ON GW.GameID = G.GameID
            ";
        
        if($p_playoff >= 0){
            $sql = $sql . " WHERE G.Playoff = " . db::fmt($p_playoff,1);
        }
        
        $sql = $sql . " GROUP BY G.SeasonID  ";
        
        //fetch the data
        $data = $d->fetch($sql);

        //fill the data
        foreach($data as $row) {
            $textValue = "<strong>Season <a href=\"http://www.shl-wpg.ca/seasonDetails.php?seasonid=" . $row['SeasonID'] . "\">" . $row['SeasonID'] . "</a></strong>: " . $row['WhiteWins'] . "-" . $row['ColorWins'];
            $this->addRecordHolder($textValue, $p_recordID, $keyCollection);
        } 
    }   
    
    
    
    
     /*
     * NAME:    regularSeasonOvertimeGames
     * PARAMS:  N/A
     * DESC:    creates the regular season overtime games
     *
     */
    public function regularSeasonOvertimeGames(){
        //variable declaration
        $data = $this->getOvertimeGames(0, 2);
    }
    
     /*
     * NAME:    getOvertimeGames
     * PARAMS:  N/A
     * DESC:    creates the overtime game records
     *
     */
    public function getOvertimeGames($p_playoff, $p_recordID){
        //variable declaration
        $sql = "";
        $textValue = "";
        $keyCollection = new collection();
        
        //database connection
        $d = new db(0);

        //set up the sql
        $sql = " SELECT G.SeasonID, COUNT(G.SeasonID) AS OvertimeGames 
                FROM QuickGameScores AS GS
                INNER JOIN Game AS G ON GS.GameID = G.GameID
                WHERE 1 = 1
            ";
        
        if($p_playoff >= 0){
            $sql = $sql . " AND G.Playoff = " . db::fmt($p_playoff,1);
        }
        
        $sql = $sql . " AND (
                            GS.WhitePoints > 10
                            OR GS.BlackPoints > 10
                        )
                        GROUP BY G.SeasonID ";
        
        //fetch the data
        $data = $d->fetch($sql);

        //fill the data
        foreach($data as $row) {
            $textValue = "<strong>Season <a href=\"http://www.shl-wpg.ca/seasonDetails.php?seasonid=" . $row['SeasonID'] . "\">" . $row['SeasonID'] . "</a></strong>: " . $row['OvertimeGames'] . " Overtime Games.";
            $this->addRecordHolder($textValue, $p_recordID, $keyCollection);
        } 
    }       

    
    /*
     * NAME:    playoffNoOTLongestGames
     * PARAMS:  N/A
     * DESC:    creates the playoff no overtime longest games
     *
     */
    public function playoffNoOTLongestGames(){
        //variable declaration
        $data = $this->getLongestGames(1, 51, false);
    }
    
    
    /*
     * NAME:    playoffLongestGames
     * PARAMS:  N/A
     * DESC:    creates the playoff  longest games
     *
     */
    public function playoffLongestGames(){
        //variable declaration
        $data = $this->getLongestGames(1, 50, true);
    }
    
     /*
     * NAME:    regularSeasonNoOvertimeLongestGames
     * PARAMS:  N/A
     * DESC:    creates the regular season no overtime longest games
     *
     */
    public function regularSeasonNoOvertimeLongestGames(){
        //variable declaration
        $data = $this->getLongestGames(0, 49, false);
    }
    
     /*
     * NAME:    regularSeasonLongestGames
     * PARAMS:  N/A
     * DESC:    creates the regular season longest games
     *
     */
    public function regularSeasonLongestGames(){
        //variable declaration
        $data = $this->getLongestGames(0, 3, true);
    }
    
    
    
     /*
     * NAME:    getLongestGames
     * PARAMS:  N/A
     * DESC:    creates the longest game records
     *
     */
    public function getLongestGames($p_playoff, $p_recordID, $p_overtime){
        //variable declaration
        $sql = "";
        $textValue = "";
        $blackPlayer;
        $whitePlayer;
        $finalScore;
        $goalieText;
        $keyCollection = new collection();
        //database connection
        $d = new db(0);

        //set up the sql
        $sql = " SELECT G.GameID, G.SeasonID, GTL.GameSeconds, G.GameNum, GS.BlackPoints, GS.WhitePoints, BTP.PlayerID AS BlackGoalieID, WTP.PlayerID AS WhiteGoalieID
                FROM QuickGameTimeLength AS GTL
                INNER JOIN Game AS G ON GTL.GameID = G.GameID
                INNER JOIN QuickGameScores AS GS ON G.GameID = GS.GameID
                INNER JOIN TeamPlayer AS BTP ON G.GameID = BTP.GameID
                INNER JOIN TeamPlayer AS WTP ON G.GameID = WTP.GameID
                WHERE BTP.Position = 1
                AND WTP.Position = 1 
                AND WTP.Color = 2
                AND BTP.Color = 1 ";
        if($p_playoff >= 0){
            $sql = $sql . " AND G.Playoff = " . db::fmt($p_playoff,1);
        }
        
        if(!$p_overtime){
            $sql = $sql . " AND GS.BlackPoints <= 10 AND GS.WhitePoints <= 10 ";
        }
        
        $sql = $sql . " AND GTL.GameSeconds = (
                    SELECT MAX(GameSeconds)
                    FROM QuickGameTimeLength
                    INNER JOIN Game ON  QuickGameTimeLength.GameID = Game.GameID
                    INNER JOIN QuickGameScores AS QGS ON Game.GameID = QGS.GameID
                    WHERE 1=1 ";
        if($p_playoff >= 0){
            $sql = $sql . " AND Game.Playoff = " . db::fmt($p_playoff,1);
        }
       if(!$p_overtime){
            $sql = $sql . " AND QGS.BlackPoints <= 10 AND QGS.WhitePoints <= 10 ";
        }
        $sql = $sql . " )";
        
        if($p_playoff >= 0){
            $sql = $sql . " AND G.Playoff = " . db::fmt($p_playoff,1);
        }
        
        //fetch the data
        $data = $d->fetch($sql);
        
  

        //fill the data
        foreach($data as $row) {
            
            $blackPlayer = new player($row['BlackGoalieID']);
            $whitePlayer = new player($row['WhiteGoalieID']);
            
            if($row['BlackPoints'] > $row['WhitePoints']){
                $finalScore = $row['BlackPoints'] . "-" . $row['WhitePoints'] . " for Colored Team.";
            }else{
                $finalScore = $row['WhitePoints'] . "-" . $row['BlackPoints'] . " for White Team.";
            }
            $goalieText = "Goalies: (<a href=\"http://www.shl-wpg.ca/playerDetails.php?position=1&playerid=" . $row['WhiteGoalieID'] . "\">" . $whitePlayer->getFullName() . "</a> white vs. <a href=\"http://www.shl-wpg.ca/playerDetails.php?position=1&playerid=" . $row['BlackGoalieID'] . "\">" . $blackPlayer->getFullName() . "</a> colored)";
            
            $textValue = "<strong>Season <a href=\"http://www.shl-wpg.ca/seasonDetails.php?seasonid=" . $row['SeasonID'] . "\">" . $row['SeasonID'] . "</a></strong>: Game <a href=\"http://www.shl-wpg.ca/gameDetails.php?gameid=" . $row['GameID'] . "\">". $row['GameNum'] . "</a> - " . number_format($row['GameSeconds']/60, 0, '', '') . " minutes. " . $finalScore . "<br />" . $goalieText . "<br />";
            $this->addRecordHolder($textValue, $p_recordID, $keyCollection);
        } 
    }         
    
    
    
    
    
     /*
     * NAME:    regularSeasonShortestGames
     * PARAMS:  N/A
     * DESC:    creates the regular season shortest games
     *
     */
    public function regularSeasonShortestGames(){
        //variable declaration
        $data = $this->getShortestGames(0, 4);
    }
    
    
    
     /*
     * NAME:    getShortestGames
     * PARAMS:  N/A
     * DESC:    creates the shortest game records
     *
     */
    public function getShortestGames($p_playoff, $p_recordID){
        //variable declaration
        $sql = "";
        $textValue = "";
        $blackPlayer;
        $whitePlayer;
        $finalScore;
        $goalieText;
        $keyCollection = new collection();
        //database connection
        $d = new db(0);

        //set up the sql
        $sql = " SELECT G.GameID, G.SeasonID, GTL.GameSeconds, G.GameNum, GS.BlackPoints, GS.WhitePoints, BTP.PlayerID AS BlackGoalieID, WTP.PlayerID AS WhiteGoalieID
                FROM QuickGameTimeLength AS GTL
                INNER JOIN Game AS G ON GTL.GameID = G.GameID
                INNER JOIN QuickGameScores AS GS ON G.GameID = GS.GameID
                INNER JOIN TeamPlayer AS BTP ON G.GameID = BTP.GameID
                INNER JOIN TeamPlayer AS WTP ON G.GameID = WTP.GameID
                WHERE BTP.Position = 1
                AND WTP.Position = 1 
                AND WTP.Color = 2
                AND BTP.Color = 1 ";
        if($p_playoff >= 0){
            $sql = $sql . " AND G.Playoff = " . db::fmt($p_playoff,1);
        }
        
        $sql = $sql . " AND GTL.GameSeconds = (
                    SELECT MIN(GameSeconds)
                    FROM QuickGameTimeLength
                    INNER JOIN Game ON  QuickGameTimeLength.GameID = Game.GameID";
        if($p_playoff >= 0){
            $sql = $sql . " WHERE Game.Playoff = " . db::fmt($p_playoff,1);
        }
        
        $sql = $sql . " )";
        
        if($p_playoff >= 0){
            $sql = $sql . " AND G.Playoff = " . db::fmt($p_playoff,1);
        }
        
        //fetch the data
        $data = $d->fetch($sql);
        
        //fill the data
        foreach($data as $row) {
            
            $blackPlayer = new player($row['BlackGoalieID']);
            $whitePlayer = new player($row['WhiteGoalieID']);
            
            if($row['BlackPoints'] > $row['WhitePoints']){
                $finalScore = $row['BlackPoints'] . "-" . $row['WhitePoints'] . " for Colored Team.";
            }else{
                $finalScore = $row['WhitePoints'] . "-" . $row['BlackPoints'] . " for White Team.";
            }
            $goalieText = "Goalies: (<a href=\"http://www.shl-wpg.ca/playerDetails.php?position=1&playerid=" . $row['WhiteGoalieID'] . "\">" . $whitePlayer->getFullName() . "</a> white vs. <a href=\"http://www.shl-wpg.ca/playerDetails.php?position=1&playerid=" . $row['BlackGoalieID'] . "\">" . $blackPlayer->getFullName() . "</a> colored)";
            
            $textValue = "<strong>Season <a href=\"http://www.shl-wpg.ca/seasonDetails.php?seasonid=" . $row['SeasonID'] . "\">" . $row['SeasonID'] . "</a></strong>: Game <a href=\"http://www.shl-wpg.ca/gameDetails.php?gameid=" . $row['GameID'] . "\">". $row['GameNum'] . "</a> - " . number_format($row['GameSeconds']/60, 0, '', '') . " minutes. " . $finalScore . "<br />" . $goalieText . "<br />";
            $this->addRecordHolder($textValue, $p_recordID, $keyCollection);
        } 
    }  
    
    
    
    
    
    
    
    
  /*******************************************************
  * 
  * 
  *     RECORD CREATION END
  * 
  * 
  **********************************************************/   
    
    
    
    
    
    
    
    
    
    
    

      /*
     * NAME:    getHighestGoalGames
     * PARAMS:  N/A
     * DESC:    gets the collection of games with the highest goal count
     *
     */
    public function getHighestGoalGames(){
        //variable declaration
        $col = new collection();
        $maxAmt = 0;
        //database connection
        $d = new db(0);

        //fetch the data
        $data = $d->fetch("
            SELECT tp.GameID, tp.Color, COUNT( p.PointID ) AS PointCount
            FROM Point AS p
            INNER JOIN TeamPlayer AS tp ON p.TeamPlayerID = tp.TeamPlayerID
            WHERE p.PointType =1
            GROUP BY tp.GameID, tp.Color
            Order By PointCount DESC");

        $maxAmt = $data[0]['PointCount'];
        
        //fill the data
        foreach($data as $row) {
            if($maxAmt != $row['PointCount']){
                //break out
                break 1;
            }

            //create the game
            $gameCreated = new game($row['GameID']);

            //add game to the collection
            $col->add($gameCreated);
        }

        //return the collection
        return $col;
    }


      /*
     * NAME:    getOneHundredGoalClub
     * PARAMS:  N/A
     * DESC:    gets the collection of players at 100 goals
     *
     */
    public function getOneHundredGoalClub(){
        //variable declaration
        $col = $col = $this->getPointClub(1,100);
        //return the collection
        return $col;
    }

      /*
     * NAME:    getOneHundredAssistClub
     * PARAMS:  N/A
     * DESC:    gets the collection of players at 100 assist
     *
     */
    public function getOneHundredAssistClub(){
        //variable declaration
        $col = $this->getPointClub(2,100);
        //return the collection
        return $col;
    }

      /*
     * NAME:    getFiveHundredPointClub
     * PARAMS:  N/A
     * DESC:    gets the collection of players at 500 Point
     *
     */
    public function getFiveHundredPointClub(){
        //variable declaration
        $col = $this->getPointClub(0,500);
        //return the collection
        return $col;
    }


      /*
     * NAME:    getOneThousandPointClub
     * PARAMS:  N/A
     * DESC:    gets the collection of players at 1000 Point
     *
     */
    public function getOneThousandPointClub(){
        //variable declaration
        $col = $this->getPointClub(0,1000);
        //return the collection
        return $col;
    }

      /*
     * NAME:    getPointClub
     * PARAMS:  $p_pointType = point type, 0=point,1=goal,2=assist
     * DESC:    gets a collection based off params.
     *
     */
    public function getPointClub($p_pointType, $p_pointCount){
        //variable declaration
        $col = new collection();
        $sqlString = "";

//        //create the string
//        $sqlString = "
//            SELECT PlayerID, PointCount
//            FROM (
//                    SELECT tp.PlayerID, COUNT( p.PointID ) AS PointCount
//                    FROM Point AS p
//                    INNER JOIN TeamPlayer AS tp ON p.TeamPlayerID = tp.TeamPlayerID
//                    INNER JOIN Game AS g ON tp.GameID = g.GameID
//                    WHERE tp.Position = 2
//                    AND g.Playoff = 0
//                    ";
//
//        if($p_pointType > 0){
//            //a point type was selected
//            $sqlString = $sqlString . " AND p.PointType = " . $p_pointType;
//        }
//
//        $sqlString = $sqlString . " GROUP BY tp.PlayerID
//            ) As PointCountGet
//            WHERE PointCount >= " . $p_pointCount;
//        $sqlString = $sqlString . " ORDER BY PointCount DESC";
        
         $sqlString = "
            SELECT PlayerID, Assists, Goals
            FROM (
              SELECT PlayerID, SUM(Assists) AS Assists, SUM(Goals) AS Goals
              FROM QuickPlayerDataTable 
              WHERE Playoff = 0
              GROUP BY PlayerID
            ) AS PlayerPoints ";
         if($p_pointType == 1){
             $sqlString = $sqlString . " WHERE Goals >= " . $p_pointCount;
         }elseif($p_pointType == 2){
             $sqlString = $sqlString . " WHERE Assists >= " . $p_pointCount;
         }else{
             $sqlString = $sqlString . " WHERE (Goals + Assists) >= " . $p_pointCount;
         }
         
        
        
        

        //database connection
        $d = new db(0);
        
        //fetch the data
        $data = $d->fetch($sqlString);


        //fill the data
        foreach($data as $row) {

            //create the game
            $playerCreated = new player($row['PlayerID']);

            //add game to the collection
            $col->add($playerCreated);
        }
        //return the collection
        return $col;
    }




    /*
     * NAME:    getCurrentSeasonPointLeaders
     * PARAMS:  N/A
     * DESC:    gets the collection of current season point leaders
     *
     */
    public function getCurrentSeasonPointLeaders(){
        //variable declaration
        $col = $this->getSeasonScoringLeaders(0,game::getMaxSeason());
        //return the collection
        return $col;
    }

    /*
     * NAME:    getCurrentSeasonAssistLeaders
     * PARAMS:  N/A
     * DESC:    gets the collection of current season assist leaders
     *
     */
    public function getCurrentSeasonAssistLeaders(){
        //variable declaration
        $col = $this->getSeasonScoringLeaders(2,game::getMaxSeason());
        //return the collection
        return $col;
    }


    /*
     * NAME:    getCurrentSeasonGoalLeaders
     * PARAMS:  N/A
     * DESC:    gets the collection of current season goal leaders
     *
     */
    public function getCurrentSeasonGoalLeaders(){
        //variable declaration
        $col = $this->getSeasonScoringLeaders(1,game::getMaxSeason());
        //return the collection
        return $col;
    }


    
      /*
     * NAME:    getSeasonScoringLeaders
     * PARAMS:  $p_pointType = point type, 0=point,1=goal,2=assist
       *        $p_seasonID  = season id, 0 = all
     * DESC:    gets a collection based off params.
     *
     */
    public function getSeasonScoringLeaders($p_pointType, $p_seasonID){
        //variable declaration
        $col = new collection();
        $sqlString = "";

        //create the string
        $sqlString = "
            SELECT PlayerID, PointCount
            FROM (
                    SELECT tp.PlayerID, COUNT( p.PointID ) AS PointCount
                    FROM Point AS p
                    INNER JOIN TeamPlayer AS tp ON p.TeamPlayerID = tp.TeamPlayerID
                    INNER JOIN Game AS g ON tp.GameID = g.GameID
                    WHERE 1 = 1
                    AND g.Playoff = 0
                    AND    tp.Position = 2
                    ";
        if($p_seasonID > 0){
            $sqlString = $sqlString . " AND g.SeasonID = " . $p_seasonID;
        }

        if($p_pointType > 0){
            //a point type was selected
            $sqlString = $sqlString . " AND p.PointType = " . $p_pointType;
        }

        $sqlString = $sqlString . " GROUP BY tp.PlayerID
            ) As PointCountGet";
        $sqlString = $sqlString . " ORDER BY PointCount DESC";

        //database connection
        $d = new db(0);

        //fetch the data
        $data = $d->fetch($sqlString);


        $maxAmt = $data[0]['PointCount'];
        //fill the data
        foreach($data as $row) {
            if($maxAmt != $row['PointCount']){
                //break out
                break 1;
            }

            //create the game
            $playerCreated = new player($row['PlayerID']);

            //add game to the collection
            $col->add($playerCreated);
        }
        //return the collection
        return $col;
    }




      /*
     * NAME:    getSeasonScoringPerGameLeaders
     * PARAMS:  $p_pointType = point type, 0=point,1=goal,2=assist
       *        $p_seasonID  = season id, 0 = all
     * DESC:    gets a collection based off params.
     *
     */
    public function getSeasonScoringPerGameLeaders($p_pointType, $p_seasonID){
        //variable declaration
        $col = new collection();
        $sqlString = "";

        //create the string
        $sqlString = "
            SELECT PlayerID, PointCount, GameCount, PerGame
            FROM (
            SELECT PointCountGet.PlayerID, PointCountGet.PointCount, COUNT ( ag.GameID ) AS GameCount, ( PointCountGet.PointCount / GameCount ) AS PerGame
            FROM (
                    SELECT tp.PlayerID, COUNT( p.PointID ) AS PointCount
                    FROM Point AS p
                    INNER JOIN TeamPlayer AS tp ON p.TeamPlayerID = tp.TeamPlayerID
                    INNER JOIN Game AS g ON tp.GameID = g.GameID
                    WHERE 1 = 1
                    ";
        if($p_seasonID > 0){
            $sqlString = $sqlString . " AND g.SeasonID = " . $p_seasonID;
        }

        if($p_pointType > 0){
            //a point type was selected
            $sqlString = $sqlString . " AND p.PointType = " . $p_pointType;
        }

        $sqlString = $sqlString . " GROUP BY tp.PlayerID
            ) As PointCountGet
         INNER JOIN TeamPlayer AS atp ON PointCountGet.PlayerID = atp.PlayerID
         INNER JOIN Game AS ag ON atp.GameID = ag.GameID
         WHERE 1 = 1";
        if($p_seasonID > 0){
            $sqlString = $sqlString . " AND ag.SeasonID = " . $p_seasonID;
        }

        $sqlString = $sqlString . " GROUP BY PointCountGet.PlayerID, PointCountGet.PointCount ";


        $sqlString = $sqlString . " ) AS FinalSelect ORDER BY PerGame DESC";

        //database connection
        $d = new db(0);

        //fetch the data
        $data = $d->fetch($sqlString);

        echo $d->log;

        $maxAmt = $data[0]['PointCount'];
        //fill the data
        foreach($data as $row) {
            if($maxAmt != $row['PointCount']){
                //break out
                break 1;
            }

            //create the game
            $playerCreated = new player($row['PlayerID']);

            //add game to the collection
            $col->add($playerCreated);
        }
        //return the collection
        return $col;
    }


    /*
     * NAME:    getCurrentSeasonPointLeaders
     * PARAMS:  N/A
     * DESC:    gets the collection of current season point leaders
     *
     */
    public function getCurrentSeasonPPGLeaders(){
        //variable declaration
        $col = $this->getSeasonScoringPerGameLeaders(0,game::getMaxSeason());
        //return the collection
        return $col;
    }

    /*
     * NAME:    getCurrentSeasonAssistLeaders
     * PARAMS:  N/A
     * DESC:    gets the collection of current season assist leaders
     *
     */
    public function getCurrentSeasonAPGLeaders(){
        //variable declaration
        $col = $this->getSeasonScoringPerGameLeaders(2,game::getMaxSeason());
        //return the collection
        return $col;
    }


    /*
     * NAME:    getCurrentSeasonGoalLeaders
     * PARAMS:  N/A
     * DESC:    gets the collection of current season goal leaders
     *
     */
    public function getCurrentSeasonGPGLeaders(){
        //variable declaration
        $col = $this->getSeasonScoringPerGameLeaders(1,game::getMaxSeason());
        //return the collection
        return $col;
    }


    /*
     * NAME:    getCurrentBestGoalieGPM
     * PARAMS:  N/A
     * DESC:    gets the collection of current season goalie GPM
     *
     */
    public function getCurrentBestGoalieGPM(){
        //variable declaration
        $col = $this->getBestGoalieGPM(game::getMaxSeason());
        //return the collection
        return $col;
    }
    
      /*
     * NAME:    getBestGoalieGPM
     * PARAMS:  $p_seasonID = the season
     * DESC:    gets a collection based off params.
     *
     */
    public function getBestGoalieGPM($p_seasonID){
        //variable declaration
        $col = new collection();
        $sqlString = "";

        //create the string
        $sqlString = "
            SELECT SeasonID, GoalsPerMin, PlayerID
            FROM (
                SELECT SeasonID, PlayerID,(GoalsAgainst/(TotalGameSeconds/60)) As GoalsPerMin
                FROM QuickGoalieDataTable
                WHERE SeasonID = " . $p_seasonID . " 
                GROUP BY SeasonID, PlayerID 
                
            ) AS gpmTemp
            WHERE GoalsPerMin = (
                SELECT MIN(GoalsPerMin)
                FROM (
                    SELECT SeasonID, PlayerID,(GoalsAgainst/(TotalGameSeconds/60)) As GoalsPerMin
                    FROM QuickGoalieDataTable
                    WHERE SeasonID = " . $p_seasonID . " 
                    GROUP BY SeasonID, PlayerID 
                    ) as gpmtemptwo
            )";
        
        //HACK, not the true value
        $sqlString = "
            SELECT SeasonID, GoalsPerMin, PlayerID
            FROM (
                SELECT SeasonID, PlayerID,(GoalsAgainst/(TotalGameSeconds/60)) As GoalsPerMin
                FROM QuickGoalieDataTable
                WHERE SeasonID = " . $p_seasonID . " 
                GROUP BY SeasonID, PlayerID 
                
            ) AS gpmTemp
            ORDER BY GoalsPerMin ASC
            LIMIT 1
            ";

        //database connection
        $d = new db(0);

        //fetch the data
        $data = $d->fetch($sqlString);


        //fill the data
        foreach($data as $row) {
 
            //create the game
            $playerCreated = new player($row['PlayerID']);

            //add game to the collection
            $col->add($playerCreated);
        }
        //return the collection
        return $col;
    }

    
    
    /*
     * NAME:    getCurrentBestGoalieGAA
     * PARAMS:  N/A
     * DESC:    gets the collection of current season goalie GAA
     *
     */
    public function getCurrentBestGoalieGAA(){
        //variable declaration
        $col = $this->getBestGoalieGAA(game::getMaxSeason());
        //return the collection
        return $col;
    } 
    
      /*
     * NAME:    getBestGoalieGAA
     * PARAMS:  $p_seasonID = the season
     * DESC:    gets a collection based off params.
     *
     */
    public function getBestGoalieGAA($p_seasonID){
        //variable declaration
        $col = new collection();
        $sqlString = "";

        //create the string
        $sqlString = "
            SELECT SeasonID, GoalsAgainstAvg, PlayerID
            FROM (
                SELECT SeasonID, PlayerID,(GoalsAgainst/(Wins+Losses)) As GoalsAgainstAvg
                FROM QuickGoalieDataTable
                WHERE SeasonID = " . $p_seasonID . " 
                GROUP BY SeasonID, PlayerID 
                
            ) AS gpmTemp
            WHERE GoalsAgainstAvg = (
                SELECT MIN(GoalsAgainstAvg)
                FROM (
                    SELECT SeasonID, PlayerID,(GoalsAgainst/(Wins+Losses)) As GoalsAgainstAvg
                    FROM QuickGoalieDataTable
                    WHERE SeasonID = " . $p_seasonID . " 
                    GROUP BY SeasonID, PlayerID 
                    ) as gpmtemptwo
            )";
        
       $sql = "
            SELECT PlayerID, (GA/(GP)) AS GAA, GP, GA
            FROM(
                SELECT PlayerID, SUM(Wins+Losses) AS GP, SUM(GoalsAgainst) AS GA
                FROM QuickGoalieDataTable
                WHERE 1 = 1 ";
        if($p_seasonID >= 0){
           $sql = $sql . " AND SeasonID = " .  db::fmt($p_seasonID,1);
        }
        $sql = $sql . " GROUP BY PlayerID
            ) AS TotalWinCount
            WHERE (GA/GP) = (

                SELECT MIN(GA/GP)
                FROM(
                    SELECT PlayerID, SUM(Wins+Losses) AS GP, SUM(GoalsAgainst) AS GA
                    FROM QuickGoalieDataTable
                    WHERE 1 = 1 ";
        if($p_seasonID >= 0){
           $sql = $sql . " AND SeasonID = " .  db::fmt($p_seasonID,1);
        }
        $sql = $sql . " GROUP BY PlayerID
                ) AS TotalWinSubCount
                    WHERE GP > 20
                )
                AND GP > 20";   

 
        
       
        //database connection
        $d = new db(0);

        //fetch the data
        $data = $d->fetch($sqlString);



        //fill the data
        foreach($data as $row) {

            //create the game
            $playerCreated = new player($row['PlayerID']);

            //add game to the collection
            $col->add($playerCreated);
        }
        //return the collection
        return $col;
    }

}
?>
