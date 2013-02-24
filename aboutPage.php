<?php
    require_once(dirname(__FILE__).'/pageComponents/header.php');
    

    //variable declaration
    $aboutGet;



    //default post variable
    $aboutGet = new about();
    $aboutGet->loadForDisplay();
    $post_dateCreated = $aboutGet->getDateCreated();
    $post_content = $aboutGet->getContent();
    $post_poster = $aboutGet->getPosterObj()->getShortName();


?>

<div class="span11">
        <?php
           if(hasAccessLevel(1)){
                echo "<div style='float: right;'>[<a href=\"/addEditAbout.php\">EDIT</a>]</div>";
            }
        ?>
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
