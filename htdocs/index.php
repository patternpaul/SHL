<?php
    require_once(dirname(__FILE__).'/pageComponents/header.php');
    

    //variable declaration
    $postCol = new postCollection();
    $postCol->getLastPosts(5);
    $postCol->load();

    //potential

?>

<div class="span11">
<?php
    for ( $beta = 0; $beta < $postCol->count(); $beta += 1) {
        //the player to output
        $postOut = $postCol->get($beta);
?>
        <div class="row">
            <div class="span10 article">
               
                <div class="postdate"> &nbsp;
                    <div class="month"><?php echo $postOut->getMonth(); ?></div>
                    <div class="day"><?php echo $postOut->getDay(); ?></div>
                    <div class="year"><?php echo $postOut->getYear(); ?></div>
                </div>
                <h2><?php echo $postOut->getTitle(); ?> <small>Posted By: <?php echo $postOut->getPosterObj()->getShortName(); ?></small></h2>
                
                <blockquote>
                    <p>
                        <?php echo $postOut->getContent(); ?>
                    </p>
                    
                </blockquote>
                
            </div>
        </div>    
<?php   
    }
?> 



</div>




<?php
require_once(dirname(__FILE__).'/pageComponents/menu.php');
require_once(dirname(__FILE__).'/pageComponents/footer.php');
?>