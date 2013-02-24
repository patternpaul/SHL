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
                    <th class="twipsy-head" rel='twipsy' title='Average Goals Per Game'>AGPG</th>
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





