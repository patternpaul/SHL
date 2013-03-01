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

    /**
     * The career Collection
     * @param int $p_playerID the player id
     */
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
            WHERE   p.PlayerID = :playerID
            AND    tp.Position = 2 
            ORDER BY g.SeasonID DESC";
        $this->sql_args = array();
        $this->sql_args["playerID"] = $this->c_playerID;
    }

    /**
     * Gets collection readdy for regular player's career 
     * @param bit $p_playOff indicates if regular season or playoff
     */
    function getPlayoff($p_playOff){
        $this->c_playoff = $p_playOff;
        $this->sql_call = "
            SELECT  DISTINCT g.SeasonID
            FROM    Player AS p
            INNER JOIN TeamPlayer AS tp ON p.PlayerID = tp.PlayerID
            INNER JOIN Game AS g ON tp.GameID = g.GameID
            WHERE   p.PlayerID = :playerID
            AND    g.Playoff = :playoff
            AND    tp.Position = 2
            ORDER BY g.SeasonID DESC";
        $this->sql_args["playoff"] = $this->c_playoff;
    }

    /**
     * Sets the collection up for a goalie's career games, whether its regular season or playoffs
     * @param int $p_playOff The playoff indicator
     */
    function getGoaliePlayoff($p_playOff){
        $this->c_playoff = $p_playOff;
        $this->c_goalie = true;
        $this->sql_call = "
            SELECT  DISTINCT g.SeasonID
            FROM    Player AS p
            INNER JOIN TeamPlayer AS tp ON p.PlayerID = tp.PlayerID
            INNER JOIN Game AS g ON tp.GameID = g.GameID
            WHERE   p.PlayerID = :playerID
            AND    g.Playoff = :playoff
            AND    tp.Position = 1
            ORDER BY g.SeasonID DESC";
        $this->sql_args["playoff"] = $this->c_playoff;
    }

    /**
     * Set up collection to get all goalie career games.
     */
    function getGoalieCareer(){
        $this->c_goalie = true;
        $this->sql_call = "
            SELECT  DISTINCT g.SeasonID
            FROM    Player AS p
            INNER JOIN TeamPlayer AS tp ON p.PlayerID = tp.PlayerID
            INNER JOIN Game AS g ON tp.GameID = g.GameID
            WHERE   p.PlayerID = :playerID
            AND    tp.Position = 1
            ORDER BY g.SeasonID DESC";
    }

    /**
     * Get all goalie games that were played as a player
     */
    function getGoalieCareerAsPlayer(){
        $this->c_goalie = true;
        $this->c_asPlayer = true;
        $this->sql_call = "
            SELECT  DISTINCT g.SeasonID
            FROM    Player AS p
            INNER JOIN TeamPlayer AS tp ON p.PlayerID = tp.PlayerID
            INNER JOIN Game AS g ON tp.GameID = g.GameID
            WHERE   p.PlayerID = :playerID
            AND    tp.Position = 1
            ORDER BY g.SeasonID DESC";
    }

    /**
     * Loads the collection
     */
    public function load(){
        //variable declaration
        $playerSeasonCreated;

        //database connection
        $d = new db(0);

        //fetch the data
        $data = DBFac::getDB()->sql($this->sql_call, $this->sql_args); 


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



    /**
     * Fills the statCollection data collecting points
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
