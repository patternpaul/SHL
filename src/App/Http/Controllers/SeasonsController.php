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
    private $agg;
    public function __construct(Games $games, IAggregateRepository $agg)
    {
        $this->games = $games;
        $this->agg = $agg;
        $viewGames = array_reverse($this->games->getLatestSeasonGames());
        view()->share('header_games', $viewGames);
    }

    public function index($season) {
        $games = $this->games->getSpecificSeasonGames($season);
        return view('app.seasons.id', ["games" => $games, "season" => $season]);
    }

    public function test()
    {
        return response()->json($this->agg->getAllDomainEvents());
    }
}
