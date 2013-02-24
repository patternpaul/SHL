<?php
/**
 * NAME:    selectCreator.php
 * AUTHOR:  Paul Everton
 * DATE:    Feb 24, 2011
 * DESCRIPTION: Object to create HTML string
 */



class selectCreator extends object  {
    //class variables
    public $c_selectString;
    public $c_className;
    public $c_idName;
    public $c_style;
    public $c_default;

    //constructor
    function selectCreator($p_name, $p_idName, $p_className, $p_style, $p_default){
        //variable declaration
        $classString = "";
        $styleString = "";

        if($p_className != ""){
            $classString = " class=\"" . $p_className . "\"";
        }

        if($p_style != ""){
            $styleString = " style=\"" . $p_style . "\"";
        }

        $this->c_selectString = "";
        $this->c_className = $p_className;
        $this->c_idName = $p_idName;
        $this->c_style = $p_style;
        $this->c_default = $p_default;

        //start building the select string
        $this->c_selectString = "<select name=\"" . $p_name . "\" id=\"" . $p_idName . "\"" . $classString . $styleString . " ><option value=\"0\">Select</option>";
    }

    /*
     * NAME:    addOption
     * PARAMS:  $p_value = the value of the option
     *          $p_display =  the display value
     * DESC:    Adds an entry to the select. Will sellect if the option is the default
     *
     */
    function addOption($p_value, $p_display){
        //variable declaration
        $selectedString = "";

        //check to see if this option should be selected
        if($p_value == $this->c_default){
            //this option should be selected
            $selectedString = "selected=\"selected\"";
        }

        //add an option to the select string
        $this->c_selectString = $this->c_selectString . "<option " . $selectedString . " value=\"" . $p_value . "\" >" . $p_display . "</option>";
    }


    /*
     * NAME:    getSelect
     * PARAMS:  N/A
     * DESC:    will return finnished select string
     *
     */
    function getSelect(){
        //variable declaration
        $returnVal = "";

        //cap off the select
        $returnVal = $this->c_selectString . "</select>";

        return $returnVal;
    }

}


?>
