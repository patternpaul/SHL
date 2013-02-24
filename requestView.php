<?php
    require_once(dirname(__FILE__).'/pageComponents/header.php');
    

    //variable declaration
    $requestGet;


    $get_requestID = default_get("requestid", 0);


    //default post variable
    $requestGet = new request($get_requestID);
    $post_title = $requestGet->getTitle();
    $post_dateCreated = $requestGet->getDateCreated();
    $post_content = $requestGet->getDisplayContent();
    $post_priority = $requestGet->getPriorityText();
    $post_status = $requestGet->getStatusText();
    $post_poster = $requestGet->getPosterObj()->getShortName();


?>

<div class="span11">
    <h2>
        Request
    </h2>
    <p>
        <?php
           if(hasAccessLevel(1)){
                echo " [<a href=\"/addEditRequest.php?requestid=".$get_requestID."\">EDIT</a>]";
            }
        ?>
    </p>
        <input type="hidden" name="requestid" id="requestid" value="<?php echo $get_requestID; ?>" />
        <table>
            <tr>
                <td>Title:</td>
                <td>
                    <?php echo $post_title; ?>
                </td>
            </tr>
            <tr>
                <td>Date:</td>
                <td>
                    <?php echo $post_dateCreated; ?>
                </td>
            </tr>
            <tr>
                <td>Priority:</td>
                <td>
                    <?php echo $post_priority; ?>
                </td>
            </tr>
            <tr>
                <td>Status:</td>
                <td>
                    <?php echo $post_status; ?>
                </td>
            </tr>
            <tr>
                <td>Poster:</td>
                <td>
                    <?php echo $post_poster; ?>
                </td>
            </tr>
            <tr>
                <td style="padding-top: 8px;" colspan="2">
                    <?php echo $post_content; ?>
                </td>
            </tr>
        </table>
</div>


<?php
require_once(dirname(__FILE__).'/pageComponents/menu.php');
require_once(dirname(__FILE__).'/pageComponents/footer.php');
?>
