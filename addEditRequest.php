<?php
    require_once(dirname(__FILE__).'/pageComponents/header.php');
    

    //variable declaration
    $requestUpdate;
    $requestGet;
    $message = "";

    $get_requestID = default_get("requestid", 0);


    //check to see if form has been submitted
    if(submitted("submit")){
        //create a player
        $requestUpdate = new request($get_requestID);
        //set the class params
        $requestUpdate->setTitle($_POST["title"]);
        $requestUpdate->setDateCreated($_POST["datecreated"]);
        $requestUpdate->setContent($_POST["requestcontent"]);
        $requestUpdate->setPriority($_POST["priority"]);
        $requestUpdate->setStatus($_POST["status"]);
        $requestUpdate->setPoster($_POST["poster"]);

        //run the exec
        $requestUpdate->exec();

        if(!$requestUpdate->hasError()){
            //no errors
            if($get_requestID == 0){
                //clear out the post
                $_POST = array();
            }
        }


        //return the message
        $message = $requestUpdate->getMessage();

    }



    //default post variable
    $requestGet = new request($get_requestID);
    $post_title = default_post("title", $requestGet->getTitle());
    $post_dateCreated = default_post("datecreated", $requestGet->getDateCreated());
    $post_content = default_post("requestcontent", $requestGet->getContent());
    $post_priority = default_post("priority", $requestGet->getPriority());
    $post_status = default_post("status", $requestGet->getStatus());
    $post_poster = default_post("poster", $requestGet->getPoster());
    $posterName = $requestGet->getPosterObj()->getShortName();

?>

<div class="span11">
    <form method="post" action="<?php echo formAction(); ?>" name="addeditrequestform" id="addeditrequestform">
        <input type="hidden" name="requestid" id="requestid" value="<?php echo $get_requestID; ?>" />
        <fieldset>
            <legend><?php headerDisplay($get_requestID, "Add Request", "Edit Request"); ?></legend>
            <?php if($message != ""){ ?>
                <div class="alert-message error">
                    <p><?php echo $message; ?></p>
                </div>
            <?php } ?>
            <div class="clearfix">
                <label for="title">Title</label>
                <div class="input">
                    <input type="text" class="large" name="title" id="title" value="<?php echo $post_title; ?>" /> <span class="label important">required</span>
                </div>
            </div><!-- /clearfix -->
            <div class="clearfix">
                <label for="datecreated">Date</label>
                <div class="input">
                    <input type="hidden" name="datecreated" id="datecreated" value="<?php echo $post_dateCreated; ?>" />
                    <?php echo $post_dateCreated; ?>
                </div>
            </div><!-- /clearfix -->
            <div class="clearfix">
                <label for="priority">Priority</label>
                <div class="input">

                    <?php
                        //create new select creator
                        $selectHTML = new selectCreator("priority","priority","large","",$post_priority);


                        //loop over the collection of players
                        for ( $beta = 0; $beta < count($requestGet->c_priorityArr); $beta += 1) {
                            //the player to output
                            $val = $requestGet->c_priorityArr[$beta];
                            $selectHTML->addOption($beta, $val);
                        }
                        echo $selectHTML->getSelect();
                    ?>
                </div>
            </div><!-- /clearfix -->
            <div class="clearfix">
                <label for="status">Status</label>
                <div class="input">
                    <?php
                        //create new select creator
                        $selectHTML = new selectCreator("status","status","large","",$post_status);


                        //loop over the collection of players
                        for ( $beta = 0; $beta < count($requestGet->c_statusArr); $beta += 1) {
                            //the player to output
                            $val = $requestGet->c_statusArr[$beta];
                            $selectHTML->addOption($beta, $val);
                        }
                        echo $selectHTML->getSelect();
                    ?>
                </div>
            </div><!-- /clearfix -->
            <div class="clearfix">
                <label for="poster">Poster</label>
                <div class="input">
                    <input type="hidden" name="poster" id="poster" value="<?php echo $post_poster; ?>" />
                    <?php echo $posterName; ?>
                </div>
            </div><!-- /clearfix -->
            <div class="clearfix">
                <label for="requestcontent">Content</label>
                <div class="input">
                    <textarea name="requestcontent" id="requestcontent" class="xlarge" ><?php echo $post_content; ?></textarea>
                </div>
            </div><!-- /clearfix -->
            <div class="actions">
                <input class="btn primary" name="submit" id="submit" value="Submit" type="submit" />
            </div>
        </fieldset>
        
    </form>
</div>


<?php
require_once(dirname(__FILE__).'/pageComponents/menu.php');
require_once(dirname(__FILE__).'/pageComponents/footer.php');
?>
