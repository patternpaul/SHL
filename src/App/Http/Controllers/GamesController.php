<?php
/**
 * Created by PhpStorm.
 * User: pauleverton
 * Date: 2016-08-21
 * Time: 11:20 AM
 */
namespace App\Http\Controllers;

use App\Listeners\Games;
use App\Listeners\GoalieStats;
use App\Listeners\Players;
use App\Listeners\PlayerStats;
use App\Listeners\Records\RecordStore;
use Illuminate\Support\Facades\Auth;

class GamesController extends Controller
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

    public function index($gameId) {
        $game = $this->games->getById($gameId);
        $players = $this->players->getAll();
        return view('app.games.id', ["game" => $game, "players" => $players]);
    }
}
