<div class="row">
    <div class="span10">
 
        <h2>Regular Season Stats</h2>
        <table id="TABLE_16"  class="zebra-striped stats player_stats">
            <thead>
                <tr>
                    <th>Season</th>
                    <th class="twipsy-head" rel='twipsy' title='Goals'>G</th>
                    <th class="twipsy-head" rel='twipsy' title='Assists'>A</th>
                    <th class="twipsy-head" rel='twipsy' title='Points'>P</th>
                    <th class="twipsy-head" rel='twipsy' title='Games Played'>GP</th>
                    <th class="twipsy-head" rel='twipsy' title='Goals Per Game'>GPG</th>
                    <th class="twipsy-head" rel='twipsy' title='Assists Per Game'>APG</th>
                    <th class="twipsy-head" rel='twipsy' title='Points Per Game'>PPG</th>
                    <th class="twipsy-head" rel='twipsy' title='Team Goals Percentage'>TG%</th>
                    <th class="twipsy-head" rel='twipsy' title='Game Winning Goals'>GWG</th>
                    <th class="twipsy-head" rel='twipsy' title='Wins'>W</th>
                    <th class="twipsy-head" rel='twipsy' title='Loss'>L</th>
                    <th class="twipsy-head" rel='twipsy' title='Win/Loss +/-'>+/-</th>
                    <th class="twipsy-head" rel='twipsy' title='Win Percentage'>W%</th>
                </tr>
            </thead>
            <tbody>
                <?php
                    $currentSeason;

                    //loop over the season stats for display
                    for($alpha = $careerRegularData->count() - 1; $alpha >= 0 ; $alpha -= 1){
                        $currentSeason = $careerRegularData->get($alpha);
                ?>
                    <tr>
                        <td><a href="/seasonDetails.php?seasonid=<?php echo $currentSeason->c_seasonID; ?>"><?php echo $currentSeason->c_seasonID; ?></a></td>
                        <td><?php echo $currentSeason->getGoalCount(); ?></td>
                        <td><?php echo $currentSeason->getAssistCount(); ?></td>
                        <td><?php echo $currentSeason->getPointCount(); ?></td>
                        <td><?php echo $currentSeason->getGameCount(); ?></td>
                        <td><?php echo $currentSeason->getGPG(); ?></td>
                        <td><?php echo $currentSeason->getAPG(); ?></td>
                        <td><?php echo $currentSeason->getPPG(); ?></td>
                        <td><?php echo $currentSeason->getTeamPointPercent(); ?></td>
                        <td><?php echo $currentSeason->getGWGCount(); ?></td>
                        <td><?php echo $currentSeason->getWinCount(); ?></td>
                        <td><?php echo $currentSeason->getLossCount(); ?></td>
                        <td><?php echo $currentSeason->getWinLossCount(); ?></td>
                        <td><?php echo $currentSeason->getWinPercent(); ?></td>
                    </tr>
                <?php    
                    }
                ?>
            </tbody>
            <tfoot>
                <tr>
                    <td>TOTAL</td>
                    <td><?php echo $careerRegularData->getGoalCount(); ?></td>
                    <td><?php echo $careerRegularData->getAssistCount(); ?></td>
                    <td><?php echo $careerRegularData->getPointCount(); ?></td>
                    <td><?php echo $careerRegularData->getGameCount(); ?></td>
                    <td><?php echo $careerRegularData->getGPG(); ?></td>
                    <td><?php echo $careerRegularData->getAPG(); ?></td>
                    <td><?php echo $careerRegularData->getPPG(); ?></td>
                    <td><?php echo $careerRegularData->getTeamPointPercent(); ?></td>
                    <td><?php echo $careerRegularData->getGWGCount(); ?></td>
                    <td><?php echo $careerRegularData->getWinCount(); ?></td>
                    <td><?php echo $careerRegularData->getLossCount(); ?></td>
                    <td><?php echo $careerRegularData->getWinLossCount(); ?></td>
                    <td><?php echo $careerRegularData->getWinPercent(); ?></td>
                </tr>
            </tfoot>
        </table>

    </div>
</div>

<div class="row">
    <div class="span10">
 
        <h2>Playoff Stats</h2>
        <table id="TABLE_16"  class="zebra-striped stats player_stats">
            <thead>
                <tr>
                    <th>Season</th>
                    <th class="twipsy-head" rel='twipsy' title='Goals'>G</th>
                    <th class="twipsy-head" rel='twipsy' title='Assists'>A</th>
                    <th class="twipsy-head" rel='twipsy' title='Points'>P</th>
                    <th class="twipsy-head" rel='twipsy' title='Games Played'>GP</th>
                    <th class="twipsy-head" rel='twipsy' title='Goals Per Game'>GPG</th>
                    <th class="twipsy-head" rel='twipsy' title='Assists Per Game'>APG</th>
                    <th class="twipsy-head" rel='twipsy' title='Points Per Game'>PPG</th>
                    <th class="twipsy-head" rel='twipsy' title='Team Goals Percentage'>TG%</th>
                    <th class="twipsy-head" rel='twipsy' title='Game Winning Goals'>GWG</th>
                    <th class="twipsy-head" rel='twipsy' title='Wins'>W</th>
                    <th class="twipsy-head" rel='twipsy' title='Loss'>L</th>
                    <th class="twipsy-head" rel='twipsy' title='Win/Loss +/-'>+/-</th>
                    <th class="twipsy-head" rel='twipsy' title='Win Percentage'>W%</th>
                </tr>
            </thead>
            <tbody>
                <?php
                    $currentSeason;

                    //loop over the season stats for display
                    for($alpha = $careerPlayoffData->count() - 1; $alpha >= 0 ; $alpha -= 1){
                        $currentSeason = $careerPlayoffData->get($alpha);
                ?>
                    <tr>
                        <td><a href="/seasonDetails.php?seasonid=<?php echo $currentSeason->c_seasonID; ?>"><?php echo $currentSeason->c_seasonID; ?></a></td>
                        <td><?php echo $currentSeason->getGoalCount(); ?></td>
                        <td><?php echo $currentSeason->getAssistCount(); ?></td>
                        <td><?php echo $currentSeason->getPointCount(); ?></td>
                        <td><?php echo $currentSeason->getGameCount(); ?></td>
                        <td><?php echo $currentSeason->getGPG(); ?></td>
                        <td><?php echo $currentSeason->getAPG(); ?></td>
                        <td><?php echo $currentSeason->getPPG(); ?></td>
                        <td><?php echo $currentSeason->getTeamPointPercent(); ?></td>
                        <td><?php echo $currentSeason->getGWGCount(); ?></td>
                        <td><?php echo $currentSeason->getWinCount(); ?></td>
                        <td><?php echo $currentSeason->getLossCount(); ?></td>
                        <td><?php echo $currentSeason->getWinLossCount(); ?></td>
                        <td><?php echo $currentSeason->getWinPercent(); ?></td>
                    </tr>
                <?php    
                    }
                ?>
            </tbody>
            <tfoot>
                <tr>
                    <td>TOTAL</td>
                    <td><?php echo $careerPlayoffData->getGoalCount(); ?></td>
                    <td><?php echo $careerPlayoffData->getAssistCount(); ?></td>
                    <td><?php echo $careerPlayoffData->getPointCount(); ?></td>
                    <td><?php echo $careerPlayoffData->getGameCount(); ?></td>
                    <td><?php echo $careerPlayoffData->getGPG(); ?></td>
                    <td><?php echo $careerPlayoffData->getAPG(); ?></td>
                    <td><?php echo $careerPlayoffData->getPPG(); ?></td>
                    <td><?php echo $careerPlayoffData->getTeamPointPercent(); ?></td>
                    <td><?php echo $careerPlayoffData->getGWGCount(); ?></td>
                    <td><?php echo $careerPlayoffData->getWinCount(); ?></td>
                    <td><?php echo $careerPlayoffData->getLossCount(); ?></td>
                    <td><?php echo $careerPlayoffData->getWinLossCount(); ?></td>
                    <td><?php echo $careerPlayoffData->getWinPercent(); ?></td>
                </tr>
            </tfoot>
        </table>

    </div>
</div>



<div class="row">
    <div class="span10">
 
        <h2>Last Five Games</h2>
        <table  class="zebra-striped stats last_game_stats">
            <thead>
                <tr>
                    <th>Season</th>
                    <th>Game</th>
                    <th>Goals</th>
                    <th>Assists</th>
                    <th>Points</th>
                    <th>+/-</th>
                </tr>
            </thead>
            <tbody>
            <?php
                $currentSeason;
                $currentTeamPlayer;
                $currentPoints;
                $currentGame;
                $oddFlip = false;
                $gameCount = 0;
                $winLoss = "-1";
                //loop over the season stats for display
                for($alpha = 0; $alpha < $careerData->count(); $alpha += 1){
                    $currentSeason = $careerData->get($alpha);
                    for($beta = 0; $beta < $currentSeason->count(); $beta += 1){
                        $currentTeamPlayer = $currentSeason->get($beta);
                        $currentTeamPlayerColor = $currentTeamPlayer->c_color;
                        $currentPoints = $currentTeamPlayer->getTeamPlayerPoints();
                        $currentGame = $currentTeamPlayer->getGame();
                        //echo $currentTeamPlayerColor . " " . $currentGame->getWinner() . "<br />";
                        if($currentTeamPlayerColor != $currentGame->getWinner()){
                            $winLoss = "-1";
                        }else{
                            $winLoss = "+1";
                        }
  
                        $playoffChar = "";
                        if($currentGame->getPlayoff()){
                            $playoffChar = "P";
                        }
                        ?>
                        <tr>
                            <td>
                                <a href="/seasonDetails.php?seasonid=<?php echo $currentSeason->c_seasonID; ?>">
                                    <?php echo $currentSeason->c_seasonID; ?>
                                </a>
                            </td>
                            <td>
                                <a href="gameDetails.php?gameid=<?php echo $currentTeamPlayer->c_gameID; ?>">
                                    <?php echo $playoffChar . $currentGame->getGameNum(); ?>
                                </a>
                            </td>
                            <td><?php echo $currentPoints->getGoalCount(); ?></td>
                            <td><?php echo $currentPoints->getAssistCount(); ?></td>
                            <td><?php echo $currentPoints->getPointCount(); ?></td>
                            <td><?php echo $winLoss; ?></td>
                        </tr>
                        <?php 
                        $gameCount += 1;
                        if($gameCount == 5){
                            //break out of the loop
                            break 2;
                        }
                    }
                }
             ?>
                
            </tbody>
        </table>
    </div>
</div>     
        


<?php

    //database connection
    $d = new db(0);

    //fetch the data
    $goalPlayersAll = $d->fetch("
    SELECT PlayerID, GoalCount,
      (
        SELECT COUNT(Goalie.GameID)
        FROM TeamPlayer AS Goalie 
        INNER JOIN TeamPlayer AS Player ON Goalie.GameID = Player.GameID AND Goalie.Color != Player.Color
        INNER JOIN Game AS GM ON Goalie.GameID = GM.GameID
        WHERE Goalie.Position = 1
        AND   Goalie.PlayerID = GAG.PlayerID
        AND   Player.PlayerID = "  . db::fmt($url_playerID,1) . "
        AND   Player.Position = 2
        AND   GM.Playoff = 0
      ) GameCount
    FROM (
      SELECT  GP.PlayerID, COUNT(GP.PlayerID) AS GoalCount
      FROM    QuickPointDetail AS QPD
      INNER JOIN TeamPlayer AS GP ON QPD.GameID = GP.GameID AND GP.Position = 1 AND GP.Color != QPD.Color
      INNER JOIN Game AS QGM ON QPD.GameID = QGM.GameID
      WHERE QPD.GoalPlayerID = "  . db::fmt($url_playerID,1) . "
      AND QGM.Playoff = 0
      GROUP BY GP.PlayerID
      ORDER BY GoalCount DESC
      LIMIT 6
    ) AS GAG
");
    
?>



<div class="row">
    <div class="span10">
        <h2>Goals Against Goalies</h2>
        <table  class="zebra-striped stats matchup_stats">
            <thead>
                <tr>
                    <th class="twipsy-head" rel='twipsy' title='Goalie'>Goalie</th>
                    <th class="twipsy-head" rel='twipsy' title='Goal Count'>Goals</th>
                    <th class="twipsy-head" rel='twipsy' title='Games Played Against'>GP</th>
                    <th class="twipsy-head" rel='twipsy' title='Goals Per Game'>GPG</th>
                    <th class="twipsy-head" rel='twipsy' title="Goalie's Average Goals Per Game">AGPG</th>
                </tr>
            </thead>
            <tbody>
<?php 
    //fill the data
    foreach($goalPlayersAll as $row) {

            $playerDetail = new player($row['PlayerID']);
            $average = 0;
            //getting average
            //fetch the data
            $averageCalc = $d->fetch("
              SELECT  GP.PlayerID,COUNT(GP.PlayerID) AS GoalCount,
              (
                SELECT COUNT(Goalie.GameID)
                FROM TeamPlayer AS Goalie 
                INNER JOIN TeamPlayer AS Player ON Goalie.GameID = Player.GameID AND Goalie.Color != Player.Color
                INNER JOIN Game AS GM ON Goalie.GameID = GM.GameID
                WHERE Goalie.Position = 1
                AND   Goalie.PlayerID = "  . db::fmt($row['PlayerID'],1) . "
                AND   Player.Position = 2
                AND   GM.Playoff = 0
              )  AS GameCount

              FROM    QuickPointDetail AS QPD
              INNER JOIN TeamPlayer AS GP ON QPD.GameID = GP.GameID AND GP.Position = 1 AND GP.Color != QPD.Color
              INNER JOIN Game AS QGM ON QPD.GameID = QGM.GameID
              WHERE GP.PlayerID = "  . db::fmt($row['PlayerID'],1) . "
              AND QGM.Playoff = 0
              GROUP BY GP.PlayerID
              ORDER BY GoalCount DESC
            ");
            
            foreach($averageCalc as $Avrgrow) {
                $average = $Avrgrow['GoalCount'] / $Avrgrow['GameCount'];
           
            }
            
            
?>
                <tr>
                    <td>
                        <a href='/playerDetails.php?playerid=<?php echo $playerDetail->getPlayerID(); ?>&position=1'>
                            <?php echo $playerDetail->getFullName(); ?>
                        </a>
                    </td>
                    <td><?php echo $row['GoalCount']; ?></td>
                    <td><?php echo $row['GameCount']; ?></td>
                    <td><?php echo number_format($row['GoalCount']/$row['GameCount'], 3, '.', '') ?></td>
                    <td><?php echo number_format($average, 3, '.', '') ?></td>
                </tr>
<?php

    }
?>
            </tbody>
        </table>
    </div>
</div>


<?php
    $maxPerSeason = 3;
    $perSeasonCount = 0;
    $seasonID = 0;
    //database connection
    $d = new db(0);

    //fetch the data
    $assitPlayers = $d->fetch("
        SELECT SeasonID, Playoff, GoalPlayer AS GoalPlayerID, AssistPlayer AS AssistPlayerID, HookupCount AS Hookups
        FROM QuickPlayerHookup
        WHERE Playoff = 0
        AND GoalPlayer = "  . db::fmt($url_playerID,1) . "
        ORDER BY SeasonID, Playoff, HookupCount DESC
        ");
    //database connection
    $d = new db(0);

    //fetch the data
    $goalPlayers = $d->fetch("
        SELECT SeasonID, Playoff, GoalPlayer AS GoalPlayerID, AssistPlayer AS AssistPlayerID, HookupCount AS Hookups
        FROM QuickPlayerHookup
        WHERE Playoff = 0
        AND AssistPlayer = "  . db::fmt($url_playerID,1) . "
        ORDER BY SeasonID, Playoff, HookupCount DESC
        ");
    
    
    //database connection
    $d = new db(0);

    //fetch the data
    $assitPlayersAll = $d->fetch("
        SELECT AssistPlayerID, Hookups,
                  (
            SELECT COUNT(GoalPlayer.GameID)
            FROM TeamPlayer AS GoalPlayer 
            INNER JOIN TeamPlayer AS AssistPlayer ON GoalPlayer.GameID = AssistPlayer.GameID
            INNER JOIN Game AS G ON GoalPlayer.GameID = G.GameID
            WHERE GoalPlayer.PlayerID = QPH.AssistPlayerID
            AND   AssistPlayer.PlayerID = "  . db::fmt($url_playerID,1) . "
            AND G.Playoff = 0
          ) AS GameCount
        FROM (
            SELECT AssistPlayer AS AssistPlayerID, SUM(HookupCount) AS Hookups
            FROM QuickPlayerHookup
            WHERE Playoff = 0
            AND GoalPlayer = "  . db::fmt($url_playerID,1) . "
            GROUP BY AssistPlayerID
            ORDER BY Hookups DESC
            LIMIT 5
        ) AS QPH
        ");
    //database connection
    $d = new db(0);

    //fetch the data
    $goalPlayersAll = $d->fetch("
        SELECT GoalPlayerID, Hookups,
                  (
            SELECT COUNT(GoalPlayer.GameID)
            FROM TeamPlayer AS GoalPlayer 
            INNER JOIN TeamPlayer AS AssistPlayer ON GoalPlayer.GameID = AssistPlayer.GameID
            INNER JOIN Game AS G ON GoalPlayer.GameID = G.GameID
            WHERE GoalPlayer.PlayerID = QPH.GoalPlayerID
            AND   AssistPlayer.PlayerID = "  . db::fmt($url_playerID,1) . "
            AND G.Playoff = 0
          ) AS GameCount
        FROM (
            SELECT GoalPlayer AS GoalPlayerID, SUM(HookupCount) AS Hookups
            FROM QuickPlayerHookup
            WHERE Playoff = 0
            AND AssistPlayer = "  . db::fmt($url_playerID,1) . "
            GROUP BY GoalPlayerID
            ORDER BY Hookups DESC
            LIMIT 5
        ) AS QPH
        ");
    
?>



<div class="row">
    <div class="span10">
        <h2>Top 5 Players who assisted you</h2>
        <table  class="zebra-striped stats matchup_stats">
            <thead>
                <tr>
                    <th class="twipsy-head" rel='twipsy' title='Player'>Player</th>
                    <th class="twipsy-head" rel='twipsy' title='Point Count'>Count</th>
                    <th class="twipsy-head" rel='twipsy' title='Games Played Together'>GP</th>
                    <th class="twipsy-head" rel='twipsy' title='Points Per Game'>PPG</th>
                </tr>
            </thead>
            <tbody>
<?php 

    //fill the data
    foreach($assitPlayersAll as $row) {
        $playerDetail = new player($row['AssistPlayerID']);
?>
                <tr>
                    <td>
                        <a href='/playerDetails.php?playerid=<?php echo $playerDetail->getPlayerID(); ?>&position=4'>
                            <?php echo $playerDetail->getFullName(); ?>
                        </a>
                    </td>
                    <td><?php echo $row['Hookups']; ?></td>
                    <td><?php echo $row['GameCount']; ?></td>
                    <td><?php echo number_format($row['Hookups']/$row['GameCount'], 3, '.', '') ?></td>
                </tr>
<?php
    }
?>
            </tbody>
        </table>
    </div>
</div>

<div class="row">
    <div class="span10">
        <h2>Top 5 Players who you assisted</h2>
        <table  class="zebra-striped stats matchup_stats">
            <thead>
                <tr>
                    <th class="twipsy-head" rel='twipsy' title='Player'>Player</th>
                    <th class="twipsy-head" rel='twipsy' title='Point Count'>Count</th>
                    <th class="twipsy-head" rel='twipsy' title='Games Played Together'>GP</th>
                    <th class="twipsy-head" rel='twipsy' title='Points Per Game'>PPG</th>
                </tr>
            </thead>
            <tbody>
<?php 
    $perSeasonCount = 0;
    //fill the data
    foreach($goalPlayersAll as $row) {

            $playerDetail = new player($row['GoalPlayerID']);
?>
                <tr>
                    <td>
                        <a href='/playerDetails.php?playerid=<?php echo $playerDetail->getPlayerID(); ?>&position=4'>
                            <?php echo $playerDetail->getFullName(); ?>
                        </a>
                    </td>
                    <td><?php echo $row['Hookups']; ?></td>
                    <td><?php echo $row['GameCount']; ?></td>
                    <td><?php echo number_format($row['Hookups']/$row['GameCount'], 3, '.', '') ?></td>
                </tr>
<?php

    }
?>
            </tbody>
        </table>
    </div>
</div>







<div class="row">
    <div class="span10">
        <h2>Top 3 Players per season who assisted you</h2>
        <table  class="zebra-striped stats player_stats">
            <thead>
                <tr>
                    <th>Season</th>
                    <th>Player</th>
                    <th>Count</th>
                </tr>
            </thead>
            <tbody>
<?php 

    //fill the data
    foreach($assitPlayers as $row) {
        if($seasonID != $row['SeasonID']){
            $seasonID = $row['SeasonID'];
            $perSeasonCount = 0;
        }
        if($perSeasonCount < $maxPerSeason){
        
            
            $playerDetail = new player($row['AssistPlayerID']);
?>
                <tr>
                    <td><?php echo $row['SeasonID']; ?></td>
                    <td>
                        <a href='/playerDetails.php?playerid=<?php echo $playerDetail->getPlayerID(); ?>&position=4'>
                            <?php echo $playerDetail->getFullName(); ?>
                        </a>
                    </td>
                    <td><?php echo $row['Hookups']; ?></td>
                </tr>
<?php
            $perSeasonCount++;
        }
    }
?>
            </tbody>
        </table>
    </div>
</div>


<div class="row">
    <div class="span10">
        <h2>Top 3 Players per season who you assisted</h2>
        <table  class="zebra-striped stats player_stats">
            <thead>
                <tr>
                    <th>Season</th>
                    <th>Player</th>
                    <th>Count</th>
                </tr>
            </thead>
            <tbody>
<?php 
    $perSeasonCount = 0;
    //fill the data
    foreach($goalPlayers as $row) {
        if($seasonID != $row['SeasonID']){
            $seasonID = $row['SeasonID'];
            $perSeasonCount = 0;
        }
        if($perSeasonCount < $maxPerSeason){
            $playerDetail = new player($row['GoalPlayerID']);
?>
                <tr>
                    <td><?php echo $row['SeasonID']; ?></td>
                    <td>
                        <a href='/playerDetails.php?playerid=<?php echo $playerDetail->getPlayerID(); ?>&position=4'>
                            <?php echo $playerDetail->getFullName(); ?>
                        </a>
                    </td>
                    <td><?php echo $row['Hookups']; ?></td>
                </tr>
<?php
            $perSeasonCount++;
        }
    }
?>
            </tbody>
        </table>
    </div>
</div>

