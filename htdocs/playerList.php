<?php
    require_once(dirname(__FILE__).'/pageComponents/header.php');
    

    //variable declaration
    $playerCur = "";
    $goalieCur = "";

    
    //get URL params
    $url_seasonID = default_get("seasonid", game::getMaxSeason());
    $url_playoff = default_get("playoff", 0);
    $url_position = default_get("position", 2);
    $url_mingames = default_get("mingames", 0);
    //define the CSS classes
    if($url_position == "1"){
        $goalieCur = "active";
    }else{
        $playerCur = "active";
    }

    //variable declaration
    $seasonPlayers = new playerCollection();
    $seasonPlayers->getSeasonPlayers($url_seasonID);
    $seasonPlayers->load();

?>



<div class="span11">
    <div class="row">
        <div class="span10">
            <form>
                <input type="hidden" id="position" name="position" value="<?php echo $url_position; ?>" />
                <?php
                    //create new select creator
                    $selectHTML = new selectCreator("playoff","playoff","","",$url_playoff);

                    $selectHTML->addOption(-1, "All");
                    $selectHTML->addOption(0, "Regular");
                    $selectHTML->addOption(1, "Playoff");

                    echo $selectHTML->getSelect();
                ?>
                Season:
                <?php
                    //create new select creator
                    $selectHTML = new selectCreator("seasonid","seasonid","","width: 50px;",$url_seasonID);

                    $selectHTML->addOption(0, "All");
                    //loop over the collection of players
                    for ( $beta = 1; $beta <= game::getMaxSeason(); $beta += 1) {
                        //the player to output
                        $selectHTML->addOption($beta, $beta);
                    }
                    echo $selectHTML->getSelect();
                ?>
                Minimum Games:
                <?php
                    //create new select creator
                    $selectHTML = new selectCreator("mingames","mingames","","width: 50px;",$url_mingames);
                    //loop over the collection of players
                    for ( $beta = 0; $beta <= 50; $beta += 1) {
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
            <ul class="tabs">
                <li class="<?php echo $playerCur ?>">
                    <a href="./playerList.php?position=2&playoff=<?php echo $url_playoff; ?>&seasonid=<?php echo $url_seasonID; ?>">Player Stats</a>
                </li>
                <li class="<?php echo $goalieCur ?>">
                    <a href="./playerList.php?position=1&playoff=<?php echo $url_playoff; ?>&seasonid=<?php echo $url_seasonID; ?>">Goalie Stats</a>
                </li>
            </ul>
        </div>
    </div>
    <div class="row">
        <div class="span10">
            <?php
                //step case
                switch ($url_position) {
                    case 1:
                        require_once(dirname(__FILE__).'/pageComponents/PlayerList/goalies.php');
                        break;
                    case 2:
                        require_once(dirname(__FILE__).'/pageComponents/PlayerList/players.php');
                        break;
                }
            ?>
        </div>  
    </div>






</div>


<?php
require_once(dirname(__FILE__).'/pageComponents/menu.php');
require_once(dirname(__FILE__).'/pageComponents/footer.php');
?>