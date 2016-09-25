<?php
/**
 * Created by PhpStorm.
 * User: pauleverton
 * Date: 2016-08-21
 * Time: 11:20 AM
 */
namespace App\Http\Controllers;

use App\Infrastructure\Aggregate\IAggregateRepository;
use App\Listeners\Games;
use App\Listeners\GoalieStats;
use App\Listeners\Players;
use App\Listeners\PlayerStats;
use App\Listeners\Records\RecordStore;
use Illuminate\Support\Facades\Auth;

class SeasonsController extends Controller
{
    private $games;

    public function __construct(Games $games)
    {
        $this->games = $games;
        $viewGames = array_reverse($this->games->getLatestSeasonGames());
        view()->share('header_games', $viewGames);
    }

    public function index($season) {
        $games = $this->games->getSpecificSeasonGames($season);
        $currentSeason = $this->games->getLatestSeason();
        return view('app.seasons.id', ["games" => $games, "season" => $season, "currentSeason" => $currentSeason]);
    }
}
