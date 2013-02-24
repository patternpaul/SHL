<?php
/**
 * NAME:    teamPlayer.php
 * AUTHOR:  Paul Everton
 * DATE:    Feb 18, 2011
 * DESCRIPTION: Object describing a team player
 */

class teamPlayer extends player implements iComparable {
    //class variables
    public $c_teamPlayerID;
    public $c_position;
    public $c_color;
    public $c_gameID;
    private $c_points;
    private $c_game;
    private $c_winLoss;
    
    
    ///constructor
    function teamPlayer($p_teamPlayerID){
        //set the class variable
        $this->c_teamPlayerID = $p_teamPlayerID;
        $this->c_winLoss = false;

        $this->setObjectID($p_teamPlayerID);
        
        if($p_teamPlayerID != 0){
            //call the load function of the class
            $this->load();
        }
    }



    /*
     * NAME:    load
     * PARAMS:  N/A
     * DESC:    Loads the team player based off the team player ID
     *
     */
    
    public function load(){
        //load the team player bassed off the ID

        //database connection
        $d = new db(0);

        //fetch the data
        $data1 = $d->fetch("
            SELECT  tp.Color,
                    tp.GameID,
                    tp.PlayerID,
                    tp.Position,
                    tp.TeamPlayerID
            FROM    TeamPlayer AS tp
            WHERE   tp.TeamPlayerID = " . db::fmt($this->c_teamPlayerID,1));


        //fill the data
        foreach($data1 as $row) {
            $this->c_teamPlayerID = $row['TeamPlayerID'];
            
            $this->c_position = $row['Position'];
            $this->c_color = $row['Color'];
            $this->c_gameID = $row['GameID'];
            $this->setPlayerID($row['PlayerID']);

            
        }
        //load the parrent
        parent::load();
    }


     /*
     * NAME:    update
     * PARAMS:  N/A
     * DESC:    updates a game
     *
     */
    public function update(){
        //check to ensure no errors occured
        if(!$this->hasError()){
            //database connection
            $d = new db(0);

            //update the data
            $data1 = $d->exec("
                UPDATE  TeamPlayer
                SET     Position = " . db::fmt($this->c_position,1) . ",
                        Color = " . db::fmt($this->c_color,1) . ",
                        PlayerID = " . db::fmt($this->getPlayerID(),1) . ",
                        GameID = " . db::fmt($this->c_gameID,1) . "
                WHERE   TeamPlayerID = " . db::fmt($this->c_teamPlayerID,1));

            //indicate that the player was updated
            $this->addDBMessage("Team Player Updated", "Team Player not Updated! Database Error!", $data1, $d);
        }

    }


    /*
     * NAME:    insert
     * PARAMS:  N/A
     * DESC:    inserts a player
     *
     */
    public function insert(){
        //check to ensure no errors occured
        if(!$this->hasError()){
            //database connection
            $d = new db(0);

            //update the data
            $data = $d->exec("
                INSERT INTO  TeamPlayer (Position, Color, PlayerID, GameID)
                VALUES (" . db::fmt($this->c_position,1) . ",
                        " . db::fmt($this->c_color,1) . ",
                        " . db::fmt($this->getPlayerID(),1) . ",
                        " . db::fmt($this->c_gameID,1) . ")");

            //set the id inserted
            $this->c_teamPlayerID = $d->last_id;

            //indicate that the game was inserted
            $this->addDBMessage("Team Player Added", "Team Player not Added! Database Error!", $data, $d);
        }
    }





     /*
     * NAME:    getTeamPlayerPoints
     * PARAMS:  N/A
     * DESC:    will return the points scored by this team player
     *
     */
     public function getTeamPlayerPoints(){
        //verify if the team player point collection has been created
         if(!isset($this->c_points)){
             //create the collection and fill
             $this->c_points = new pointCollection();
             //get this team player's points
             $this->c_points->getTeamPlayerPoints($this->c_teamPlayerID);
             //load the collection
             $this->c_points->load();
         }
         //return the collection
         return $this->c_points;
     }

     /*
     * NAME:    getGame
     * PARAMS:  N/A
     * DESC:    will return the game this team player is participating in
     *
     */
     public function getGame(){
        //verify if the game has been created
         if(!isset($this->c_game)){
             //create the game
             $this->c_game = new game($this->c_gameID);
         }
         //return the game
         return $this->c_game;
     }


    
     /*
     * NAME:    compare
     * PARAMS:  $p_objectToCompare = passed object to compare
     * DESC:    will return true or false if its the same object
     *
     */
     public function compareTo($p_objectToCompare){
        //variable declaration
        $is_comparable = false;

        //compare the objects
        if($this->c_teamPlayerID == $p_objectToCompare->c_teamPlayerID){
            $is_comparable = true;
        }

        return $is_comparable;
     }

     /*
     * NAME:    isID
     * PARAMS:  $p_ID = passed ID to compare
     * DESC:    will return true or false if the object has that ID
     *
     */
     public function isID($p_ID){
        //variable declaration
        $is_id = false;

        //compare the objects
        if($this->c_teamPlayerID == $p_ID){
            $is_id = true;
        }

        return $is_id;
     }
}
?>
