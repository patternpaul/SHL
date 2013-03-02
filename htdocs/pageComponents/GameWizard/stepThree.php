<?php
    //get the tam players
    $whiteTeam = $gameGet->getTeamWhite();
    $blackTeam = $gameGet->getTeamBlack();
    
    $whitePoints = $gameGet->getTeamWhiteScores();
    $blackPoints = $gameGet->getTeamBlackScores();

    $foundPoint;
    $foundDefault = 0;
    $foundTeamPlayer;
?>
<?php if($message != ""){ ?>
    <div class="alert-message error">
        <p><?php echo $message; ?></p>
    </div>
<?php } ?>
    <h2>Team White Goals</h2>
    <div class="row">
        <div class="span5">
            <h2>Goal</h2>

            <?php
                for ( $alpha = 1; $alpha <= $teamWhiteGoalCount; $alpha += 1) {
                    //loop over the count of players to display
                    $foundDefault = 0;
                    
                    $foundPoint = $whitePoints->getByID($alpha);
                    if($foundPoint){
                        $foundTeamPlayer = new teamPlayer($foundPoint->c_goal->c_teamPlayerID);
                        $foundDefault = $foundTeamPlayer->getPlayerID();
                    }
                    if (array_key_exists("whiteg".$alpha, $_POST)){
                        $foundDefault = $_POST["whiteg".$alpha];
                    }
                    
                    
                    $selectHTML = new selectCreator("whiteg".$alpha,"whiteg".$alpha,"","width: 200px",$foundDefault);
                    //loop over the collection of players
                    for ( $beta = 0; $beta < $whiteTeam->count(); $beta += 1) {
                        //the player to output
                        $teamPlayer = $whiteTeam->get($beta);
                        $selectHTML->addOption($teamPlayer->getPlayerID(), $teamPlayer->getFullName());
                    }
                    echo $alpha . ": " . $selectHTML->getSelect() . "<br />";
                }
            ?>
        </div>

        <div class="span5">
            <h2>Assist</h2>
            <?php
                for ( $alpha = 1; $alpha <= $teamWhiteGoalCount; $alpha += 1) {
                    //loop over the count of players to display
                    //loop over the count of players to display
                    $foundDefault = 0;
                    $foundPoint = $whitePoints->getByID($alpha);
                    if($foundPoint){
                        if($foundPoint->c_assist){
                            $foundTeamPlayer = new teamPlayer($foundPoint->c_assist->c_teamPlayerID);
                            $foundDefault = $foundTeamPlayer->getPlayerID();
                        }
                    }
                    if (array_key_exists("whitea".$alpha, $_POST)){
                        $foundDefault = $_POST["whitea".$alpha];
                    }
                    
                    

                    $selectHTML = new selectCreator("whitea".$alpha,"whitea".$alpha,"","width: 200px",$foundDefault);
                    //loop over the collection of players
                    for ( $beta = 0; $beta < $whiteTeam->count(); $beta += 1) {
                        //the player to output
                        $teamPlayer = $whiteTeam->get($beta);
                        $selectHTML->addOption($teamPlayer->getPlayerID(), $teamPlayer->getFullName());
                    }
                    echo $selectHTML->getSelect() . "<br />";
                }
            ?>
        </div>

    </div>

    <div id="breakPoint">&nbsp;</div>


    <h2>Team Black Goals</h2>
    <div class="row">
        <div class="span5">
            <h2>Goal</h2>

            <?php
                for ( $alpha = 1; $alpha <= $teamBlackGoalCount; $alpha += 1) {
                    //loop over the count of players to display
                    $foundDefault = 0;
                    $foundPoint = $blackPoints->getByID($alpha);
                    if($foundPoint){
                        $foundTeamPlayer = new teamPlayer($foundPoint->c_goal->c_teamPlayerID);
                        $foundDefault = $foundTeamPlayer->getPlayerID();
                    }
                    
                    if (array_key_exists("blackg".$alpha, $_POST)){
                        $foundDefault = $_POST["blackg".$alpha];
                    }

                    $selectHTML = new selectCreator("blackg".$alpha,"blackg".$alpha,"","width: 200px",$foundDefault);
                    //loop over the collection of players
                    for ( $beta = 0; $beta < $blackTeam->count(); $beta += 1) {
                        //the player to output
                        $teamPlayer = $blackTeam->get($beta);
                        $selectHTML->addOption($teamPlayer->getPlayerID(), $teamPlayer->getFullName());
                    }
                    echo $alpha . ": " . $selectHTML->getSelect() . "<br />";
                }
            ?>
        </div>

        <div class="span5">
            <h2>Assist</h2>
            <?php
                for ( $alpha = 1; $alpha <= $teamBlackGoalCount; $alpha += 1) {
                    //loop over the count of players to display
                    $foundDefault = 0;
                    $foundPoint = $blackPoints->getByID($alpha);
                    if($foundPoint){
                        if($foundPoint->c_assist){
                            $foundTeamPlayer = new teamPlayer($foundPoint->c_assist->c_teamPlayerID);
                            $foundDefault = $foundTeamPlayer->getPlayerID();
                        }
                    }
                    
                    if (array_key_exists("blacka".$alpha, $_POST)){
                        $foundDefault = $_POST["blacka".$alpha];
                    }

                    $selectHTML = new selectCreator("blacka".$alpha,"blacka".$alpha,"","width: 200px",$foundDefault);
                    //loop over the collection of players
                    for ( $beta = 0; $beta < $blackTeam->count(); $beta += 1) {
                        //the player to output
                        $teamPlayer = $blackTeam->get($beta);
                        $selectHTML->addOption($teamPlayer->getPlayerID(), $teamPlayer->getFullName());
                    }
                    echo $selectHTML->getSelect() . "<br />";
                }
            ?>
        </div>

    </div>

    <div id="breakPoint">&nbsp;</div>