<?php

/**
 * NAME:    dbVersion.php
 * AUTHOR:  Paul Gagne
 * DATE:    Oct 11, 2011
 * DESCRIPTION: Represents a DB version
 */

class dbVersion {

    private $c_major;
    private $c_minor;
    private $c_build;

    public function __construct($major, $minor, $build) {
        $this->c_major = $major;
        $this->c_minor = $minor;
        $this->c_build = $build;
    }

    public function getMajor() {
        return $this->c_major;
    }

    public function getMinor() {
        return $this->c_minor;
    }

    public function getBuild() {
        return $this->c_build;
    }

    public function greaterThan(dbVersion $v) {

        if ($this->c_major > $v->c_major) {
            return true;
        }
        elseif ($this->c_major == $v->c_major && $this->c_minor > $v->c_minor) {
            return true;
        }
        elseif ($this->c_major == $v->c_major && $this->c_minor == $v->c_minor && $this->c_build > $v->c_build) {
            return true;
        }
        else {
            return false;
        }

    }

}

?>
