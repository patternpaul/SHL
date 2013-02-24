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

