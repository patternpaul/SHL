<?php
/**
 * NAME:    playerSeason.php
 * AUTHOR:  Paul Everton
 * DATE:    Feb 18, 2011
 * DESCRIPTION: Collection describing a player's season
 */

class playerSeason extends statCollection {
    //put your code here
    public $c_seasonID;
    public $c_playerID;
    public $c_playoff;
    public $c_position;
    public $sql_call;


    //Constructors
    function playerSeason($p_seasonID, $p_playerID){
        //initialize class variables
        parent::__construct();
        $this->c_seasonID = $p_seasonID;
        $this->c_playerID = $p_playerID;
        $this->c_position = 2;
        $this->c_playoff = 0;
        $this->sql_call = "
            SELECT      tp.TeamPlayerID
            FROM        TeamPlayer AS tp
            INNER JOIN  Game AS g ON tp.GameID = g.GameID
            WHERE       tp.PlayerID = " . db::fmt($this->c_playerID,1) .
            " AND        g.SeasonID = " . db::fmt($this->c_seasonID,1) .
            " AND    tp.Position = 2" .
            " ORDER BY  g.SeasonID, g.Playoff DESC, g.GameID DESC";
    }



    function getPlayoff($p_playOff){
        $this->c_playoff = $p_playOff;
        $this->sql_call = "
            SELECT      tp.TeamPlayerID
            FROM        TeamPlayer AS tp
            INNER JOIN  Game AS g ON tp.GameID = g.GameID
            WHERE       tp.PlayerID = " . db::fmt($this->c_playerID,1) .
            " AND        g.SeasonID = " . db::fmt($this->c_seasonID,1) .
            " AND        g.Playoff = " . db::fmt($this->c_playoff,1) .
            " AND    tp.Position = 2" .
            " ORDER BY  g.SeasonID, g.Playoff DESC, g.GameID DESC";
    }


    function getGoaliePlayoff($p_playOff){
        $this->c_playoff = $p_playOff;
        $this->c_position = 1;
        $this->sql_call = "
            SELECT   DISTINCT   stp.TeamPlayerID
            FROM        TeamPlayer AS gtp
            INNER JOIN  Game AS g ON gtp.GameID = g.GameID
            INNER JOIN  TeamPlayer AS stp ON gtp.GameID = stp.GameID
            INNER JOIN  Point AS p ON stp.TeamPlayerID = p.TeamPlayerID
            INNER JOIN  Player AS gp ON gtp.PlayerID = gp.PlayerID
            INNER JOIN  Player AS sp ON stp.PlayerID = sp.PlayerID
            WHERE       gtp.PlayerID = " . db::fmt($this->c_playerID,1) .
            " AND        g.SeasonID = " . db::fmt($this->c_seasonID,1) .
            " AND        g.Playoff = " . db::fmt($this->c_playoff,1) .
            " AND	     gtp.Color != stp.Color
              AND	     p.PointType = 1
              AND	     stp.TeamPlayerID != gtp.TeamPlayerID
              AND	     gtp.Position = 1
              ORDER BY  g.SeasonID, g.Playoff DESC, g.GameID DESC";
    }

    function getGoalieSeason(){
        $this->c_position = 1;
        $this->sql_call = "
            SELECT   DISTINCT   stp.TeamPlayerID
            FROM        TeamPlayer AS gtp
            INNER JOIN  Game AS g ON gtp.GameID = g.GameID
            INNER JOIN  TeamPlayer AS stp ON gtp.GameID = stp.GameID
            INNER JOIN  Point AS p ON stp.TeamPlayerID = p.TeamPlayerID
            INNER JOIN  Player AS gp ON gtp.PlayerID = gp.PlayerID
            INNER JOIN  Player AS sp ON stp.PlayerID = sp.PlayerID
            WHERE       gtp.PlayerID = " . db::fmt($this->c_playerID,1) .
            " AND        g.SeasonID = " . db::fmt($this->c_seasonID,1) .
            " AND	     gtp.Color != stp.Color
              AND	     p.PointType = 1
              AND	     stp.TeamPlayerID != gtp.TeamPlayerID
              AND	     gtp.Position = 1
              ORDER BY  g.SeasonID, g.Playoff DESC, g.GameID DESC";
    }

     function getGoalieSeasonAsPlayer(){
         $this->c_position = 1;
        $this->sql_call = "
            SELECT      tp.TeamPlayerID
            FROM        TeamPlayer AS tp
            INNER JOIN  Game AS g ON tp.GameID = g.GameID
            WHERE       tp.PlayerID = " . db::fmt($this->c_playerID,1) .
            " AND        g.SeasonID = " . db::fmt($this->c_seasonID,1) .
            " AND	tp.Position = 1
             ORDER BY  g.SeasonID, g.Playoff DESC, g.GameID DESC";
    }


    /*
     * NAME:    isID
     * PARAMS:  N/A
     * DESC:    verifies if this is the season you want
     */
    public function isID($p_id){
        //variable definition
        $lb_isID = false;
        
        if($p_id == $this->c_seasonID){
            $lb_isID = true;
        }
        
        return $lb_isID;
    }


       
            

    /*
     * NAME:    load
     * PARAMS:  N/A
     * DESC:    loads the collection based off the SQL call
     */
    public function load(){
        //variable declaration
        $playerCreated;
        //database connection
        $d = new db(0);

        //fetch the data
        $data = $d->fetch($this->sql_call);


        //fill the data
        foreach($data as $row) {
            //create the player
            $playerCreated = new teamPlayer($row['TeamPlayerID']);


            //add the player to the collection
            $this->add($playerCreated);
        }
    }

    /*
     * NAME:    fillScores
     * PARAMS:  N/A
     * DESC:    will fill the assist and goal counts
     */
    public function fillScores(){
        //variable definition
        $uniqueGames = array();
        $currentTeamPlayer;



        if($this->c_position == 2){
            //variable declaration
            $sqlCall = "
                SELECT *
                FROM QuickPlayerDataTable AS QPD
                WHERE QPD.PlayerID = " . db::fmt($this->c_playerID,1) . "
                AND QPD.Playoff = " . db::fmt($this->c_playoff,0);

            if($this->c_seasonID > 0){
                $sqlCall = $sqlCall  . " AND QPD.SeasonID = " . db::fmt($this->c_seasonID,1);
            }

            $sqlCall = $sqlCall  . "
                GROUP BY QPD.PlayerID
            ";

            //database connection
            $d = new db(0);

            //fetch the data
            $data = $d->fetch($sqlCall);

            //fill the data
            foreach($data as $row) {
                $this->addWinCount($row['Wins']);
                $this->addLossCount($row['Losses']);
                $this->addGWGCount($row['WinningGoals']);
                $this->addGameCount($row['Wins'] + $row['Losses']);
                $this->addGoalCount($row['Goals']);
                $this->addAssistCount($row['Assists']);
                $this->addPlayerTeamScoresCount($row['TeamScores']);
            }
        }else{


            //variable declaration
            $sqlCall = "
                SELECT *
                FROM QuickGoalieDataTable AS QPD
                WHERE QPD.PlayerID = " . db::fmt($this->c_playerID,1) . "
                AND QPD.Playoff = " . db::fmt($this->c_playoff,0);

            if($this->c_seasonID > 0){
                $sqlCall = $sqlCall  . " AND QPD.SeasonID = " . db::fmt($this->c_seasonID,1);
            }

            $sqlCall = $sqlCall  . "
                GROUP BY QPD.PlayerID
            ";

            //database connection
            $d = new db(0);
            
            //fetch the data
            $data = $d->fetch($sqlCall);

            //fill the data
            foreach($data as $row) {


                $this->addGoalAgainstCount($row['GoalsAgainst']);
                $this->addGoalCount($row['Goals']);
                $this->addAssistCount($row['Assists']);

                $this->addWinCount($row['Wins']);
                $this->addLossCount($row['Losses']);
                $this->addGameCount($row['Wins'] + $row['Losses']);
                $this->addShutOutCount($row['ShutOutCount']);
                $this->addSecondsPlayedCount($row['TotalGameSeconds']);
            }




//
//
//
//            //loop over the teamplayers
//            for($alpha = 0; $alpha < $this->count(); $alpha += 1){
//                //get the current player
//                $currentTeamPlayer = $this->get($alpha);
//                //add the team player goals
//                $this->addGoalCount($currentTeamPlayer->getTeamPlayerPoints()->getGoalCount());
//                //add the team player's assists
//                $this->addAssistCount($currentTeamPlayer->getTeamPlayerPoints()->getAssistCount());
//
//            }
//
            
            
            
            
//             //variable declaration
//            $sqlCall = "
//                SELECT *
//                FROM PlayerWinsLossesByPosition AS QPD
//                WHERE QPD.Position = 1
//                AND QPD.PlayerID = " . db::fmt($this->c_playerID,1) . "
//                AND QPD.Playoff = " . db::fmt($this->c_playoff,1);
//
//            if($this->c_seasonID > 0){
//                $sqlCall = $sqlCall  . " AND QPD.SeasonID = " . db::fmt($this->c_seasonID,1);
//            }
//
//            $sqlCall = $sqlCall  . "
//                GROUP BY QPD.PlayerID
//            ";
//
//            //database connection
//            $d = new db(0);
//
//            //fetch the data
//            $data = $d->fetch($sqlCall);
//
//            //fill the data
//            foreach($data as $row) {
//                $this->addWinCount($row['Wins']);
//                $this->addLossCount($row['Losses']);
//                $this->addGameCount($row['Wins'] + $row['Losses']);
//            }







            $this->addGWGCount(0);
            //
        }








         //$this->addGWGCount(0);
        
//
//        $this->addWinCount(0);
//      $this->addLossCount(0);
     // $this->addGWGCount(0);
        //indicate it has been filled;
        $this->hasBeenFilled();

    }

}
?>
