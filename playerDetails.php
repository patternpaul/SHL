<?php
    require_once(dirname(__FILE__).'/pageComponents/header.php');
    


    //get URL params
    $url_playerID = default_get("playerid", 36);
    $url_position = default_get("position", 2);

    //variable declaration
    $playerCur = "";
    $goalieCur = "";
    $recordsCur = "";
    $MatchupCur = "";
    $againstGoalieCur = "";
    $playerOutput;

    //create the player
    $playerObj = new player($url_playerID);

    //define the CSS classes
    if($url_position == "1"){
        $goalieCur = "active";
        //get the career data
        $careerData = $playerObj->getGoalieCareerGamesAsPlayer();
        $careerRegularData = $playerObj->getCareerGoalieRegularGames();
        $careerPlayoffData = $playerObj->getCareerGoaliePlayoffGames();
    }elseif($url_position == "2"){
        $playerCur = "active";
        //get the career data
        $careerData = $playerObj->getCareerGames();
        $careerRegularData = $playerObj->getCareerRegularGames();
        $careerPlayoffData = $playerObj->getCareerPlayoffGames();
    }elseif($url_position == "3"){
        $recordsCur = "active";
        //player records
        $playerRecords = new recordCollection();
        $playerRecords->loadPlayerRecords($url_playerID);

    }elseif($url_position == "4"){
        $MatchupCur = "active";
    }else{
        $againstGoalieCur = "active";
    }



    
    //get the collection of players
    $playerCol = new playerCollection();
    //load the collection
    $playerCol->load();


?>

<div class="span11">
    <div class="row">
        <div class="span10">
            <div class="alert-message block-message player">
                <h2><?php echo $playerObj->getFullName(); ?> 
                
                <?php
                    if(hasAccessLevel(1)){
                       echo "<a href=\"/addEditPlayer.php?playerid=".$url_playerID."\">[Edit]</a>" ;
                    }
                ?>
                </h2>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="span5">
            <ul class="media-grid">
                <li>
                    <a href="#">
                        <img class="thumbnail" src="<?php echo $playerObj->getPicture(); ?>" style="width: 100%" alt="">
                    </a>
                </li>
            </ul>
        </div>
        <div class="span5">
            <table class="zebra-striped player-info">
                <thead>
                    
                </thead>
                <tbody>
                    <tr>
                        <td>Email:</td>
                        <td><?php echo $playerObj->getEmail(); ?></td>
                    </tr>
                    <tr>
                        <td>Phone:</td>
                        <td><?php echo $playerObj->getPhoneNumber(); ?></td>
                    </tr>
                    <tr>
                        <td>Height:</td>
                        <td><?php echo $playerObj->getHeight(); ?></td>
                    </tr>
                    <tr>
                        <td>Shoots:</td>
                        <td><?php echo $playerObj->getShoots(); ?></td>
                    </tr>
                    <tr>
                        <td>Fav. Pro Playe:</td>
                        <td><?php echo $playerObj->getPro(); ?></td>
                    </tr>
                    <tr>
                        <td>Fav. Pro Team:</td>
                        <td><?php echo $playerObj->getFavTeam(); ?></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    <div class="row">
        <div class="span10">
            <ul class="tabs">
                <li class="<?php echo $playerCur ?>">
                    <a href="./playerDetails.php?playerid=<?php echo $url_playerID ?>&position=2">Player Stats</a>
                </li>
                <li class="<?php echo $goalieCur ?>">
                    <a href="./playerDetails.php?playerid=<?php echo $url_playerID ?>&position=1">Goalie Stats</a>
                </li>
                <li class="<?php echo $recordsCur ?>">
                    <a href="./playerDetails.php?playerid=<?php echo $url_playerID ?>&position=3">Player Records</a>
                </li>             
            </ul>
        </div>
    </div>
    

    <?php
        //step case
        switch ($url_position) {
            case 1:
                require_once(dirname(__FILE__).'/pageComponents/PlayerDetails/goalieStats.php');
                break;
            case 2:
                require_once(dirname(__FILE__).'/pageComponents/PlayerDetails/playerStats.php');
                break;
            case 3:
                require_once(dirname(__FILE__).'/pageComponents/PlayerDetails/playerRecords.php');
                break;
            case 4:
                require_once(dirname(__FILE__).'/pageComponents/PlayerDetails/playerPointTeam.php');
                break;
            case 5:
                require_once(dirname(__FILE__).'/pageComponents/PlayerDetails/againstGoalie.php');
                break;
        }
    ?>
    
    
    
</div>





<?php
require_once(dirname(__FILE__).'/pageComponents/menu.php');
require_once(dirname(__FILE__).'/pageComponents/footer.php');
?>