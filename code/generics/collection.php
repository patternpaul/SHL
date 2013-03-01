<?php

/**
 * NAME:    collection.php
 * AUTHOR:  Paul Everton
 * DATE:    Feb 18, 2011
 * DESCRIPTION: a generic collection
 */


class collection extends object {
    //class variables
    public $col_arr;
    public $sql_call;
    public $sql_args;
    const base_sql = "";
    
    //Constructors
    function collection(){
        //initialize class variables
        $this->col_arr = array();
    }





    /*
     * NAME:    add
     * PARAMS:  $passedObj = a passed generic object
     * DESC:    adds an object to the collection array
     */
    public function add($passedObj){
        $this->col_arr[] = $passedObj;
    }

    /*
     * NAME:    count
     * PARAMS:  N/A
     * DESC:    gets the count of objects
     */
    public function count(){
        return count($this->col_arr);
    }

    /*
     * NAME:    get
     * PARAMS:  $passedLoc = the location
     * DESC:    gets the object at the specified location
     */
    public function get($passedLoc){
        return $this->col_arr[$passedLoc];
    }

     /*
     * NAME:    sql_reset
     * PARAMS:  N/A
     * DESC:    Reloads the SQL call
     */
    public function sql_reset(){
        $this->sql_call = $this::base_sql;
    }


     /*
     * NAME:    getByID
     * PARAMS:  $p_ID = the id of the object
     * DESC:    gets the object with that ID
     */
    public function getByID($p_ID){
        $returnObj = null;

        //loop over the array of objects to find
        foreach($this->col_arr as $obj){
            //check to see if it's the same ID
            if($obj->isID($p_ID)){
               $returnObj = $obj;
            }
        }
        
        //return the found object
        return $returnObj;
    }


}
?>