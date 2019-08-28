<?php

namespace App\Http\Controllers;

use App\Aggregates\Game;
use App\Commands\Game\AddFullGame;
use App\Commands\Game\EditFullGame;
use App\Commands\Player\AddPlayer;
use App\Commands\Player\EditPlayer;
use App\Listeners\Games;
use App\Listeners\Players;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;

class ContentController extends Controller
{
    private $players;
    private $games;

    public function __construct(Players $players, Games $games)
    {
        $this->players = $players;
        $this->games = $games;
        $viewGames = array_reverse($this->games->getLatestSeasonGames());
        view()->share('header_games', $viewGames);
    }

    public function index() {
        return view('index');
    }
}

