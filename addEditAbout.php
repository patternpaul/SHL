<?php
    require_once(dirname(__FILE__).'/pageComponents/header.php');
    

    //variable declaration
    $aboutUpdate;
    $aboutGet;
    $message = "";



    //check to see if form has been submitted
    if(submitted("submit")){
        //create a player
        $aboutUpdate = new about();
        //set the class params
        $aboutUpdate->setDateCreated($_POST["datecreated"]);
        $aboutUpdate->setContent($_POST["requestcontent"]);
        $aboutUpdate->setPoster($_POST["poster"]);

        //run the exec
        $aboutUpdate->exec();

        if(!$aboutUpdate->hasError()){
//            //no errors
//            if($get_postID == 0){
//                //clear out the post
//                $_POST = array();
//            }
        }


        //return the message
        $message = $aboutUpdate->getMessage();

    }



    //default post variable
    $aboutGet = new about();
    $about_dateCreated = default_post("datecreated", $aboutGet->getDateCreated());
    $about_content = default_post("requestcontent", $aboutGet->getContent());
    $about_poster = default_post("poster", $aboutGet->getPoster());
    $posterName = $aboutGet->getPosterObj()->getShortName();

?>

<script type="text/javascript">
tinyMCE.init({
        // General options
        mode : "textareas",
        theme : "advanced",
        plugins : "autolink,lists,spellchecker,pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template",

        // Theme options
        theme_advanced_buttons1 : "bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull",
        theme_advanced_buttons2 : "styleselect,formatselect,fontselect,fontsizeselect",
        theme_advanced_buttons3 : "cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo",
        theme_advanced_buttons4 : "link,unlink,anchor,image,cleanup,help,code,|,insertdate,inserttime,preview,|,forecolor,backcolor",
        theme_advanced_buttons5 : "tablecontrols,|,hr,removeformat,visualaid,|,sub,sup",
        theme_advanced_buttons6 : "charmap,emotions,iespell,media,advhr,|,ltr,rtl,|,fullscreen",
        theme_advanced_buttons7 : "insertlayer,moveforward,movebackward,absolute,|,styleprops,spellchecker,|,cite,abbr,acronym,del,ins,attribs",
        theme_advanced_buttons8 : "visualchars,nonbreaking,template,blockquote,pagebreak,|,insertfile,insertimage",
        theme_advanced_toolbar_location : "top",
        theme_advanced_toolbar_align : "left",
        theme_advanced_statusbar_location : "bottom",

        // Skin options
        skin : "o2k7",
        skin_variant : "silver",

        // Example content CSS (should be your site CSS)
        content_css : "/styles/andreas01.css"

        // Drop lists for link/image/media/template dialogs
        //template_external_list_url : "js/template_list.js",
        //external_link_list_url : "js/link_list.js",
        //external_image_list_url : "js/image_list.js",
        //media_external_list_url : "js/media_list.js",

        // Replace values for the template plugin
        //template_replace_values : {
//                username : "Some User",
//                staffid : "991234"
//        }
});
</script>


<div class="span11">
    <form method="post" action="<?php echo formAction(); ?>" name="addeditaboutform" id="addeditaboutform">
        <input type="hidden" name="postid" id="postid" value="<?php echo $get_postID; ?>" />
        <fieldset>
            <legend>Edit About Page</legend>
            <?php if($message != ""){ ?>
                <div class="alert-message error">
                    <p><?php echo $message; ?></p>
                </div>
            <?php } ?>
            <div class="clearfix">
                <label for="datecreated">Date</label>
                <div class="input">
                    <input type="hidden" name="datecreated" id="datecreated" value="<?php echo $about_dateCreated; ?>" />
                    <?php echo $about_dateCreated; ?>
                </div>
            </div><!-- /clearfix -->
            <div class="clearfix">
                <label for="title">Poster</label>
                <div class="input">
                    <input type="hidden" name="poster" id="poster" value="<?php echo $about_poster; ?>" />
                    <?php echo $posterName; ?>
                </div>
            </div><!-- /clearfix -->
            <div class="clearfix">
                <label for="requestcontent">Content</label>
                <div class="input">
                    <textarea name="requestcontent" id="requestcontent" class="large"><?php echo $about_content; ?></textarea>
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
