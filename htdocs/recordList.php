<?php
    require_once(dirname(__FILE__).'/pageComponents/header.php');
    

    //variable declaration
    $recordObj = new records();

    //$fiveHundredPointClub = $recordObj->getFiveHundredPointClub();
    //$oneThousandPointClub = $recordObj->getOneThousandPointClub();
    
    //record reset
    $recordObj->recordRefil();
    //get record collection
    $recordCol = new recordCollection();
    $recordCol->load();
    
?>

<div class="span11">
    <?php
        $currentRecord;
        $recordName;
        $currentHolder;
        //loop over the season stats for display
        for($alpha = 0; $alpha < $recordCol->count(); $alpha += 1){
            $currentRecord = $recordCol->get($alpha);
            $recordName = $currentRecord->c_name;
            ?>
            <div class="row">
                    <div class="span10">
                    <h2><?php echo $recordName; ?></h2>
                    <?php
                        for($beta = 0; $beta < $currentRecord->count(); $beta += 1){
                            $currentHolder = $currentRecord->get($beta);
                    ?>
                
                    <?php echo $currentHolder->getValue(); ?><br />
                    <?php 
                        }
                    ?>
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
