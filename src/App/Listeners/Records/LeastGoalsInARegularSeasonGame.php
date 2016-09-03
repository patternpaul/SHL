<?php

namespace App\Listeners\Records;

use App\Aggregates\Game;
use App\Events\Game\GameAdded;
use App\Events\Game\GameCompleted;
use App\Events\Game\PointAdded;
use App\Events\Game\TeamPlayerAdded;
use App\Infrastructure\Database\IRedisDB;
use App\Listeners\Listener;
use Carbon\Carbon;
use Illuminate\Contracts\Events\Dispatcher;

class LeastGoalsInARegularSeasonGame extends Listener
{
    private $redis;
    private $recordStore;

    CONST BASE_KEY = 'leastGoalsInARegularSeasonGame';
    private $baseKey = LeastGoalsInARegularSeasonGame::BASE_KEY;

    public function __construct(IRedisDB $redis, RecordStore $recordStore)
    {
        $this->redis = $redis;
        $this->recordStore = $recordStore;
    }
    
    public function onTeamPlayerAdded(TeamPlayerAdded $event)
    {
        if ($event->position == Game::GOALIE) {
            $obj = [];
            $obj[$event->teamColour."Goalie"] = $event->playerId;

            $this->redis->hmset($this->getBaseKey() . ':game:' . $event->getAggregateId(), $obj);
        }
    }

    public function onGameAdded(GameAdded $event)
    {
        $obj = [];
        $obj["season"] = $event->season;
        $obj["gameNumber"] = $event->gameNumber;


        $this->redis->hmset($this->getBaseKey() . ':game:' . $event->getAggregateId(), $obj);
        $this->redis->hset($this->getBaseKey().':seasons:', $event->season, $event->season);
    }


    public function onGameCompleted(GameCompleted $event) {

        $game = [];
        $game[Game::BLACK_TEAM.'Points'] = $event->blackPointTotal;
        $game[Game::WHITE_TEAM.'Points'] = $event->whitePointTotal;
        $game['maxGoals'] = $event->winningPoint;
        $game["winningTeam"] = $event->winningTeam;

        $minGoals = $game[Game::BLACK_TEAM.'Points'];
        if ($game[Game::WHITE_TEAM.'Points'] < $game[Game::BLACK_TEAM.'Points']) {
            $minGoals = $game[Game::WHITE_TEAM.'Points'];
        }

        $this->redis->hmset($this->getBaseKey() . ':game:' . $event->gameId, $game);

        $obj = $this->redis->hgetall('leastGoalsInARegularSeasonGame');

        if (!isset($obj['minGoals']) || $minGoals < $this->getOrDefault($obj, 'minGoals')) {
            $this->redis->del('leastGoalsInARegularSeasonGame:gameIds');
            $this->redis->hset('leastGoalsInARegularSeasonGame','minGoals', $minGoals);
        }

        if (!isset($obj['minGoals']) || $minGoals <= $this->getOrDefault($obj, 'minGoals')) {
            $this->redis->hset('leastGoalsInARegularSeasonGame:gameIds', $event->gameId, $event->gameId);
        }


        $this->storeRecord();
    }


    private function storeRecord()
    {
        $mostGoals = $this->redis->hgetall('leastGoalsInARegularSeasonGame');
        $games = $this->redis->hvals('leastGoalsInARegularSeasonGame:gameIds');

        $recordEntries = [];
        foreach ($games as $gameId) {
            $game = $this->redis->hgetall($this->getBaseKey() . ':game:' . $gameId);

            $score = $game[Game::BLACK_TEAM.'Points'] . '-' . $game[Game::WHITE_TEAM.'Points'] . ' for Colored Team';
            if ($game[Game::BLACK_TEAM.'Points'] < $game[Game::WHITE_TEAM.'Points']) {
                $score = $game[Game::WHITE_TEAM.'Points'] . '-' . $game[Game::BLACK_TEAM.'Points'] . ' for Colored Team';
            }

            $recordEntries[] = [
                'entry' =>   RecordStore::seasonEncode($game['season']).": ".RecordStore::gameEncode($gameId,$game["gameNumber"])." - " .
                    $mostGoals['minGoals'] . ' Goals. '. $score . '. Goalies: ('.RecordStore::playerEncode($game[Game::WHITE_TEAM."Goalie"]).
                ' white vs. '.RecordStore::playerEncode($game[Game::BLACK_TEAM."Goalie"]).' colored)',
                'playerKeys' => [$game[Game::WHITE_TEAM."Goalie"], $game[Game::BLACK_TEAM."Goalie"]]
            ];
        }
        $this->recordStore->setRecord($this->baseKey, 'Least Goals In A Regular Season Game', $recordEntries);
    }


    public function subscribe(Dispatcher $events)
    {
        $events->listen(
            [
                GameCompleted::class,
                GameAdded::class,
                TeamPlayerAdded::class
            ],
            LeastGoalsInARegularSeasonGame::class . '@handleEvent'
        );
    }

}
