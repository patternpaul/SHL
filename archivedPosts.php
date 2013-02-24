<?php
    require_once(dirname(__FILE__).'/pageComponents/header.php');
    

    //variable declaration
    $postCol = new postCollection();
    $postCol->load();


?>

<div class="span11">
<!--
<h2>Welcome to the SHL site!</h2>

<p>
    <img src="/img/shlLogo.jpg" height="100" width="125" class="left" alt="" />
    Season 1, 2, 3 and part of 4 data is in!<br /> In other hockey related news, Islander's fans DOMINATE!
    <object type="application/x-shockwave-flash" data="http://www.collegehumor.com/moogaloop/moogaloop.swf?clip_id=1793054&fullscreen=1" width="480" height="360" ><param name="allowfullscreen" value="true" /><param name="movie" quality="best" value="http://www.collegehumor.com/moogaloop/moogaloop.swf?clip_id=1793054&fullscreen=1" /></object>
</p>
-->
<?php
    for ( $beta = 0; $beta < $postCol->count(); $beta += 1) {
        //the player to output
        $postOut = $postCol->get($beta);

       if(hasAccessLevel(1)){
            echo "<div style='float: right;'>[<a href=\"/addEditPost.php?postid=".$postOut->getPostID()."\">EDIT</a>]</div>";
        }
        echo '<h2>' . $postOut->getTitle() . '</h2>';
        echo '<div class="postDelimiter">' . $postOut->getContent();
        echo '<br/><small>Posted By: ' . $postOut->getPosterObj()->getShortName() . ' on ' . $postOut->getDateCreated() .'</small></div>';
    }

?>
</div>


<?php
require_once(dirname(__FILE__).'/pageComponents/menu.php');
require_once(dirname(__FILE__).'/pageComponents/footer.php');
?>