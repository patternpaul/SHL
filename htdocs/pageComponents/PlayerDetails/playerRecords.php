    <p>
        <?php
        $currentRecord;
        $recordName;
        $currentHolder;
        //loop over the season stats for display
        for($alpha = 0; $alpha < $playerRecords->count(); $alpha += 1){
            $currentRecord = $playerRecords->get($alpha);
            $recordName = $currentRecord->c_name;
            echo "<h2>" . $recordName . "</h2><div class='postDelimiter'>";
            for($beta = 0; $beta < $currentRecord->count(); $beta += 1){
                $currentHolder = $currentRecord->get($beta);
                echo $currentHolder->getValue() . "<br />";
            }
            echo "</div>";
        }
    ?>

    </p>


    <div id="breakPoint">&nbsp;</div>