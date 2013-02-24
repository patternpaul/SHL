<table class="zebra-striped stats player_stats">
    <thead>
        <tr>
            <th>Name</th>
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
            $ls_trClass = "";
            //loop over the collection of games
            for ( $alpha = 0; $alpha < $seasonPlayers->count(); $alpha += 1) {
                //the player to output
                $playerObj = $seasonPlayers->get($alpha);
                $playerObj->quickGoalieLoad($url_seasonID, $url_playoff);
                if($playerObj->getQuickGGamesCount() != 0 && $playerObj->getQuickGGamesCount() >= $url_mingames){
                    if($ls_trClass == "odd"){
                        $ls_trClass = "even";
                    }else{
                        $ls_trClass = "odd";
                    }
                    ?>
                    <tr class="<?php echo $ls_trClass; ?>">
                        <td class='name'>
                            <a href='/playerDetails.php?playerid=<?php echo $playerObj->getPlayerID(); ?>&position=1'>
                                <?php echo $playerObj->getShortName(); ?>
                            </a>
                        </td>
                        <td><?php echo $playerObj->getQuickGACount(); ?></td>
                        <td><?php echo $playerObj->getQuickGGamesCount(); ?></td>
                        <td><?php echo $playerObj->getQuickGAA(); ?></td>
                        <td><?php echo $playerObj->getQuickGTotalGameMinutes(); ?></td>
                        <td><?php echo $playerObj->getQuickGGPM(); ?></td>
                        <td><?php echo $playerObj->getQuickGWinCount(); ?></td>
                        <td><?php echo $playerObj->getQuickGLossCount(); ?></td>
                        <td><?php echo $playerObj->getQuickGWinLossCount(); ?></td>
                        <td><?php echo $playerObj->getQuickGoalieWinPercent(); ?></td>
                        <td><?php echo $playerObj->getQuickGShutOutCount(); ?></td>
                        <td><?php echo $playerObj->getQuickGGoalsCount(); ?></td>
                        <td><?php echo $playerObj->getQuickGAssistsCount(); ?></td>
                        <td><?php echo $playerObj->getQuickGPointCount(); ?></td>

                   </tr>
                   <?php 
                }
            }
        ?>
    </tbody>
</table>











