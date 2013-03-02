<?php
/**
 * NAME:    object.php
 * AUTHOR:  Paul Everton
 * DATE:    Feb 25, 2011
 * DESCRIPTION: a generic object
 */
class object {
    //put your code here
    private  $c_errorOccured = false;
    private  $c_message = "";
    const c_messageNewLine = "<br />";
    public  $phoneFormats = array('(###)###-####','');
    private $c_objectID;

    function object(){
        //default the class variables
        $this->c_objectID = sql_object_hash();
    }


    /*
     * NAME:    setObjectID
     * PARAMS:  $p_passedID
     * DESC:    will set the object ID
     */
    public function setObjectID($p_passedID){
        $this->c_objectID = $p_passedID;
    }

    /*
     * NAME:    getObjectID
     * PARAMS:  N/A
     * DESC:    will return the object ID
     */
    public function getObjectID(){
        return $this->c_objectID;
    }

    
    
 
    
    
    
    /*
     * NAME:    exec
     * PARAMS:  N/A
     * DESC:    updates or inserts a player
     *
     */
    public function exec(){
        //check to ensure no errors occured
        if(!$this->hasError()){
            if($this->getObjectID() > 0){
                //run update
                $this->update();
            }  else {
                //run insert
                $this->insert();
            }
        }
    }


    /*
     * NAME:    update
     * PARAMS:  N/A
     * DESC:    updates a player
     *
     */
    public function update(){
        //throw error indicating the function has not been implemented
        throw new Exception("update has not been implemented");
    }


    /*
     * NAME:    insert
     * PARAMS:  N/A
     * DESC:    inserts a player
     *
     */
    public function insert(){
        //throw error indicating the function has not been implemented
        throw new Exception("insert has not been implemented");
    }








    /*
     * NAME:    errorOccured
     * PARAMS:  N/A
     * DESC:    indicates that an error occured
     */
    public function errorOccured(){
        $this->c_errorOccured = true;
    }

    /*
     * NAME:    hasError
     * PARAMS:  N/A
     * DESC:    returns the error indicator
     *
     */
    public function hasError(){
        return $this->c_errorOccured;
    }

    /*
     * NAME:    addMessage
     * PARAMS:  $p_message = message to add to collection
     * DESC:    adds to ongoing message log
     *
     */
    public function addMessage($p_message){
        $this->c_message = $this->c_message . $p_message . self::c_messageNewLine;
    }

    /*
     * NAME:    addDBMessage
     * PARAMS:  $p_passMessage = message to add to collection
     *          $p_failMessage = message to add to collection
     *          $p_data = the data return
     *          $p_db = the db used
     * DESC:    adds to ongoing message log
     *
     */
    public function addDBMessage($p_passMessage, $p_failMessage,$p_data, $p_db){
        if($p_db->hasError()){
            $this->errorOccured();
            //failed the db call, add fail message
            $this->addMessage($p_failMessage);
            //check to see if we should add DB error log
            if(VIEW_DBERRORS){
                //ADD db log errors
               $this->addMessage($p_db->log);
            }
        }else{
            //passed, add passed message
            $this->addMessage($p_passMessage);
        }
    }


    /*
     * NAME:    getMessage
     * PARAMS:  N/A
     * DESC:    returns the ongoing message log
     */
    public function getMessage(){
        return $this->c_message;
    }

    /*
     * NAME:    resetMessage
     * PARAMS:  N/A
     * DESC:    resets the message log
     */
    public function resetMessage(){
        $this->c_errorOccured = false;
        $this->c_message = "";
    }

    /*
     * NAME:    checkEmailFormat
     * PARAMS:  $email
     * DESC:    returns the boolean indicating email is formatted correctly
     *          REF: http://www.linuxjournal.com/article/9585
     *          REF AUTHOR: Jun 01, 2007  By Douglas Lovell
     *          ORIG AUTHOR: Dave Child
     *          REF: http://www.ilovejackdaniels.com/php/email-address-validation
     * MODIFICATION: ereg changed to preg_match. ereg was deprecated
     */
    public function checkEmailFormat($email) {
      // First, we check that there's one @ symbol,
      // and that the lengths are right.
      if (!preg_match("/^[^@]{1,64}@[^@]{1,255}$/", $email)) {
        // Email invalid because wrong number of characters
        // in one section or wrong number of @ symbols.
        return false;
      }
      // Split it into sections to make life easier
      $email_array = explode("@", $email);
      $local_array = explode(".", $email_array[0]);
      for ($i = 0; $i < sizeof($local_array); $i++) {
        if
    (!preg_match("/^(([A-Za-z0-9!#$%&'*+=?^_`{|}~-][A-Za-z0-9!#$%&
    ↪'*+=?^_`{|}~\.-]{0,63})|(\"[^(\\|\")]{0,62}\"))$/",
    $local_array[$i])) {
          return false;
        }
      }
      // Check if domain is IP. If not,
      // it should be valid domain name
      if (!preg_match("/^\[?[0-9\.]+\]?$/", $email_array[1])) {
        $domain_array = explode(".", $email_array[1]);
        if (sizeof($domain_array) < 2) {
            return false; // Not enough parts to domain
        }
        for ($i = 0; $i < sizeof($domain_array); $i++) {
          if
    (!preg_match("/^(([A-Za-z0-9][A-Za-z0-9-]{0,61}[A-Za-z0-9])|
    ↪([A-Za-z0-9]+))$/",
    $domain_array[$i])) {
            return false;
          }
        }
      }
      return true;
    }


    /*
     * NAME:    phoneNumberFormatMessage
     * PARAMS:  N/A
     * DESC:    returns the message of proper phone number formats
     */
    public function phoneNumberFormatMessage(){
        //variable declaration
        $returnMessage = "Valid formats are ";

        //loop over the formats for display
        foreach($this->phoneFormats as $key => $value){
            if($value != ""){
                $returnMessage = $returnMessage . $value . ", ";
            }else{
                $returnMessage = $returnMessage . "BLANK, ";
            }
        }

        //return the format message
        return $returnMessage;
    }


    /*
     * NAME:    checkPhoneNumber
     * PARAMS:  $p_number
     * DESC:    returns the boolean indicating phone number is formatted correctly
     */
    public function checkPhoneNumber($number)
    {
        return $this->validateTelephoneNumber($number, $this->phoneFormats);
    }


    /*
     * NAME:    validateTelephoneNumber
     * PARAMS:  $number, $formats
     * DESC:    returns the boolean indicating phone number is formatted correctly
     *          REF: http://www.bitrepository.com/how-to-validate-a-telephone-number.html
     *          REF AUTHOR: Gabriel C.
     * MODIFICATION: ereg_replace replaced with preg_replace
     */
    public function validateTelephoneNumber($number, $formats)
    {
        //variable declaration
        $returnCheck = true;
        $format = trim(preg_replace("/[0-9]/", "#", $number));

        $returnCheck = in_array(trim($format), $formats);

        return $returnCheck;
    }





}
?>
