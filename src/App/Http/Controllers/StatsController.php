<?php
/**
 * Created by PhpStorm.
 * User: pauleverton
 * Date: 2016-08-21
 * Time: 8:45 AM
 */
namespace App\Http\Controllers;

use App\Listeners\Games;
use App\Listeners\GoalieStats;
use App\Listeners\PlayerStats;
use Illuminate\Support\Facades\Input;

class StatsController extends Controller
{
    private $playerStats;
    private $goalieStats;
    private $games;

    /**
     * DemoController constructor.
     * @param $playerStats
     */
    public function __construct(PlayerStats $playerStats, GoalieStats $goalieStats, Games $games)
    {
        $this->playerStats = $playerStats;
        $this->goalieStats = $goalieStats;
        $this->games = $games;
        $viewGames = array_reverse($this->games->getLatestSeasonGames());
        view()->share('header_games', $viewGames);
        view()->share('active_navbar_stats', true);
    }

    public function index() {
        return redirect()->route('player-stats');
    }

    public function playerStats()
    {
        return $this->basePlayerStats($this->games->getLatestSeason(), '0');
    }

    public function goalieStats()
    {
        return $this->baseGoalieStats($this->games->getLatestSeason(), '0');
    }

    public function playerStatsSelected($season, $playoff)
    {
        return $this->basePlayerStats(intval($season), $playoff);
    }

    public function goalieStatsSelected($season, $playoff)
    {
        return $this->baseGoalieStats(intval($season), $playoff);
    }

    public function basePlayerStats($season, $playoff)
    {
        $stats = $this->playerStats->getCalcStatLines($season, $playoff);

        return view('app.stats.players.index', ["stats" => $stats, "season" => $season, "playoffs" => $playoff]);
    }

    public function baseGoalieStats($season, $playoff)
    {
        $stats = $this->goalieStats->getCalcStatLines($season, $playoff);

        return view('app.stats.goalies.index', ["stats" => $stats, "season" => $season, "playoffs" => $playoff]);
    }
}
