<?php

namespace App\Http\Controllers;

use App\Listeners\Games;
use App\Listeners\Players;
use App\Listeners\Records\RecordStore;

class RecordsController extends Controller
{
    private $records;
    private $players;
    private $games;

    public function __construct(RecordStore $records, Players $players, Games $games)
    {
        $this->records = $records;
        $this->players = $players;
        $this->games = $games;
        $viewGames = array_reverse($this->games->getLatestSeasonGames());
        view()->share('header_games', $viewGames);
        view()->share('active_navbar_records', true);
    }

    public function index()
    {
        $players = $this->players->getAll();
        $records = $this->records->getRecords();
        return view('app.records.index', ["records" => $records, "players" => $players]);
    }
}
