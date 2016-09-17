<?php
/**
 * Created by PhpStorm.
 * User: pauleverton
 * Date: 2016-08-21
 * Time: 8:45 AM
 */
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Listeners\GoalieStats;
use App\Listeners\PlayerStats;

class ApiStatsController extends Controller
{
    private $playerStats;
    
    /**
     * DemoController constructor.
     * @param $playerStats
     */
    public function __construct(PlayerStats $playerStats, GoalieStats $goalieStats)
    {
        $this->playerStats = $playerStats;
        $this->goalieStats = $goalieStats;
    }


    public function playerStatsSelected($season, $playoff)
    {
        $stats = $this->playerStats->getCalcStatLines($season, $playoff);
        return response()->json(array_values($stats));
    }
}
