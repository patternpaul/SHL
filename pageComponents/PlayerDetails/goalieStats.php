<div class="row">
    <div class="span10">
 
        <h2>Regular Season Stats</h2>
        <table  class="zebra-striped stats player_stats">
            <thead>
                <tr>
                    <th>Season</th>
                    <th class="twipsy-head" rel='twipsy' title='Goals Against'>GA</th>
                    <th class="twipsy-head" rel='twipsy' title='Games Played'>GP</th>
                    <th class="twipsy-head" rel='twipsy' title='Goals Against Average'>GAA</th>
                    <th class="twipsy-head" rel='twipsy' title='Minutes Played'>MP</th>
                    <th class="twipsy-head" rel='twipsy' title='Goals Per Minute'>GPM</th>
                    <th class="twipsy-head" rel='twipsy' title='Wins'>W</th>
                    <th class="twipsy-head" rel='twipsy' title='Loss'>L</th>
                    <th class="twipsy-head" rel='twipsy' title='Win/Loss +/-'>+/-</th>
                    <th class="twipsy-head" rel='twipsy' title='Win Percentage'>W%</th>
                    <th class="twipsy-head" rel='twipsy' title='Shut Out'>SO</th>
                    <th class="twipsy-head" rel='twipsy' title='Goals'>G</th>
                    <th class="twipsy-head" rel='twipsy' title='Assists'>A</th>
                    <th class="twipsy-head" rel='twipsy' title='Points'>P</th>
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
                        <td>
                            <a href="/seasonDetails.php?seasonid=<?php echo $currentSeason->c_seasonID; ?>">
                                <?php echo $currentSeason->c_seasonID; ?>
                            </a>
                        </td>
                        <td><?php echo $currentSeason->getGoalsAgainstCount(); ?></td>
                        <td><?php echo $currentSeason->getGameCount(); ?></td>
                        <td><?php echo $currentSeason->getGAA(); ?></td>
                        <td><?php echo $currentSeason->getTotalGameMinutes(); ?></td>
                        <td><?php echo $currentSeason->getGPM(); ?></td>
                        <td><?php echo $currentSeason->getWinCount(); ?></td>
                        <td><?php echo $currentSeason->getLossCount(); ?></td>
                        <td><?php echo $currentSeason->getWinLossCount(); ?></td>
                        <td><?php echo $currentSeason->getWinPercent(); ?></td>
                        <td><?php echo $currentSeason->getShutOutCount(); ?></td>
                        <td><?php echo $currentSeason->getGoalCount(); ?></td>
                        <td><?php echo $currentSeason->getAssistCount(); ?></td>
                        <td><?php echo $currentSeason->getPointCount(); ?></td>
                    </tr>
                <?php    
                    }
                ?>
            </tbody>
            <tfoot>
                <tr>
                    <td>TOTAL</td>
                    <td><?php echo $careerRegularData->getGoalsAgainstCount(); ?></td>
                    <td><?php echo $careerRegularData->getGameCount(); ?></td>
                    <td><?php echo $careerRegularData->getGAA(); ?></td>
                    <td><?php echo $careerRegularData->getTotalGameMinutes(); ?></td>
                    <td><?php echo $careerRegularData->getGPM(); ?></td>
                    <td><?php echo $careerRegularData->getWinCount(); ?></td>
                    <td><?php echo $careerRegularData->getLossCount(); ?></td>
                    <td><?php echo $careerRegularData->getWinLossCount(); ?></td>
                    <td><?php echo $careerRegularData->getWinPercent(); ?></td>
                    <td><?php echo $careerRegularData->getShutOutCount(); ?></td>
                    <td><?php echo $careerRegularData->getGoalCount(); ?></td>
                    <td><?php echo $careerRegularData->getAssistCount(); ?></td>
                    <td><?php echo $careerRegularData->getPointCount(); ?></td>
                </tr>
            </tfoot>
        </table>

    </div>
</div>


<div class="row">
    <div class="span10">
 
        <h2>Playoff Stats</h2>
        <table class="zebra-striped stats player_stats">
            <thead>
                <tr>
                    <th>Season</th>
                    <th class="twipsy-head" rel='twipsy' title='Goals Against'>GA</th>
                    <th class="twipsy-head" rel='twipsy' title='Games Played'>GP</th>
                    <th class="twipsy-head" rel='twipsy' title='Goals Against Average'>GAA</th>
                    <th class="twipsy-head" rel='twipsy' title='Minutes Played'>MP</th>
                    <th class="twipsy-head" rel='twipsy' title='Goals Per Minute'>GPM</th>
                    <th class="twipsy-head" rel='twipsy' title='Wins'>W</th>
                    <th class="twipsy-head" rel='twipsy' title='Loss'>L</th>
                    <th class="twipsy-head" rel='twipsy' title='Win/Loss +/-'>+/-</th>
                    <th class="twipsy-head" rel='twipsy' title='Win Percentage'>W%</th>
                    <th class="twipsy-head" rel='twipsy' title='Shut Out'>SO</th>
                    <th class="twipsy-head" rel='twipsy' title='Goals'>G</th>
                    <th class="twipsy-head" rel='twipsy' title='Assists'>A</th>
                    <th class="twipsy-head" rel='twipsy' title='Points'>P</th>
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
                        <td>
                            <a href="/seasonDetails.php?seasonid=<?php echo $currentSeason->c_seasonID; ?>">
                                <?php echo $currentSeason->c_seasonID; ?>
                            </a>
                        </td>
                        <td><?php echo $currentSeason->getGoalsAgainstCount(); ?></td>
                        <td><?php echo $currentSeason->getGameCount(); ?></td>
                        <td><?php echo $currentSeason->getGAA(); ?></td>
                        <td><?php echo $currentSeason->getTotalGameMinutes(); ?></td>
                        <td><?php echo $currentSeason->getGPM(); ?></td>
                        <td><?php echo $currentSeason->getWinCount(); ?></td>
                        <td><?php echo $currentSeason->getLossCount(); ?></td>
                        <td><?php echo $currentSeason->getWinLossCount(); ?></td>
                        <td><?php echo $currentSeason->getWinPercent(); ?></td>
                        <td><?php echo $currentSeason->getShutOutCount(); ?></td>
                        <td><?php echo $currentSeason->getGoalCount(); ?></td>
                        <td><?php echo $currentSeason->getAssistCount(); ?></td>
                        <td><?php echo $currentSeason->getPointCount(); ?></td>
                    </tr>
                <?php    
                    }
                ?>
            </tbody>
            <tfoot>
                <tr>
                    <td>TOTAL</td>
                    <td><?php echo $careerPlayoffData->getGoalsAgainstCount(); ?></td>
                    <td><?php echo $careerPlayoffData->getGameCount(); ?></td>
                    <td><?php echo $careerPlayoffData->getGAA(); ?></td>
                    <td><?php echo $careerPlayoffData->getTotalGameMinutes(); ?></td>
                    <td><?php echo $careerPlayoffData->getGPM(); ?></td>
                    <td><?php echo $careerPlayoffData->getWinCount(); ?></td>
                    <td><?php echo $careerPlayoffData->getLossCount(); ?></td>
                    <td><?php echo $careerPlayoffData->getWinLossCount(); ?></td>
                    <td><?php echo $careerPlayoffData->getWinPercent(); ?></td>
                    <td><?php echo $careerPlayoffData->getShutOutCount(); ?></td>
                    <td><?php echo $careerPlayoffData->getGoalCount(); ?></td>
                    <td><?php echo $careerPlayoffData->getAssistCount(); ?></td>
                    <td><?php echo $careerPlayoffData->getPointCount(); ?></td>
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
                    <th>G</th>
                    <th>GA</th>
                    <th>+/-</th>
                    <th>MP</th>
                    <th>GPM</th>
                </tr>
            </thead>
            <tbody>

            <?php
                $currentSeason;
                $currentTeamPlayer;
                $currentTeamPlayerColor;
                $currentGame;
                $scoreCollection;
                $gameTime;
                $oddFlip = false;
                $gameCount = 0;
                $gpm = 0;
                $winLoss = "+1";
                //loop over the season stats for display
                for($alpha = 0; $alpha < $careerData->count(); $alpha += 1){
                    $currentSeason = $careerData->get($alpha);
                    for($beta = 0; $beta < $currentSeason->count(); $beta += 1){
                        $currentTeamPlayer = $currentSeason->get($beta);
                        $currentTeamPlayerColor = $currentTeamPlayer->c_color;
                        $currentGame = $currentTeamPlayer->getGame();
                        if($currentTeamPlayerColor != $currentGame->getWinner()){
                            $winLoss = "-1";
                        }else{
                            $winLoss = "+1";
                        }
                        if($currentTeamPlayerColor == 2){
                            $scoreCollection = $currentGame->getTeamScoreCollection(1);
                        }else{
                            $scoreCollection = $currentGame->getTeamScoreCollection(2);
                        }
                        $gameTime = $currentGame->getTotalGameMinutes();
                        $gpm = 0;
                        if($gameTime != 0){
                            $gpm = number_format($scoreCollection->count() / $gameTime, 3, '.', '');
                        }else{
                            
                            $gpm = 0;
                        }
                        $playoffChar = "";
                        if($currentGame->getPlayoff()){
                            $playoffChar = "P";
                        }
                        ?>
                        <tr>
                            <td><a href='/seasonDetails.php?seasonid=<?php echo $currentSeason->c_seasonID; ?>'><?php echo $currentSeason->c_seasonID; ?></a></td>
                            <td>
                                <a href=\"gameDetails.php?gameid=<?php echo $currentTeamPlayer->c_gameID; ?>\">
                                    <?php echo $playoffChar . $currentGame->getGameNum(); ?>
                                </a>
                            </td>
                            <td><?php echo  $scoreCollection->count(); ?></td>
                            <td><?php echo $winLoss; ?></td>
                            <td><?php echo $gameTime; ?></td>
                            <td><?php echo $gpm; ?></td>
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
    SELECT GoalPlayerID, GoalCount,
      (
        SELECT COUNT(Goalie.GameID)
        FROM TeamPlayer AS Goalie 
        INNER JOIN TeamPlayer AS Player ON Goalie.GameID = Player.GameID AND Goalie.Color != Player.Color
        INNER JOIN Game AS GM ON Goalie.GameID = GM.GameID
        WHERE Goalie.Position = 1
        AND   Goalie.PlayerID = "  . db::fmt($url_playerID,1) . "
        AND   Player.PlayerID = GAG.GoalPlayerID
        AND   Player.Position = 2
        AND   GM.Playoff = 0
      ) GameCount
    FROM (
      SELECT  QPD.GoalPlayerID, COUNT(QPD.GoalPlayerID) AS GoalCount
      FROM    QuickPointDetail AS QPD
      INNER JOIN TeamPlayer AS GP ON QPD.GameID = GP.GameID AND GP.Position = 1 AND GP.Color != QPD.Color
      INNER JOIN Game AS QGM ON QPD.GameID = QGM.GameID
      WHERE GP.PlayerID = "  . db::fmt($url_playerID,1) . "
      AND QGM.Playoff = 0
      GROUP BY QPD.GoalPlayerID
      ORDER BY GoalCount DESC
      LIMIT 6
    ) AS GAG



");
    
    
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
                AND   Goalie.PlayerID = "  . db::fmt($url_playerID,1) . "
                AND   Player.Position = 2
                AND   GM.Playoff = 0
              )  AS GameCount

              FROM    QuickPointDetail AS QPD
              INNER JOIN TeamPlayer AS GP ON QPD.GameID = GP.GameID AND GP.Position = 1 AND GP.Color != QPD.Color
              INNER JOIN Game AS QGM ON QPD.GameID = QGM.GameID
              WHERE GP.PlayerID = "  . db::fmt($url_playerID,1) . "
              AND QGM.Playoff = 0
              GROUP BY GP.PlayerID
              ORDER BY GoalCount DESC
            ");
            
            foreach($averageCalc as $Avrgrow) {
                $average = $Avrgrow['GoalCount'] / $Avrgrow['GameCount'];
           
            }
    
    
?>



<div class="row">
    <div class="span10">
        <h2>Goals Against You</h2>
        <table  class="zebra-striped stats matchup_stats">
            <thead>
                <tr>
                    <th class="twipsy-head" rel='twipsy' title='Player'>Player</th>
                    <th class="twipsy-head" rel='twipsy' title='Goal Count'>Goals</th>
                    <th class="twipsy-head" rel='twipsy' title='Games Played Against'>GP</th>
                    <th class="twipsy-head" rel='twipsy' title='Goals Per Game'>GPG</th>
                    <th class="twipsy-head" rel='twipsy' title="Your Average Goals Per Game Per Player">AGPG</th>
                </tr>
            </thead>
            <tbody>
<?php 
    //fill the data
    foreach($goalPlayersAll as $row) {

            $playerDetail = new player($row['GoalPlayerID']);

            
            
?>
                <tr>
                    <td>
                        <a href='/playerDetails.php?playerid=<?php echo $playerDetail->getPlayerID(); ?>&position=2'>
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





