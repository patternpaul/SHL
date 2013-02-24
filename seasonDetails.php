<?php
    require_once(dirname(__FILE__).'/pageComponents/header.php');
    


    //get URL params
    $url_seasonID = default_get("seasonid", game::getMaxSeason());

    if($url_seasonID == 0){
        $url_seasonID = game::getMaxSeason();
    }

    //variable declaration
    $seasonGames = new gameCollection();
    $seasonGames->getSpecificSeason($url_seasonID);
    $seasonGames->load();
?>



<div class="span11">
    <div class="row">
        <div class="span10">
            <form>
                Season:
                <?php
                    //create new select creator
                    $selectHTML = new selectCreator("seasonid","seasonid","","",0);


                    //loop over the collection of players
                    for ( $beta = 1; $beta <= game::getMaxSeason(); $beta += 1) {
                        //the player to output
                        $selectHTML->addOption($beta, $beta);
                    }
                    echo $selectHTML->getSelect();
                ?>
                <input type="submit" value="Submit">
            </form>
        </div>
    </div>
    <div class="row">
        <div class="span10">
            <div class="alert-message block-message player">
                <h2>
                    <?php
                        if($url_seasonID > 0){
                            echo "Season " . $url_seasonID;
                        }else{
                            echo "All Seasons";
                        }
                    ?>
                <h2>
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="span10">
            <table class="zebra-striped stats">
                <thead>
                    <tr>
                        <th>Season</th>
                        <th>Game</th>
                        <th>Type</th>
                        <th>White</th>
                        <th>Colored</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        $oddFlip = true;
                        $playoffInd = "YES";
                        //loop over the collection of games
                        for ( $alpha = 0; $alpha < $seasonGames->count(); $alpha += 1) {
                            //the player to output
                            $gameObj = $seasonGames->get($alpha);

                            if($gameObj->getPlayoff() == 1){
                               $playoffInd = "Playoff";

                            }else{
                                $playoffInd = "Regular";
                            }
                            ?>
                            <tr>
                                <td><a href='/playerList.php?seasonid=<?php echo $gameObj->getSeasonID(); ?>'><?php echo $gameObj->getSeasonID(); ?></a></td>
                                <td><a href='/gameDetails.php?gameid=<?php echo $gameObj->getGameID(); ?>'><?php echo $gameObj->getGameNum(); ?></td>
                                <td><?php echo $playoffInd; ?></td>
                                <td><?php echo $gameObj->getQuickWhiteScores(); ?></td>
                                <td><?php echo $gameObj->getQuickBlackScores(); ?></td>
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