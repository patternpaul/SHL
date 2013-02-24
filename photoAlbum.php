<?php
    require_once(dirname(__FILE__).'/pageComponents/header.php');
    require_once(dirname(__FILE__).'/pageComponents/menu.php');

?>
<script type="text/javascript" src="/scripts/picasa.js"></script>

<div id="content">
    <h2 id="album_name_loc"></h2>
    <a href="/photoAlbumList.php">Back To Albums</a>
    <p>
        <ul class="gallery" id='photo_loc'>

        </ul>
    </p>
</div>


<?php

require_once(dirname(__FILE__).'/pageComponents/footer.php');
?>
