<?php

    //get the team collections for step 2
    $teamWhiteCol = $gameGet->getTeamWhite();
    $teamBlackCol = $gameGet->getTeamBlack();
    $tempPlayer;
    
    $teamWhiteArray = array();
    $teamBlackArray = array();
    $whiteGoalie = "";
    $blackGoalie = "";
    $defaultVal = 0;

    //loop over the collections and set defaults
    for ( $beta = 0; $beta < $teamWhiteCol->count(); $beta += 1) {
        $tempPlayer = $teamWhiteCol->get($beta);
        if($tempPlayer->c_position == 2){
            $teamWhiteArray[] = $tempPlayer->getPlayerID();
        }else{
            $whiteGoalie = $tempPlayer->getPlayerID();
        }
    }
    for ( $beta = 0; $beta < $teamBlackCol->count(); $beta += 1) {
        $tempPlayer = $teamBlackCol->get($beta);
        if($tempPlayer->c_position == 2){
            $teamBlackArray[] = $tempPlayer->getPlayerID();
        }else{
            $blackGoalie = $tempPlayer->getPlayerID();
        }
    }

?>

    <h2>Enter the Teams</h2>
    <div class="row">
        <div class="span5">
            <h2>Team White</h2>
            G: 
            <?php
                //create new select creator
                $selectHTML = new selectCreator("gwhite","gwhite","","width: 200px",$whiteGoalie);


                //loop over the collection of players
                for ( $beta = 0; $beta < $playerCol->count(); $beta += 1) {
                    //the player to output
                    $teamPlayer = $playerCol->get($beta);
                    $selectHTML->addOption($teamPlayer->getPlayerID(), $teamPlayer->getFullName());
                }
                echo $selectHTML->getSelect() . "<br />";
            ?>
         

            <?php
                for ( $alpha = 0; $alpha < $teamWhiteCount; $alpha += 1) {
                    //loop over the count of players to display
                    if(isset($teamWhiteArray[$alpha])){
                         $defaultVal = $teamWhiteArray[$alpha];
                    }else{
                        $defaultVal = 0;
                    }

                    $selectHTML = new selectCreator("whitep".$alpha,"whitep".$alpha,"","width: 200px",$defaultVal);
                    //loop over the collection of players
                    for ( $beta = 0; $beta < $playerCol->count(); $beta += 1) {
                        //the player to output
                        $teamPlayer = $playerCol->get($beta);
                        $selectHTML->addOption($teamPlayer->getPlayerID(), $teamPlayer->getFullName());
                    }
                    echo "P: " . $selectHTML->getSelect() . "<br />";
                }
            ?>
        </div>

        <div  class="span5">
            <h2>Team Black</h2>
            G:
            <?php
                //create new select creator
                $selectHTML = new selectCreator("gblack","gblack","","width: 200px",$blackGoalie);


                //loop over the collection of players
                for ( $beta = 0; $beta < $playerCol->count(); $beta += 1) {
                    //the player to output
                    $teamPlayer = $playerCol->get($beta);
                    $selectHTML->addOption($teamPlayer->getPlayerID(), $teamPlayer->getFullName());
                }
                echo $selectHTML->getSelect() . "<br />";
            ?>


            <?php
                for ( $alpha = 0; $alpha < $teamBlackCount; $alpha += 1) {
                    //loop over the count of players to display
                    if(isset($teamBlackArray[$alpha])){
                         $defaultVal = $teamBlackArray[$alpha];
                    }else{
                        $defaultVal = 0;
                    }

                    $selectHTML = new selectCreator("blackp".$alpha,"blackp".$alpha,"","width: 200px",$defaultVal);
                    //loop over the collection of players
                    for ( $beta = 0; $beta < $playerCol->count(); $beta += 1) {
                        //the player to output
                        $teamPlayer = $playerCol->get($beta);
                        $selectHTML->addOption($teamPlayer->getPlayerID(), $teamPlayer->getFullName());
                    }
                    echo "P: " . $selectHTML->getSelect() . "<br />";
                }
            ?>
        </div>

    </div>

    <div id="breakPoint">&nbsp;</div>