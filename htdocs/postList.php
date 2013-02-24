<?php
    require_once(dirname(__FILE__).'/pageComponents/header.php');
    

    //variable declaration
    $postCol = new postCollection();
    $postCol->load();

?>
    <script type="text/javascript">

    $(document).ready(function()
        {
            $("#post_table").tablesorter( {sortList: [[1,1]]} );
        }
    );
    </script>
<div class="span11">
    <h2>
        Post List
    </h2>
    <p>
        [<a href="/addEditPost.php">ADD</a>]
    </p>
    <table class="zebra-striped" id="post_table">
        <thead>
            <tr>
                <th>Title</th>
                <th>Date</th>
                <th>Author</th>
                <th>Edit</th>
            </tr>
        </thead>
        <tbody>
            <?php
                //loop over the collection of requests
                $oddFlip = false;

                for ( $beta = 0; $beta < $postCol->count(); $beta += 1) {
                    //the player to output
                    $postOut = $postCol->get($beta);
                    ?>
                    <tr>
                        <td><a href="/postView.php?postid=<?php echo $postOut->getPostID(); ?>" ><?php echo $postOut->getTitle(); ?></a></td>
                        <td><?php echo $postOut->getDateCreated(); ?></td>
                        <td><?php echo $postOut->getPosterObj()->getShortName(); ?></td>
                        <td><a href="/addEditPost.php?postid=<?php echo $postOut->getPostID(); ?>" >X</a></td>
                    </tr>
                    <?php
                }
            ?>
        </tbody>
    </table>
    
    
</div>


<?php
require_once(dirname(__FILE__).'/pageComponents/menu.php');
require_once(dirname(__FILE__).'/pageComponents/footer.php');
?>
