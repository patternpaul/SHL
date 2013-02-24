<?php

/**
 * NAME:    iComparable.php
 * AUTHOR:  Paul Everton
 * DATE:    Feb 18, 2011
 * DESCRIPTION: comparable interface
 */

interface iComparable {
    //put your code here
    public function compareTo($p_objectToCompare);
    public function isID($p_ID);
}
?>
