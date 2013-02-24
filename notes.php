<?php
    require_once(dirname(__FILE__).'/pageComponents/header.php');
    require_once(dirname(__FILE__).'/pageComponents/menu.php');

?>

<div id="content">

<h2>Welcome to the SHL site!</h2>

<p>
Change the statCollection collection to contain <br /><br />

-addRegularGame<br />
-AddPlayoffGame<br />
-AddRegPlayer<br />
-AddRegGoalie<br />
-AddPlayoffPlayer<br />
-AddPlayoffGoalie<br /><br />

these will add to separate statCollections of those objects. Need gets for them.<br /><br />

Can we code a generic "fill" method in statCollection?<br /><br />

store within session a "object being modified". Classes would hold the return from DB for comparison? Change for later since Multi user should not be a problem <br />

</p>
</div>


<?php

require_once(dirname(__FILE__).'/pageComponents/footer.php');
?>