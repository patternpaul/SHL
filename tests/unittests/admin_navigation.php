<?php

/**
 * NAME:    admin_navigation_test.php
 * AUTHOR:  Paul Everton
 * DATE:    Feb 26, 2013
 * DESCRIPTION: tests checking admin functionality
 */

class admin_navigation_test extends WebTestCase {
    
    //Class Variables
    private $cs_userName;
    private $cs_userPassword;
    private $cs_urlToUse;

    //constructor
    function admin_navigation_test(){
        $this->cs_userName = "admin";
        $this->cs_userPassword = "password";
        $this->cs_urlToUse = "http://" . $_SERVER['SERVER_NAME'] . "/";
    }
    
    
    
    
    function testRandomClick(){
        //variable declaration
        $ls_url = $this->cs_urlToUse;
        $li_pageClicks = 50;
        
        for ( $alpha = 0; $alpha < $li_pageClicks; $alpha += 1) {
            $this->get($this->cs_urlToUse);
            //check to make sure no internal error
            $this->assertNoText("Internal Server Error", "Error Message");
            
            //get available URLs
            $la_urls = $this->getBrowser()->getUrls();

            //get a random URL
            $ls_url = $la_urls[rand(0,count($la_urls)-1)];
        }
    }
    
    /*
     * NAME:    testUserLogin
     * PARAMS:  N/A
     * DESC:    user flow to login
     */
    function testUserLogin() {
        $this->get($this->cs_urlToUse);
        $this->assertText('SHL', 'SHL was not found.');
        
        //fill out the login form
        $this->setFieldById('username', $this->cs_userName);
        $this->setFieldById('password', $this->cs_userPassword);
        $this->clickSubmitById('loginsubmit');
        
        $this->assertText('Edit Profile', 'Edit Profile was not found.');
    }

    
    /*
     * NAME:    testUserLogin
     * PARAMS:  N/A
     * DESC:    user flow to login
     */
    function testAddRadomGames() {
        //variable declaration
        $li_gameCount = 16;
        $lo_randomGame;
        $lo_randomTeam;
        $lo_player;
        $lo_playerFound;
        $la_playerPoints;
        $li_teamColor;
        
        
        $lo_prePlayer;
        $li_prePlayerGoals;
        $li_prePlayerAssists;
        $li_prePlayerPoints;
        $ls_prePlayerName;
        
        
        //login
        $this->testUserLogin();
        
        
        $lo_randomGame = $this->CreateRandomGame();
        
        $li_teamColor = game::getRandomTeamColor();
        //get a random team
        if($li_teamColor == game::TEAMBLACK){
            $lo_randomTeam = $lo_randomGame->getTeamBlack();
        }else{
            $lo_randomTeam = $lo_randomGame->getTeamWhite();
        }
        
        //get a random player from that team
        $lo_player = $lo_randomTeam->get(rand(0,$lo_randomTeam->count() - 1));
        
        while($lo_player->c_position == 1){
            $lo_player = $lo_randomTeam->get(rand(0,$lo_randomTeam->count() - 1));
        }
        
        //pre pull that players info
        $lo_prePlayer = new player($lo_player->getPlayerID());
        $li_prePlayerID = $lo_player->getPlayerID();
        $this->dump("player ID: " . $li_prePlayerID);
        $lo_prePlayer->load();
        $lo_prePlayer->quickLoad(12, 0);
        
        $ls_prePlayerName = $lo_prePlayer->getShortName();
        $this->dump("Name: " . $ls_prePlayerName);
        $li_prePlayerGoals = $lo_prePlayer->getQuickGoalCount();
        $this->dump("Goals: " . $li_prePlayerGoals);
        $li_prePlayerAssists = $lo_prePlayer->getQuickAssistCount();
        $this->dump("Assists: " . $li_prePlayerAssists);
        
        
        $la_playerPoints = $lo_player->getTeamPlayerPoints();
        $this->dump("Game #: " . $lo_randomGame->getGameNum());
        $this->dump("Player ID : " . $lo_prePlayer->getPlayerID());
        $this->dump("Team Player Points Count: " . $la_playerPoints->count());
            for($zeta = 0; $zeta < $la_playerPoints->count(); $zeta += 1){
                
                $curPoint = $la_playerPoints->get($zeta);
                $this->dump("Point Type: " . $curPoint->c_pointType);
                if($curPoint->c_pointType == 1){
                    $li_prePlayerGoals++;
                }else{
                    $li_prePlayerAssists++;
                }
            }

        
        $this->AddGame($lo_randomGame);     
        
        
        //add games
        for ( $alpha = 1; $alpha < $li_gameCount; $alpha += 1) {
            $lo_playerFound = null;
            $lo_randomGame = $this->CreateRandomGame();
            
            //find the player
            $lo_playerFound = $lo_randomGame->getTeamBlack()->getPlayer($lo_player->getPlayerID());
            if(is_null($lo_playerFound)){
                $lo_playerFound = $lo_randomGame->getTeamWhite()->getPlayer($lo_player->getPlayerID());
            }
            
            if(!is_null($lo_playerFound)){
                if($lo_playerFound->c_position != 1){
                    $this->dump("Game #: " . $lo_randomGame->getGameNum());
                    $this->dump("Player ID : " . $lo_prePlayer->getPlayerID());
                    $this->dump("Player Name Found: " . $lo_playerFound->getShortName());
                    //We found the player
                    $la_playerPoints = $lo_playerFound->getTeamPlayerPoints();
                    $this->dump("Team Player Points Count: " . $la_playerPoints->count());
                    for($zeta = 0; $zeta < $la_playerPoints->count(); $zeta += 1){
                        $curPoint = $la_playerPoints->get($zeta);
                        $this->dump("Point Type: " . $curPoint->c_pointType);
                        if($curPoint->c_pointType == 1){
                            $li_prePlayerGoals++;
                        }else{
                            $li_prePlayerAssists++;
                        }
                    }
                }
            }
            
            $this->AddGame($lo_randomGame);
        }
        
//        //go and check that the points added for the player are properly added
//        $curPlayerPoints = $lo_player->getTeamPlayerPoints();
//        for($zeta = 0; $zeta < $curPlayerPoints->count(); $zeta += 1){
//            $curPoint = $curPlayerPoints->get($zeta);
//            if($curPoint->c_pointType == 1){
//                $li_prePlayerGoals++;
//            }else{
//                $li_prePlayerAssists++;
//            }
//        }

        $lo_postPlayer = new player($li_prePlayerID);
        $this->dump("player ID: " . $li_prePlayerID);
        $lo_postPlayer->load();
        $lo_postPlayer->quickLoad(12, 0);
        
        $ls_postPlayerName = $lo_postPlayer->getShortName();
        $this->dump("Name: " . $ls_postPlayerName);
        $li_postPlayerGoals = $lo_postPlayer->getQuickGoalCount();
        $this->dump("Goals: " . $li_postPlayerGoals);
        $li_postPlayerAssists = $lo_postPlayer->getQuickAssistCount();
        $this->dump("Assists: " . $li_postPlayerAssists);
       

        $this->dump("Name: " . $ls_prePlayerName);

        $this->dump("Goals: " . $li_prePlayerGoals);

        $this->dump("Assists: " . $li_prePlayerAssists);
        
        
        $this->assertTrue($ls_prePlayerName == $ls_postPlayerName);
        $this->assertTrue($li_prePlayerGoals == $li_postPlayerGoals);
        $this->assertTrue($li_prePlayerAssists == $li_postPlayerAssists);
        
    }  
    
    
    
    
    /**
     * returns a randomly created game
     * @return game the random game created
     */
    private function CreateRandomGame(){
        //variable declaration
        $goaliePlayer = true;
        $seasonID = 12;
        $gameNum = 1;
        $po_game = new game(0);
        $teamWhite = new teamPlayerCollection();
        $teamBlack = new teamPlayerCollection();
        $teamWhiteGoalCount = 10;
        $teamBlackGoalCount = 8;
        
        $la_params = array();
        $ls_sql = '
          SELECT GameNum
          FROM Game
          WHERE SeasonID = :seasonID
          ORDER BY GameNum DESC
          LIMIT 1';
        
        $la_params[':seasonID'] = $seasonID;
        
        //querry the DB
        $data = DBFac::getDB()->exec($ls_sql, $la_params);

        //get the game num
        foreach($data as $row) {
            $gameNum = $row['GameNum'] + 1;
        }
        
        $la_params = array();
        
        //set the game time
        $po_game->setGameDate("2013-02-14");
        $po_game->setGameStart("2:20 PM");
        $po_game->setGameEnd("3:20 PM");
        $po_game->setSeasonID($seasonID);
        $po_game->setGameNum($gameNum);
        $po_game->setPlayoff(0);  
        
        $ls_sql = '
            SELECT  p.PlayerID
            FROM    Player AS p
            ORDER BY RAND()
            LIMIT 5'; 
        
        //querry the DB
        $data = DBFac::getDB()->exec($ls_sql, $la_params);

        //Setup the players
        foreach($data as $row) {
            //player selected, create player
            $teamPlayerCreated = new teamPlayer(0);
            $teamPlayerCreated->c_color = 2;
            if($goaliePlayer){
                $teamPlayerCreated->c_position = 1;
                $goaliePlayer = false;
            }else{
                $teamPlayerCreated->c_position = 2;
                
            }
            $teamPlayerCreated->setPlayerID($row['PlayerID']);
            //load the object
            $teamPlayerCreated->load();
            //add the goalie
            $teamWhite->add($teamPlayerCreated);
        }      
        $po_game->setTeamWhite($teamWhite);
        
        
        
        //querry the DB
        $data = DBFac::getDB()->exec($ls_sql, $la_params);

        $goaliePlayer = true;
        //Setup the players
        foreach($data as $row) {
            //player selected, create player
            $teamPlayerCreated = new teamPlayer(0);
            $teamPlayerCreated->c_color = 1;
            if($goaliePlayer){
                $teamPlayerCreated->c_position = 1;
                $goaliePlayer = false;
            }else{
                $teamPlayerCreated->c_position = 2;                
            }
            $teamPlayerCreated->setPlayerID($row['PlayerID']);
            //load the object
            $teamPlayerCreated->load();
            //add the goalie
            $teamBlack->add($teamPlayerCreated);
        }  
        $po_game->setTeamBlack($teamBlack);
        
        
        //for right now it will be white 10, black 8. TODO: Randomize
        for ( $alpha = 1; $alpha <= $teamWhiteGoalCount; $alpha += 1) {

            //create the point
            $pointHolder = new point(0);
            $pointHolder->c_pointType = 1;
            $pointHolder->c_pointNum = $alpha;
            //get random player
            $po_game->getTeamWhite()->get(rand(0,$po_game->getTeamWhite()->count()-1))->getTeamPlayerPoints()->add($pointHolder);

            //create the point
            $pointHolder = new point(0);
            $pointHolder->c_pointType = 2;
            $pointHolder->c_pointNum = $alpha;
            //get the player
            $po_game->getTeamWhite()->get(rand(0,$po_game->getTeamWhite()->count()-1))->getTeamPlayerPoints()->add($pointHolder);
        }
        
        for ( $alpha = 1; $alpha <= $teamBlackGoalCount; $alpha += 1) {

            //create the point
            $pointHolder = new point(0);
            $pointHolder->c_pointType = 1;
            $pointHolder->c_pointNum = $alpha;
            //get the player
            $po_game->getTeamBlack()->get(rand(0,$po_game->getTeamBlack()->count()-1))->getTeamPlayerPoints()->add($pointHolder);


            //create the point
            $pointHolder = new point(0);
            $pointHolder->c_pointType = 2;
            $pointHolder->c_pointNum = $alpha;
            //get the player
            $po_game->getTeamBlack()->get(rand(0,$po_game->getTeamBlack()->count()-1))->getTeamPlayerPoints()->add($pointHolder);
        }

        
        return $po_game;
    }
    /*
     * NAME:    AddGame
     * PARAMS:  N/A
     * DESC:    user flow to add a game. Assumes user logged in
     */
    function AddGame($po_game) {
        $this->assertTrue($po_game instanceof game, "Game not passed in");
        
        $teamWhite = $po_game->getTeamWhite();
        $teamBlack = $po_game->getTeamBlack();
        
        
        $this->clickLink('Add Game');
        $this->assertText('Add Game ', 'Add Game was not found.');
        
        $this->setFieldById('gamedate', $po_game->getGameDate());
        
        $this->setFieldById('gamestarthour', $po_game->getGameStartHour());
        $this->setFieldById('gamestartminute', $po_game->getGameStartMinute());
        $this->setFieldById('gamestartampm', $po_game->getGameStartAMPM());
        
        $this->setFieldById('gameendhour', $po_game->getGameEndHour());
        $this->setFieldById('gameendminute', $po_game->getGameEndMinute());
        $this->setFieldById('gameendampm', $po_game->getGameEndAMPM());    
        
        $this->setFieldById('gameplayoff', $po_game->getPlayoff()); 
        
        $this->setFieldById('seasonid', $po_game->getSeasonID()); 
        
        $this->setFieldById('gamenumber', $po_game->getGameNum()); 
        $this->assertTrue($po_game->getGameNum() > 0);
        
        $this->clickSubmitById('submitnext');
        
        $this->assertText('Enter the Teams', 'Enter the Teams was not found.');
        
        //loop over the collection of players
        $playerCount = 0;
        for ( $beta = 0; $beta < $teamWhite->count(); $beta += 1) {
            //the player to output
            $teamPlayer = $teamWhite->get($beta);
            $idSelector = "";

            if($teamPlayer->c_position == 1){
                $idSelector = "gwhite";
            }else{
                $idSelector = "whitep" . $playerCount;
                $playerCount++;
            }

            $this->setFieldById($idSelector, $teamPlayer->getPlayerID()); 
        }
        
        $playerCount = 0;
        for ( $beta = 0; $beta < $teamBlack->count(); $beta += 1) {
            //the player to output
            $teamPlayer = $teamBlack->get($beta);
            $idSelector = "";

            if($teamPlayer->c_position == 1){
                $idSelector = "gblack";
            }else{
                $idSelector = "blackp" . $playerCount;
                $playerCount++;
            }

            $this->setFieldById($idSelector, $teamPlayer->getPlayerID()); 
        }   
        
        
        
        $this->clickSubmitById('submitnext');
        
        $this->assertText('Team White Goals', 'Enter the Teams was not found.');
        $this->assertText('Team Black Goals', 'Enter the Teams was not found.');

       
            
        $teamWhite = $po_game->getTeamWhite();
        $teamBlack = $po_game->getTeamBlack();
       
        //loop over the scores for display
        for($beta = 0; $beta < $teamWhite->count(); $beta += 1){
            $curPlayer = $teamWhite->get($beta);
            $curPlayerPoints = $curPlayer->getTeamPlayerPoints();
            $curPlayerID = $curPlayer->getPlayerID();
            $this->assertTrue($curPlayerID > 0);
            for($zeta = 0; $zeta < $curPlayerPoints->count(); $zeta += 1){
                $curPoint = $curPlayerPoints->get($zeta);
                if($curPoint->c_pointType == 1){
                    $this->setFieldById("whiteg" . $curPoint->c_pointNum, $curPlayerID);
                }else{
                    $this->setFieldById("whitea" . $curPoint->c_pointNum, $curPlayerID);
                }
            }
        }
        
        //loop over the scores for display
        for($beta = 0; $beta < $teamBlack->count(); $beta += 1){
            $curPlayer = $teamBlack->get($beta);
            $curPlayerPoints = $curPlayer->getTeamPlayerPoints();
            $curPlayerID = $curPlayer->getPlayerID();
            $this->assertTrue($curPlayerID > 0);
            for($zeta = 0; $zeta < $curPlayerPoints->count(); $zeta += 1){
                $curPoint = $curPlayerPoints->get($zeta);
                if($curPoint->c_pointType == 1){
                    $this->setFieldById("blackg" . $curPoint->c_pointNum, $curPlayerID);
                }else{
                    $this->setFieldById("blacka" . $curPoint->c_pointNum, $curPlayerID);
                }
            }
        }
        
        
  

        $this->clickSubmitById('submitnext');
        $this->assertText('Game Added', "Game Added was not found");
        //$this->dump($this->getBrowser()->getContent());
        
    } 
    
}
?>
