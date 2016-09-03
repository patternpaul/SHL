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

class PlayerController extends Controller
{
    private $playerStats;
    private $goalieStats;
    private $players;
    private $records;
    private $games;
    public function __construct(PlayerStats $playerStats, GoalieStats $goalieStats, Players $players, RecordStore $records, Games $games)
    {
        $this->playerStats = $playerStats;
        $this->goalieStats = $goalieStats;
        $this->players = $players;
        $this->records = $records;
        $this->games = $games;
        $viewGames = array_reverse($this->games->getLatestSeasonGames());
        view()->share('header_games', $viewGames);
    }

    public function index() {
        return redirect()->route('player-stats');
    }

    public function playerStats($playerId)
    {
        $stats = $this->playerStats->getPlayerStats($playerId, 0);
        $playoffStats = $this->playerStats->getPlayerStats($playerId, 1);
        $player = $this->players->getById($playerId);
        return view('app.players.stats.player', ["regStats" => $stats, 'playoffStats' => $playoffStats, "player" => $player]);
    }

    public function goalieStats($playerId)
    {
        $stats = $this->goalieStats->getGoalieStats($playerId, 0);
        $playoffStats = $this->goalieStats->getGoalieStats($playerId, 1);
        $player = $this->players->getById($playerId);
        return view('app.players.stats.goalie', ["regStats" => $stats, 'playoffStats' => $playoffStats, "player" => $player]);
    }

    public function records($playerId)
    {
        $player = $this->players->getById($playerId);
        $records = $this->records->getPlayerRecords($playerId);
        return view('app.players.records', ["records" => $records, "player" => $player]);
    }
}
