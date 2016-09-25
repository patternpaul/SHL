<?php

namespace App\Http\Controllers;

class ServerController extends Controller
{

    public function stats() {
        $result = [];

        /**
         *
         This is based off https://github.com/stevencorona/opcache-json/blob/master/src/Opcache/Status.php



         */


        // Guard execution if the extension is not loaded.
        if (! extension_loaded("Zend OPcache")) {
            return response()->json($result);
        }
        // Clear out data from prevous run
        $result['status'] = null;
        $raw = \opcache_get_status(true);
        // The scripts output has a really non-optimal format
        // for JSON, the result is a hash with the full path
        // as the key. Let's strip the key and turn it into
        // a regular array.
        // Make a copy of the raw scripts and then strip it from
        // the data.
        $scripts = $raw['scripts'];
        unset($raw['scripts']);
        $result['scripts'] = [];
        // Loop over each script and strip the key.
        foreach($scripts as $key => $val) {
            $result['scripts'][] = $val;
        }
        // Sort by memory consumption
        usort($result['scripts'], function($a, $b) {
            if ($a["memory_consumption"] == $b["memory_consumption"]) return 0;
            return ($a["memory_consumption"] < $b["memory_consumption"]) ? 1 : -1;
        });


        $result['status'] = $raw;

        return response()->json($result);
    }
}
