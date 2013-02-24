

        <div class="span3 right-content">
          	<h3>Player Leaders</h3>
                
                
                
            <?php
                $curRecords = new records();
                $curGoalLeaders = $curRecords->getCurrentSeasonGoalLeaders();
                $curAssistLeaders = $curRecords->getCurrentSeasonAssistLeaders();
                $curPointLeaders = $curRecords->getCurrentSeasonPointLeaders();

            ?>

          	<table class="zebra-striped stats menu_stats">
                        <thead></thead>
          		<tbody>
                            <?php
                                //goals
                                for ( $alpha = 0; $alpha < $curGoalLeaders->count(); $alpha += 1) {
                                    $curPlayer = $curGoalLeaders->get($alpha);
                                    $curPlayer->quickLoad(game::getMaxSeason(), 0);
                                    echo "<tr><td>G</td><td class='name'><a href='/playerDetails.php?playerid=" . $curPlayer->getPlayerID() ."'>" . $curPlayer->getShortName() . "</a></td><td class='stat-num'>" . $curPlayer->getQuickGoalCount() . "</td></tr>";
                                }
                            ?>
                            <?php
                                //assists
                                for ( $alpha = 0; $alpha < $curAssistLeaders->count(); $alpha += 1) {
                                    $curPlayer = $curAssistLeaders->get($alpha);
                                    $curPlayer->quickLoad(game::getMaxSeason(), 0);
                                    echo "<tr><td>A</td><td class='name'><a href='/playerDetails.php?playerid=" . $curPlayer->getPlayerID() ."'>" . $curPlayer->getShortName() . "</a></td><td class='stat-num'>" . $curPlayer->getQuickAssistCount() . "</td></tr>";
                                }
                            ?>
                            <?php
                                //points
                                for ( $alpha = 0; $alpha < $curPointLeaders->count(); $alpha += 1) {
                                    $curPlayer = $curPointLeaders->get($alpha);
                                    $curPlayer->quickLoad(game::getMaxSeason(), 0);
                                    echo "<tr><td>P</td><td class='name'><a href='/playerDetails.php?playerid=" . $curPlayer->getPlayerID() ."'>" . $curPlayer->getShortName() . "</a></td><td class='stat-num'>" . $curPlayer->getQuickPointCount() . "</td></tr>";
                                }
                            ?>
          		</tbody>
          	</table>
                
                <h3>Player Hot Streak</h3>
                <table class="zebra-striped stats menu_stats">
                        <thead></thead>
                        <tbody>
                            <?php
                                $curGoalieHot = $curRecords->getCurrentRegSeasonPlayerHotStreak();
                                
                                for ( $alpha = 0; $alpha < count($curGoalieHot); $alpha += 1) {
                                    $curAr = $curGoalieHot[$alpha];
                                    $curPlayer = new player($curAr[0]);
                                    echo "<tr><td class='name'><a href='/playerDetails.php?playerid=" . $curPlayer->getPlayerID() ."'>" . $curPlayer->getShortName() . "</a></td><td class='stat-num'>" . $curAr[1] . " wins</td></tr>";
                                }
                            ?>
                        </tbody>
                </table>
                
          	<h3>Goalie Leaders</h3>
          	<table class="zebra-striped stats menu_stats">
                        <thead></thead>
          		<tbody>
                            <?php
                                $curGoalieGPM = $curRecords->getCurrentBestGoalieGPM();
                                
                                for ( $alpha = 0; $alpha < $curGoalieGPM->count(); $alpha += 1) {
                                    $curPlayer = $curGoalieGPM->get($alpha);
                                    $curPlayer->quickGoalieLoad(game::getMaxSeason(), 0);
                                    echo "<tr><td>GPM</td><td class='name'><a href='/playerDetails.php?playerid=" . $curPlayer->getPlayerID() ."&position=1'>" . $curPlayer->getShortName() . "</a></td><td class='stat-num'>" . $curPlayer->getQuickGGPM() . "</td></tr>";
                                }
                            ?>
                            <?php
                                $curGoalieGAA = $curRecords->getCurrentBestGoalieGAA();
                                for ( $alpha = 0; $alpha < $curGoalieGAA->count(); $alpha += 1) {
                                    $curPlayer = $curGoalieGAA->get($alpha);
                                    $curPlayer->quickGoalieLoad(game::getMaxSeason(), 0);
                                    echo "<tr><td>GAA</td><td class='name'><a href='/playerDetails.php?playerid=" . $curPlayer->getPlayerID() ."&position=1'>" . $curPlayer->getShortName() . "</a></td><td class='stat-num'>" . $curPlayer->getQuickGAA() . "</td></tr>";
                                }
                            ?>
          		</tbody>
          	</table>

                <h3>Goalie Hot Streak</h3>
                <table class="zebra-striped stats menu_stats">
                        <thead></thead>
                        <tbody>
                            <?php
                                $curGoalieHot = $curRecords->getCurrentRegSeasonGoalieHotStreak();
                                
                                for ( $alpha = 0; $alpha < count($curGoalieHot); $alpha += 1) {
                                    $curAr = $curGoalieHot[$alpha];
                                    $curPlayer = new player($curAr[0]);
                                    echo "<tr><td class='name'><a href='/playerDetails.php?playerid=" . $curPlayer->getPlayerID() ."&position=1'>" . $curPlayer->getShortName() . "</a></td><td class='stat-num'>" . $curAr[1] . " wins</td></tr>";
                                }
                            ?>
                        </tbody>
                </table>
                
                
                
                
                
                
                
                

                

          	<h3>Recent Games</h3>
          	<table class="zebra-striped stats menu_stats">
          		<thead>
          			<tr>
          				<th>#</th>
          				<th>White</th>
          				<th>Colored</th>
          			</tr>
          		</thead>
          		<tbody>
                            
                            <?php
                                $lastGames = new gameCollection();
                                //get last few games
                                $lastGames->getLastGames(5);
                                //load the collection
                                $lastGames->load();

                                //loop over games for display
                                $gameToDisplay;

                                 for ( $alpha = 0; $alpha < $lastGames->count(); $alpha += 1) {
                                      //get the game
                                      $gameToDisplay = $lastGames->get($alpha);
                                      //display
                                    echo "<tr><td>";
                                    if($gameToDisplay->getPlayoff() != 1){
                                        echo "G";
                                    }else{
                                        echo "PG";
                                    }
                                    echo "<a href='/gameDetails.php?gameid=" . $gameToDisplay->getGameID() . "'>" . $gameToDisplay->getGameNum() . "</a></td>";

                                    echo "<td>" . $gameToDisplay->getTeamWhiteScores()->count() . "</td>";
                                    echo "<td>" . $gameToDisplay->getTeamBlackScores()->count() . "</td>";
                                    echo "</tr>";
                                }

                            ?>                

          		</tbody>
          	</table>


<div id="fb-root"></div>
<script>(function(d, s, id) {
  var js, fjs = d.getElementsByTagName(s)[0];
  if (d.getElementById(id)) return;
  js = d.createElement(s); js.id = id;
  js.src = "//connect.facebook.net/en_US/all.js#xfbml=1&appId=262911533729483";
  fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));</script>
<div class="fb-like" data-href="http://www.facebook.com/apps/application.php?id=262911533729483" data-send="true" data-layout="button_count" data-width="450" data-show-faces="false"></div>





          </div>







