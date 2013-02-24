


<?php
    require_once(dirname(__FILE__).'/pageComponents/header.php');


    //variable declaration
    $get_gameID = default_get("gameid", 0);
    $post_curStep = default_post("curstep", 1);
    $message = "";
    //step two
    $teamWhiteCount = 8;
    $teamBlackCount = 8;
    //step three
    $teamWhiteGoalCount = 18;
    $teamBlackGoalCount = 18;

    //get the game
    $gameGet = gameAddEditGetGame($get_gameID);
    //get all players
    $playerCol = new playerCollection();
    //load the collection
    $playerCol->load();
    
    //custom error
    $ls_customError = "";




    //check to see if the game was posted
    if(submitted("gameid")){
        //game wizard posted
        if($post_curStep == 1){
            //first step
            $gameGet->setGameDate($_POST["gamedate"]);
            $gameGet->setGameStart($_POST["gamestarthour"] . ":" . $_POST["gamestartminute"] . " " . $_POST["gamestartampm"]);
            $gameGet->setGameEnd($_POST["gameendhour"] . ":" . $_POST["gameendminute"] . " " . $_POST["gameendampm"]);
            $gameGet->setSeasonID($_POST["seasonid"]);
            $gameGet->setGameNum($_POST["gamenumber"]);
            $gameGet->setPlayoff($_POST["gameplayoff"]);

            //validate the entry
            $gameGet->validate();
            
            //get the set message
            $message = $gameGet->getMessage();
            
        }elseif($post_curStep == 2){
            //black == 1
            //goalie = 1


            //create the white team
            $teamCollection = new teamPlayerCollection();
            //create the goalie player
            $teamPlayerCreated = new teamPlayer(0);
            $teamPlayerCreated->c_color = 2;
            $teamPlayerCreated->c_position = 1;
            $teamPlayerCreated->setPlayerID($_POST["gwhite"]);
            //load the object
            $teamPlayerCreated->load();
            //add the goalie
            $teamCollection->add($teamPlayerCreated);
            //go over team white players and add them
            for ( $alpha = 0; $alpha < $teamWhiteCount; $alpha += 1) {
                if($_POST["whitep".$alpha] > 0){
                    //player selected, create player
                    $teamPlayerCreated = new teamPlayer(0);
                    $teamPlayerCreated->c_color = 2;
                    $teamPlayerCreated->c_position = 2;
                    $teamPlayerCreated->setPlayerID($_POST["whitep".$alpha]);
                    //load the object
                    $teamPlayerCreated->load();
                    //add the goalie
                    $teamCollection->add($teamPlayerCreated);
                }
            }
            //add the white team
            $gameGet->setTeamWhite($teamCollection);

            //create the white team
            $teamCollection = new teamPlayerCollection();
            //create the goalie player
            $teamPlayerCreated = new teamPlayer(0);
            $teamPlayerCreated->c_color = 1;
            $teamPlayerCreated->c_position = 1;
            $teamPlayerCreated->setPlayerID($_POST["gblack"]);
            //load the object
            $teamPlayerCreated->load();
            //add the goalie
            $teamCollection->add($teamPlayerCreated);
            //go over team white players and add them
            for ( $alpha = 0; $alpha < $teamBlackCount; $alpha += 1) {
                if($_POST["blackp".$alpha] > 0){
                    //player selected, create player
                    $teamPlayerCreated = new teamPlayer(0);
                    $teamPlayerCreated->c_color = 1;
                    $teamPlayerCreated->c_position = 2;
                    $teamPlayerCreated->setPlayerID($_POST["blackp".$alpha]);
                    //load the object
                    $teamPlayerCreated->load();
                    //add the goalie
                    $teamCollection->add($teamPlayerCreated);
                }
            }
            //add the black team
            $gameGet->setTeamBlack($teamCollection);



        }elseif($post_curStep == 3){
            //PointType = 1 goal

            //loop over white goals
            for ( $alpha = 1; $alpha <= $teamWhiteGoalCount; $alpha += 1) {
                if($_POST["whiteg".$alpha] > 0){
                    //create the point
                    $pointHolder = new point(0);
                    $pointHolder->c_pointType = 1;
                    $pointHolder->c_pointNum = $alpha;
                    //get the player
                    $gameGet->getTeamWhite()->getPlayer($_POST["whiteg".$alpha])->getTeamPlayerPoints()->add($pointHolder);
                }
                if($_POST["whitea".$alpha] > 0){
                    //create the point
                    $pointHolder = new point(0);
                    $pointHolder->c_pointType = 2;
                    $pointHolder->c_pointNum = $alpha;
                    //get the player
                    $gameGet->getTeamWhite()->getPlayer($_POST["whitea".$alpha])->getTeamPlayerPoints()->add($pointHolder);
                }
                if(($_POST["whiteg".$alpha] <= 0) && ($_POST["whitea".$alpha] > 0)){
                    $ls_customError = $ls_customError . "White's point #" . $alpha . " is missing a goal scorer <br />";
                }
            }

            //loop over black goals
            for ( $alpha = 1; $alpha <= $teamBlackGoalCount; $alpha += 1) {
                if($_POST["blackg".$alpha] > 0){
                    //create the point
                    $pointHolder = new point(0);
                    $pointHolder->c_pointType = 1;
                    $pointHolder->c_pointNum = $alpha;
                    //get the player
                    $gameGet->getTeamBlack()->getPlayer($_POST["blackg".$alpha])->getTeamPlayerPoints()->add($pointHolder);
                }
                if($_POST["blacka".$alpha] > 0){
                    //create the point
                    $pointHolder = new point(0);
                    $pointHolder->c_pointType = 2;
                    $pointHolder->c_pointNum = $alpha;
                    //get the player
                    $gameGet->getTeamBlack()->getPlayer($_POST["blacka".$alpha])->getTeamPlayerPoints()->add($pointHolder);
                }
                if(($_POST["blackg".$alpha] <= 0) && ($_POST["blacka".$alpha] > 0)){
                    $ls_customError = $ls_customError . "Black's point #" . $alpha . " is missing a goal scorer <br />";
                }
            }

            if(!submitted("submitprevious") && (trim($ls_customError) == "")){
                if($gameGet->getGameID() > 0){

                    $gameGet->fullGameUpdate();

                }else{
                    $gameGet->fullGameInsert();
                }
            }
        }

        //step move check
        //check if no errors
        if(!$gameGet->hasError() && (trim($ls_customError) == "")){
            //move to the next step
            if(submitted("submitnext") && ($post_curStep < 3)){
                $post_curStep =  $post_curStep + 1;
            }elseif(submitted("submitprevious")){
                $post_curStep = $post_curStep - 1;
            }else{
                $post_curStep = 1;
                $message = $gameGet->getMessage();
                //reset the stored game object
                resetGetGame($get_gameID);
                $gameGet = gameAddEditGetGame($get_gameID);
            }
        }else{
            //get the set message
            $message = $ls_customError . $gameGet->getMessage();
        }
        //reset the message
        $gameGet->resetMessage();

    } //submit check end







?>





<div class="span11">
    <h2>
        <?php headerDisplay($get_gameID, "Add Game", "Edit Game"); ?>
    </h2>
    <form method="post" action="<?php echo formAction(); ?>" name="addeditgameform" id="addeditgameform">
        <input type="hidden" name="gameid" id="gameid" value="<?php echo $get_gameID; ?>" />
        <input type="hidden" name="curstep" id="curstep" value="<?php echo $post_curStep; ?>" />
        <fieldset>
        
            <?php
                //step case
                switch ($post_curStep) {
                    case 1:
                        require_once(dirname(__FILE__).'/pageComponents/GameWizard/stepOne.php');
                        break;
                    case 2:
                        require_once(dirname(__FILE__).'/pageComponents/GameWizard/stepTwo.php');
                        break;
                    case 3:
                        require_once(dirname(__FILE__).'/pageComponents/GameWizard/stepThree.php');
                        break;
                }
            ?>

            
            
            
            <div class="actions">
                <?php if($post_curStep != 1) { ?>
                    <input type="submit" name="submitprevious" class="btn" id="submitprevious" value="Previous" />
                <?php } ?>
                <?php
                    if($post_curStep != 3) {
                        $nextDisplay = "Next";
                    }else{
                        $nextDisplay = "Submit";
                    }
                ?>
                <input type="submit" name="submitnext" id="submitnext" class="btn primary" value="<?php echo $nextDisplay ?>" />
                    
            </div>
        </fieldset>
    </form>
</div>






<?php
    require_once(dirname(__FILE__).'/pageComponents/menu.php');
    require_once(dirname(__FILE__).'/pageComponents/footer.php');
?>