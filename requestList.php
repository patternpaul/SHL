<?php
    require_once(dirname(__FILE__).'/pageComponents/header.php');
    

    //variable declaration
    $requestCol = new requestCollection();
    $requestCol->load();

?>


    <script type="text/javascript">

    $(document).ready(function()
        {
            $("#post_table").tablesorter( {sortList: [[1,1], [2,0]]} );
        }
    );
    </script>
<div class="span11">
    <h2>
        Request List
    </h2>
    <p>
        [<a href="/addEditRequest.php">ADD</a>]
    </p>
    <table class="zebra-striped" id="post_table">
        <thead>
            <tr>
                <th>Title</th>
                <th>Status</th>
                <th>Priority</th>
            </tr>
        </thead>
        <tbody>
            <?php
                //loop over the collection of requests
                $oddFlip = false;

                for ( $beta = 0; $beta < $requestCol->count(); $beta += 1) {
                    //the player to output
                    $requestOut = $requestCol->get($beta);
                    ?>
                    <tr>
                        <td><a href="/requestView.php?requestid=<?php echo $requestOut->getRequestID(); ?>" ><?php echo $requestOut->getTitle(); ?></a></td>
                        <td><?php echo $requestOut->getStatusText(); ?></td>
                        <td><?php echo $requestOut->getPriorityText(); ?></td>
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
