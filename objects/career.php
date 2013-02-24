<?php
/**
 * NAME:    career.php
 * AUTHOR:  Paul Everton
 * DATE:    Feb 18, 2011
 * DESCRIPTION: Collection describing a player's career
 */

class career extends statCollection {
    //put your code here
    public  $c_playerID;
    public  $c_playoff;
    public  $c_goalie;
    public  $c_asPlayer;
    const   playoff = 1;
    const   regularSeason = 0;

    //Constructors
    function career($p_playerID){
        //initialize class variables
        parent::__construct();
  
        $this->c_playerID = $p_playerID;
        $this->c_goalie = false;
        $this->c_asPlayer = false;
        $this->sql_call = "
            SELECT  DISTINCT g.SeasonID
            FROM    Player AS p
            INNER JOIN TeamPlayer AS tp ON p.PlayerID = tp.PlayerID
            INNER JOIN Game AS g ON tp.GameID = g.GameID
            WHERE   p.PlayerID = " . db::fmt($p_playerID,1) .
            " AND    tp.Position = 2" .
            " ORDER BY g.SeasonID DESC";
    }

    function getPlayoff($p_playOff){
        $this->c_playoff = $p_playOff;
        $this->sql_call = "
            SELECT  DISTINCT g.SeasonID
            FROM    Player AS p
            INNER JOIN TeamPlayer AS tp ON p.PlayerID = tp.PlayerID
            INNER JOIN Game AS g ON tp.GameID = g.GameID
            WHERE   p.PlayerID = " . db::fmt($this->c_playerID,1) .
            " AND    g.Playoff = " . db::fmt($p_playOff,1) .
            " AND    tp.Position = 2" .
            " ORDER BY g.SeasonID DESC";
    }

    function getGoaliePlayoff($p_playOff){
        $this->c_playoff = $p_playOff;
        $this->c_goalie = true;
        $this->sql_call = "
            SELECT  DISTINCT g.SeasonID
            FROM    Player AS p
            INNER JOIN TeamPlayer AS tp ON p.PlayerID = tp.PlayerID
            INNER JOIN Game AS g ON tp.GameID = g.GameID
            WHERE   p.PlayerID = " . db::fmt($this->c_playerID,1) .
            " AND    g.Playoff = " . db::fmt($p_playOff,1) .
            " AND    tp.Position = 1" .
            " ORDER BY g.SeasonID DESC";
    }

    function getGoalieCareer(){
        $this->c_goalie = true;
        $this->sql_call = "
            SELECT  DISTINCT g.SeasonID
            FROM    Player AS p
            INNER JOIN TeamPlayer AS tp ON p.PlayerID = tp.PlayerID
            INNER JOIN Game AS g ON tp.GameID = g.GameID
            WHERE   p.PlayerID = " . db::fmt($this->c_playerID,1) .
            " AND    tp.Position = 1" .
            " ORDER BY g.SeasonID DESC";
    }

    function getGoalieCareerAsPlayer(){
        $this->c_goalie = true;
        $this->c_asPlayer = true;
        $this->sql_call = "
            SELECT  DISTINCT g.SeasonID
            FROM    Player AS p
            INNER JOIN TeamPlayer AS tp ON p.PlayerID = tp.PlayerID
            INNER JOIN Game AS g ON tp.GameID = g.GameID
            WHERE   p.PlayerID = " . db::fmt($this->c_playerID,1) .
            " AND    tp.Position = 1" .
            " ORDER BY g.SeasonID DESC";
    }

    /*
     * NAME:    load
     * PARAMS:  N/A
     * DESC:    loads the collection based off the SQL call
     */
    public function load(){
        //variable declaration
        $playerSeasonCreated;

        //database connection
        $d = new db(0);

        //fetch the data
        $data = $d->fetch($this->sql_call);


        //fill the data
        foreach($data as $row) {
            //create the player
            $playerSeasonCreated = new playerSeason($row['SeasonID'], $this->c_playerID);


            //check playoff indicator
            if(isset($this->c_playoff)){
                //playoff indication set, pull appropriate info
                $playerSeasonCreated->getPlayoff($this->c_playoff);
                if($this->c_goalie){
                    //goalie season selected
                    $playerSeasonCreated->getGoaliePlayoff($this->c_playoff);
                }
            }elseif($this->c_goalie){
                if($this->c_asPlayer){
                    $playerSeasonCreated->getGoalieSeasonAsPlayer();
                }else{
                    $playerSeasonCreated->getGoalieSeason();
                }
            }

            //load the player
            $playerSeasonCreated->load();

            //add the player to the collection
            $this->add($playerSeasonCreated);
        }
    }



    /*
     * NAME:    fillScores
     * PARAMS:  N/A
     * DESC:    will fill the assist and goal counts
     */
    public function fillScores(){
        //variable definition
        $currentSeason;

        
        //loop over the teamplayers
        for($alpha = 0; $alpha < $this->count(); $alpha += 1){
            //get the season
            $currentSeason = $this->get($alpha);
            //add the team player goals
            $this->addGoalCount($currentSeason->getGoalCount());
            //add the team player's assists
            $this->addAssistCount($currentSeason->getAssistCount());
            //add the game count
            $this->addGameCount($currentSeason->getGameCount());
            //add the win count
            $this->addWinCount($currentSeason->getWinCount());
            //add the loss count
            $this->addLossCount($currentSeason->getLossCount());
            //add the GWG count
            $this->addGWGCount($currentSeason->getGWGCount());
            //add team scores count
            $this->addPlayerTeamScoresCount($currentSeason->getPlayerTeamScoresCount());
            
            //add the goal GA
            $this->addGoalAgainstCount($currentSeason->getGoalsAgainstCount());

            //add the shut out
            $this->addShutOutCount($currentSeason->getShutOutCount());
            //add the shut out
            $this->addSecondsPlayedCount($currentSeason->getSecondsPlayedCount());

        }
        //indicate it has been filled;
        $this->hasBeenFilled();
    }

    
}
?>
