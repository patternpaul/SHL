<table class="zebra-striped stats player_stats">
    <thead>
        <tr>
            <th>Name</th>
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
            $ls_trClass = "";
            //loop over the collection of games
            for ( $alpha = 0; $alpha < $seasonPlayers->count(); $alpha += 1) {
                //the player to output
                $playerObj = $seasonPlayers->get($alpha);
                $playerObj->quickLoad($url_seasonID, $url_playoff);
                if($playerObj->getQuickGameCount() != 0 && $playerObj->getQuickGameCount() >= $url_mingames){

                    ?>
                    <tr>
                        <td class='name'>
                            <a href='/playerDetails.php?playerid=<?php echo $playerObj->getPlayerID(); ?>'>
                                <?php echo $playerObj->getShortName(); ?>
                            </a>
                        </td>
                        <td><?php echo $playerObj->getQuickGoalCount(); ?></td>
                        <td><?php echo $playerObj->getQuickAssistCount(); ?></td>
                        <td><?php echo $playerObj->getQuickPointCount(); ?></td>
                        <td><?php echo $playerObj->getQuickGameCount(); ?></td>
                        <td><?php echo $playerObj->getQuickGPG(); ?></td>
                        <td><?php echo $playerObj->getQuickAPG(); ?></td>
                        <td><?php echo $playerObj->getQuickPPG(); ?></td>
                        <td><?php echo $playerObj->getQuickTeamPointPercent(); ?></td>
                        <td><?php echo $playerObj->getQuickGWGCount(); ?></td>
                        <td><?php echo $playerObj->getQuickWinCount(); ?></td>
                        <td><?php echo $playerObj->getQuickLossCount(); ?></td>
                        <td><?php echo $playerObj->getQuickWinLoss(); ?></td>
                        <td><?php echo $playerObj->getQuickWinPercent(); ?></td>
                    </tr>
                   <?php 
                }
            }
        ?>
    </tbody>
</table>






