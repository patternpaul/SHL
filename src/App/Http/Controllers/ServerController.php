<?php

namespace App\Http\Controllers;

use Opcache\Status;

class ServerController extends Controller
{

    public function stats() {
        $opcache = new Status();

        return $opcache->status(true);
    }
}
