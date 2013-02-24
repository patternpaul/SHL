<?php

    $post_gamedate = default_post("gamedate", $gameGet->getGameDate());
    $post_gamestarthour = default_post("gamestarthour", $gameGet->getGameStartHour());
    $post_gamestartminute = default_post("gamestartminute", $gameGet->getGameStartMinute());
    $post_gamestartampm = default_post("gamestartampm", $gameGet->getGameStartAMPM());
    $post_gameend = default_post("gameend", $gameGet->getGameEnd());
    $post_gameendhour = default_post("gameendhour", $gameGet->getGameEndHour());
    $post_gameendminute = default_post("gameendminute", $gameGet->getGameEndMinute());
    $post_gameendampm = default_post("gameendampm", $gameGet->getGameEndAMPM());
    $post_seasonid = default_post("seasonid", $gameGet->getSeasonID());
    $post_gamenumber = default_post("gamenumber", $gameGet->getGameNum());
    $post_gameplayoff = default_post("gameplayoff", $gameGet->getPlayoff());
    $maxseason = $gameGet->getMaxSeason() + 1;
?>

<script>
    $(function() {
            $( "#gamedate" ).datepicker({ dateFormat: 'yy-mm-dd' });
            //register event
            $('#gameplayoff').change(function() {
                playoffChange();
            });

    });




    //script for select modification
    function playoffChange(){
        playoff = $('#gamenumber').val();
        if(playoff == "1"){
            playoffGameSelection(true);
        }else{
            playoffGameSelection(false);
        }
    }

    //script for select modification
    function playoffGameSelection(passedBool){
        gameCount = 50;

        if(passedBool){
            gameCount = 7;
        }else{
            gameCount = 50;
        }
        //clear out the select list
        $('#gamenumber').find('option').remove();
        //fill up select
        for(var i = 1; i <= gameCount; i++)
        {
            $('#gamenumber').append('<option value="'+i+'">'+i+'</option')
        }
    }

</script>

<legend>Enter Game Information</legend>
<?php if($message != ""){ ?>
    <div class="alert-message error">
        <p><?php echo $message; ?></p>
    </div>
<?php } ?>
<div class="clearfix">
    <label for="gamedate">Game Date</label>
    <div class="input">
        <input type="text" class="large" name="gamedate" id="gamedate" value="<?php echo $post_gamedate; ?>" />
    </div>
</div><!-- /clearfix -->
<div class="clearfix">
    <label for="gamestarthour">Start</label>
    <div class="input">
        <?php
            $selectHTML = new selectCreator("gamestarthour","gamestarthour","mini","",$post_gamestarthour);
            //create the hour field
            for ( $beta = 1; $beta <= 12; $beta += 1) {
                $selectHTML->addOption($beta, $beta);
            }
            echo $selectHTML->getSelect() . ":";
        ?>
        <?php
            $selectHTML = new selectCreator("gamestartminute","gamestartminute","mini","",$post_gamestartminute);
            //create the minute field
            for ( $beta = 0; $beta <= 59; $beta += 1) {
                if($beta < 10){
                    $selectHTML->addOption("0" . $beta, "0" . $beta);
                }else{
                    $selectHTML->addOption($beta, $beta);
                }
            }
            echo $selectHTML->getSelect() . " ";
        ?>
        <?php
            $selectHTML = new selectCreator("gamestartampm","gamestartampm","mini","",$post_gamestartampm);
            //create the ampm field
            $selectHTML->addOption("AM", "AM");
            $selectHTML->addOption("PM", "PM");
            echo $selectHTML->getSelect();
        ?>
    </div>
</div><!-- /clearfix -->
<div class="clearfix">
    <label for="gamestarthour">Start</label>
    <div class="input">
        <?php
            $selectHTML = new selectCreator("gameendhour","gameendhour","mini","",$post_gameendhour);
            //create the hour field
            for ( $beta = 1; $beta <= 12; $beta += 1) {
                $selectHTML->addOption($beta, $beta);
            }
            echo $selectHTML->getSelect() . ":";
        ?>
        <?php
            $selectHTML = new selectCreator("gameendminute","gameendminute","mini","",$post_gameendminute);
            //create the minute field
            for ( $beta = 0; $beta <= 59; $beta += 1) {
                if($beta < 10){
                    $selectHTML->addOption("0" . $beta, "0" . $beta);
                }else{
                    $selectHTML->addOption($beta, $beta);
                }
            }
            echo $selectHTML->getSelect() . " ";
        ?>
        <?php
            $selectHTML = new selectCreator("gameendampm","gameendampm","mini","",$post_gameendampm);
            //create the ampm field
            $selectHTML->addOption("AM", "AM");
            $selectHTML->addOption("PM", "PM");
            echo $selectHTML->getSelect();
        ?>
    </div>
</div><!-- /clearfix -->

<div class="clearfix">
    <label for="gameplayoff">Playoff</label>
    <div class="input">
        <?php
            $selectHTML = new selectCreator("gameplayoff","gameplayoff","large","",$post_gameplayoff);
            $selectHTML->addOption("0", "No");
            $selectHTML->addOption("1", "Yes");
            echo $selectHTML->getSelect();
        ?>
    </div>
</div><!-- /clearfix -->

<div class="clearfix">
    <label for="seasonid">Season ID</label>
    <div class="input">
        <?php
            $selectHTML = new selectCreator("seasonid","seasonid","large","",$post_seasonid);
            for ( $beta = 1; $beta <= $maxseason; $beta += 1) {
                $selectHTML->addOption($beta, $beta);
            }
            echo $selectHTML->getSelect();
        ?>
    </div>
</div><!-- /clearfix -->
<div class="clearfix">
    <label for="gamenumber">Game Number</label>
    <div class="input">
        <?php
            $selectHTML = new selectCreator("gamenumber","gamenumber","large","",$post_gamenumber);
            //create the game number field
            $maxgamenum = 50;
            if($post_gameplayoff == 1){
                //playoff game, only goes to game 7
                $maxgamenum = 7;
            }
            for ( $beta = 1; $beta <= $maxgamenum; $beta += 1) {
                $selectHTML->addOption($beta, $beta);
            }
            echo $selectHTML->getSelect();
        ?>
    </div>
</div><!-- /clearfix -->





    
