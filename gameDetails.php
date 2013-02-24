<?php
    require_once(dirname(__FILE__).'/pageComponents/header.php');
    


    //get URL params
    $url_gameID = default_get("gameid", game::getNewestGame());

    //variable declaration
    $gameOutput;
    $currentGame = new game($url_gameID);
    $teamWhite = $currentGame->getTeamWhite();
    $teamBlack = $currentGame->getTeamBlack();
    $whiteScores = $currentGame->getWhiteScores();
    $blackScores = $currentGame->getBlackScores();

    $teamPlayer;


?>



<div class="span11">

	<h2>
            <?php
                if($currentGame->getPlayoff() == 1){
                    echo "Playoff ";
                }
                echo "Game ".$currentGame->getGameNum().", <a href='/seasonDetails.php?seasonid=" . $currentGame->getSeasonID() . "'>Season ".$currentGame->getSeasonID()."</a>";
            ?>

        </h2>
	<?php echo $currentGame->getGameDate(); ?><br />
	<?php echo $currentGame->getGameStart()." To ".$currentGame->getGameEnd(); ?><br />
        <?php
            if(hasAccessLevel(1)){
                echo " [<a href=\"/addEditGame.php?gameid=".$currentGame->getGameID()."\">EDIT</a>]";
            }
        ?>

    
    
    
    
    <div class="row">
        <div class="span5">
            <h2>Team White</h2>
            <?php
                //loop over the collection of players
                for ( $beta = 0; $beta < $teamWhite->count(); $beta += 1) {
                    //the player to output
                    $teamPlayer = $teamWhite->get($beta);
                    
                    if($teamPlayer->c_position == 1){
                        echo "G: ";
                    }else{
                        echo "P: ";
                    }

                    echo "<a href=\"playerDetails.php?playerid=".$teamPlayer->getPlayerID()."&position=".$teamPlayer->c_position."\">".$teamPlayer->getFullName()."</a><br />";
                }
            ?>

        </div>

        <div class="span5">
            <h2>Team Colored</h2>
            <?php
                //loop over the collection of players
                for ( $beta = 0; $beta < $teamBlack->count(); $beta += 1) {
                    //the player to output
                    $teamPlayer = $teamBlack->get($beta);

                    if($teamPlayer->c_position == 1){
                        echo "G: ";
                    }else{
                        echo "P: ";
                    }

                    echo "<a href=\"playerDetails.php?playerid=".$teamPlayer->getPlayerID()."&position=".$teamPlayer->c_position."\">".$teamPlayer->getFullName()."</a><br />";

                }
            ?>
        </div>
        
    </div>



    <div class="row">
        <div class="span5">
            <table class="zebra-striped stats">
                <thead>
                    <tr>
                        <th>P</th>
                        <th>Goal</th>
                        <th>Assist</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                       $oddFlip = false;
                        //loop over the scores for display
                        for($beta = 0; $beta < $whiteScores->count(); $beta += 1){
                            $score = $whiteScores->get($beta);
                            $pointNum = $score->c_pointNum;
                            $goal = $score->c_goal;
                            $goalPlayer = $teamWhite->getByID($goal->c_teamPlayerID);
                            $goalName = $goalPlayer->getShortName();
                            $goalID = $goalPlayer->getPlayerID();
                            $goalPos = $goalPlayer->c_position;
                            $assistName = "&nbsp;";
                            $assistID = "";
                            $assistPos = "";
                            if(isset($score->c_assist)){
                                $assist = $score->c_assist;
                                $assistPlayer = $teamWhite->getByID($assist->c_teamPlayerID);
                                $assistName = $assistPlayer->getShortName();
                                $assistID = $assistPlayer->getPlayerID();
                                $assistPos = $assistPlayer->c_position;
                            }
                            ?>
                            <tr>
                                <td><?php echo $pointNum; ?></td>
                                <td class='game-name'>
                                    <a href="playerDetails.php?playerid=<?php echo $goalID; ?>&position=<?php echo $goalPos; ?>">
                                        <?php echo $goalName; ?>
                                    </a>
                                </td>
                                <td class='game-name'>
                                    <a href="playerDetails.php?playerid=<?php echo $assistID; ?>&position=<?php echo $assistPos; ?>">
                                        <?php echo $assistName; ?>
                                    </a>
                                </td>
                            </tr>
                            <?php
                        }
                        
                    ?>
                </tbody>
            </table>
        </div>

        <div class="span5">
            <table class="zebra-striped stats">
                <thead>
                    <tr>
                        <th>P</th>
                        <th>Goal</th>
                        <th>Assist</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                       $oddFlip = false;
                        //loop over the scores for display
                        for($beta = 0; $beta < $blackScores->count(); $beta += 1){
                            $score = $blackScores->get($beta);
                            $pointNum = $score->c_pointNum;
                            $goal = $score->c_goal;
                            $goalPlayer = $teamBlack->getByID($goal->c_teamPlayerID);
                            $goalName = $goalPlayer->getShortName();
                            $goalID = $goalPlayer->getPlayerID();
                            $goalPos = $goalPlayer->c_position;
                            $assistName = "&nbsp;";
                            $assistID = "";
                            $assistPos = "";
                            if(isset($score->c_assist)){
                                $assist = $score->c_assist;
                                $assistPlayer = $teamBlack->getByID($assist->c_teamPlayerID);
                                $assistName = $assistPlayer->getShortName();
                                $assistID = $assistPlayer->getPlayerID();
                                $assistPos = $assistPlayer->c_position;
                            }
                            ?>
                            <tr>
                                <td><?php echo $pointNum; ?></td>
                                <td class='game-name'>
                                    <a href="playerDetails.php?playerid=<?php echo $goalID; ?>&position=<?php echo $goalPos; ?>">
                                        <?php echo $goalName; ?>
                                    </a>
                                </td>
                                <td class='game-name'>
                                    <a href="playerDetails.php?playerid=<?php echo $assistID; ?>&position=<?php echo $assistPos; ?>">
                                        <?php echo $assistName; ?>
                                    </a>
                                </td>
                            </tr>
                            <?php
                        }
                        
                    ?>
                </tbody>
            </table>    
            
            
            
     
        </div>
    </div>

</div>





<?php
require_once(dirname(__FILE__).'/pageComponents/menu.php');
require_once(dirname(__FILE__).'/pageComponents/footer.php');
?>