<?php
    require_once(dirname(__FILE__).'/pageComponents/header.php');
    

    //variable declaration
    $postGet;


    $get_postID = default_get("postid", 0);


    //default post variable
    $postGet = new post($get_postID);
    $post_title = $postGet->getTitle();
    $post_dateCreated = $postGet->getDateCreated();
    $post_content = $postGet->getContent();
    $post_poster = $postGet->getPosterObj()->getShortName();


?>

<div class="span11">
        <?php
           if(hasAccessLevel(1)){
                echo "<div style='float: right;'>[<a href=\"/addEditPost.php?postid=".$get_postID."\">EDIT</a>]</div>";
            }
        ?>
    <h2>
        <?php echo $post_title; ?>

    </h2>
    <div class="postDelimiter">
        <?php echo $post_content; ?>

        <br/><br/>
        <small>
            Posted By: <?php echo $post_poster; ?> on <?php echo $post_dateCreated; ?></small>
    </div>
</div>


<?php
require_once(dirname(__FILE__).'/pageComponents/menu.php');
require_once(dirname(__FILE__).'/pageComponents/footer.php');
?>
