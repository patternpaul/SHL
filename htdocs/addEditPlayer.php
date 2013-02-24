<?php
    require_once(dirname(__FILE__).'/pageComponents/header.php');
    

    //variable declaration
    $playerUpdate;
    $playerGet;
    $message = "";
    $get_playedID = default_get("playerid", 0);

    
    //check to see if form has been submitted
    if(submitted("submit")){
        //create a player
        $playerUpdate = new user();
        $playerUpdate->setPlayerID($get_playedID);
        $playerUpdate->load();
        
        
        
        
        
        
        
        $fileUpload = new fileUpload();
        $fileUpload->uploadFile($_FILES['image'], "playerimg/");
        
        $message = $fileUpload->getMessage();
        
        
        if(!$fileUpload->hasError()){
        
        
        //set the class params
        $playerUpdate->setFirstName($_POST["firstname"]);
        $playerUpdate->setLastName($_POST["lastname"]);
        $playerUpdate->setEmail($_POST["email"]);
        $playerUpdate->setPhoneNumber($_POST["phonenumb"]);
        $playerUpdate->setUserName($_POST["username"]);
        $playerUpdate->setPassword($_POST["password"]);
        $playerUpdate->setPasswordConfirm($_POST["passwordconfirm"]);

        $playerUpdate->setHeight($_POST["pheight"]);
        if($_POST["shoots"] != "0"){
            $playerUpdate->setShoots($_POST["shoots"]);
        }else{
            $playerUpdate->setShoots("");
        }
        
        $playerUpdate->setPro($_POST["proplayer"]);
        $playerUpdate->setFavTeam($_POST["proteam"]);
        if(trim($fileUpload->actualFile) != ""){
            $playerUpdate->setPicture($fileUpload->actualFile);
        }
        //run the exec
        $playerUpdate->exec();

        if(!$playerUpdate->hasError()){
            //no errors
            if($get_playedID == 0){
                //clear out the post
                $_POST = array();
            }
        }
        
        
        //return the message
        $message = $playerUpdate->getMessage();
        }
        
    }

    
    //default post variable
    $playerGet = new user();
    $playerGet->setPlayerID($get_playedID);
    $playerGet->load();
    $post_firstName = default_post("firstname", $playerGet->getFirstName());
    $post_lastName = default_post("lastname", $playerGet->getLastName());
    $post_email = default_post("email", $playerGet->getEmail());
    $post_phoneNumb = default_post("phonenumb", $playerGet->getPhoneNumber());
    $post_username = default_post("username", $playerGet->getUserName());
    $post_pheight  = default_post("pheight", $playerGet->getHeight());
    $post_shoots = default_post("shoots", $playerGet->getShoots()); 
    $post_proPlayer  = default_post("proplayer", $playerGet->getPro());
    $post_proTeam = default_post("proteam", $playerGet->getFavTeam());
    $player_img = $playerGet->getPicture();
    
    $post_password = "";
    $post_passwordconfirm = "";

?>
<script type="text/javascript">
function editPhoto(){
    document.getElementById("picbox").style.display = 'none';
    document.getElementById("image").style.display = '';
}

</script>
<div class="span11">

    
    <form method="post" action="<?php echo formAction(); ?>" enctype="multipart/form-data" name="addeditplayerform" id="addeditplayerform">
        <input type="hidden" name="playerid" id="playerid" value="<?php echo $get_playedID; ?>" />
        <fieldset>
            <legend><?php headerDisplay($get_playedID, "Add Player", "Edit Player"); ?></legend>
            <?php if($message != ""){ ?>
                <div class="alert-message error">
                    <p><?php echo $message; ?></p>
                </div>
            <?php } ?>
            
            
            <div class="clearfix">
                <label for="firstname">First Name</label>
                <div class="input">
                    <input type="text" class="large" name="firstname" id="firstname" value="<?php echo $post_firstName; ?>" /> <span class="label important">required</span>
                </div>
            </div><!-- /clearfix -->
            <div class="clearfix">
                <label for="lastname">Last Name</label>
                <div class="input">
                    <input type="text" class="large" name="lastname" id="lastname" value="<?php echo $post_lastName; ?>" /> <span class="label important">required</span>
                </div>
            </div><!-- /clearfix -->
            <div class="clearfix">
                <label for="email">Email</label>
                <div class="input">
                    <input type="text" class="large" name="email" id="email" value="<?php echo $post_email; ?>" />
                </div>
            </div><!-- /clearfix -->
            <div class="clearfix">
                <label for="phonenumb">Phone Number</label>
                <div class="input">
                    <input type="text" class="large" name="phonenumb" id="phonenumb" value="<?php echo $post_phoneNumb; ?>" />
                </div>
            </div><!-- /clearfix -->
            <div class="clearfix">
                <label for="pheight">Height</label>
                <div class="input">
                    <input type="text" class="large" name="pheight" id="pheight" value="<?php echo $post_pheight; ?>" />
                </div>
            </div><!-- /clearfix -->
            <div class="clearfix">
                <label for="shoots">Shoots</label>
                <div class="input">
                    <?php
                        $selectHTML = new selectCreator("shoots","shoots","large","",$post_shoots);
                        //create the ampm field
                        $selectHTML->addOption("Left", "Left");
                        $selectHTML->addOption("Right", "Right");
                        echo $selectHTML->getSelect();
                    ?>
                </div>
            </div><!-- /clearfix -->
            <div class="clearfix">
                <label for="proplayer">Favorite Pro Player</label>
                <div class="input">
                    <input type="text" class="large" name="proplayer" id="proplayer" value="<?php echo $post_proPlayer; ?>" />
                </div>
            </div><!-- /clearfix -->
            <div class="clearfix">
                <label for="proteam">Favorite Pro Team</label>
                <div class="input">
                    <input type="text" class="large" name="proteam" id="proteam" value="<?php echo $post_proTeam; ?>" />
                </div>
            </div><!-- /clearfix -->
            
            <div class="clearfix">
                <label for="username">Username</label>
                <div class="input">
                    <input type="text" class="large" name="username" id="username" value="<?php echo $post_username; ?>" />
                </div>
            </div><!-- /clearfix -->
            <div class="clearfix">
                <label for="password">Password</label>
                <div class="input">
                    <input type="password" class="large" name="password" id="password" value="<?php echo $post_password; ?>" />
                </div>
            </div><!-- /clearfix -->
            <div class="clearfix">
                <label for="passwordconfirm">Confirm Password:</label>
                <div class="input">
                    <input type="password" class="large" name="passwordconfirm" id="passwordconfirm" value="<?php echo $post_passwordconfirm; ?>" />
                </div>
            </div><!-- /clearfix -->
            <div class="clearfix">
                <label for="image">Player Image</label>
                <div class="input">
                    <input type="file" class="input-file" name="image" id="image" style="display: none;" value="<?php echo $player_img; ?>" />
                    <div id="picbox" name="picbox">
                        <img id="pimg" name="pimg" src="<?php echo $player_img; ?>" width="150"  alt="" />
                        <input type="button" class="btn" value="Edit" onclick="javascript: editPhoto()" />
                    </div>
                </div>
            </div><!-- /clearfix -->
            
            
            <div class="actions">
                <input class="btn primary" name="submit" id="submit" value="Save changes" type="submit" />
            </div>
        </fieldset>
       
    </form>
</div>


<?php
require_once(dirname(__FILE__).'/pageComponents/menu.php');
require_once(dirname(__FILE__).'/pageComponents/footer.php');
?>
