<?php

namespace App\Http\Controllers;

class ServerController extends Controller
{

    public function stats() {
        $opcache = new \Opcache\Status();

        return $opcache->status(true);
    }
}
