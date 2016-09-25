<?php

namespace App\Listeners\Records;

use App\Aggregates\Game;
use App\Events\Game\GameAdded;
use App\Events\Game\GameCompleted;
use App\Events\Game\GameEdited;
use App\Events\Game\GameUnCompleted;
use App\Events\Game\PointAdded;
use App\Events\Game\TeamPlayerAdded;
use App\Events\Game\TeamPlayerRemoved;
use App\Infrastructure\Database\IRedisDB;
use App\Listeners\Listener;
use Carbon\Carbon;
use Illuminate\Contracts\Events\Dispatcher;

class CupWinners extends Listener
{
    private $redis;
    private $recordStore;

    CONST BASE_KEY = 'cupWinners';
    private $baseKey = CupWinners::BASE_KEY;

    public function __construct(IRedisDB $redis, RecordStore $recordStore)
    {
        $this->redis = $redis;
        $this->recordStore = $recordStore;
    }
    

    public function onTeamPlayerAdded(TeamPlayerAdded $event)
    {
        $game = $this->redis->hgetall($this->getBaseKey().':game:' . $event->gameId);

        if ($game['playoff'] == 1) {
            $teamA = $this->redis->hgetall($this->baseKey.':season:'.$game['season'].':playoffteam:A');
            $teamB = $this->redis->hgetall($this->baseKey.':season:'.$game['season'].':playoffteam:B');
            $playoffGamesPlayed = $this->redis->hgetall($this->baseKey.':season:'.$game['season'].':playoffgamesplayed:');
            $playoffGamesPlayed[$event->playerId] = $this->getOrDefault($playoffGamesPlayed, $event->playerId, 0) + 1;
            $this->redis->hmset($this->baseKey.':season:'.$game['season'].':playoffgamesplayed:', $playoffGamesPlayed);
            $this->redis->hset($this->baseKey.':season:'.$game['season'].':gamesscene:', $event->gameId, $event->gameId);
            $gamesSceneForSeason = $this->redis->hvals($this->baseKey.':season:'.$game['season'].':gamesscene:');

            $teamFound = false;
            if (isset($teamA[$event->playerId])) {
                $teamFound = 'A';
            } elseif (isset($teamB[$event->playerId])) {
                $teamFound = 'B';
            } elseif (count($gamesSceneForSeason) == 1) {
                $altTeam = 'A';
                $altColour = Game::WHITE_TEAM;
                if ($event->teamColour == $altColour) {
                    $altColour = Game::BLACK_TEAM;
                }
                if ($altTeam == $this->getOrDefault($game, 'team-'.$altColour, $altTeam)) {
                    $altTeam = 'B';
                }
                $teamFound = $this->getOrDefault($game, 'team-'.$event->teamColour, $altTeam);
            } else {
                $this->redis->hset($this->getBaseKey().':season:'.$game['season'] . ':game:' . $game["gameNumber"].':team:'.$event->teamColour.':lostplayers:', $event->playerId, $event->playerId);
            }

            if ($teamFound !== false) {
                $gameWins = $this->redis->hgetall($this->getBaseKey().':season:'.$game['season'].':gamewins:');
                $wins = $this->getOrDefault($gameWins, $teamFound);
                $gameWins[$teamFound] = $wins;
                $this->redis->hmset($this->getBaseKey().':season:'.$game['season'].':gamewins:', $gameWins);

                $this->redis->hset($this->baseKey.':season:'.$game['season'].':playoffteam:'.$teamFound, $event->playerId, $event->playerId);

                $lostPlayers = $this->redis->hvals($this->getBaseKey().':season:'.$game['season'] . ':game:' . $game["gameNumber"].':team:'.$event->teamColour .':lostplayers:');
                foreach ($lostPlayers as $lostPlayer) {
                    $this->redis->hset($this->baseKey.':season:'.$game['season'].':playoffteam:'.$teamFound, $lostPlayer, $lostPlayer);
                }
                $this->redis->del($this->getBaseKey().':season:'.$game['season'] . ':game:' . $game["gameNumber"].':team:'.$event->teamColour .':lostplayers:');
                $this->redis->hset($this->getBaseKey().':game:' . $event->gameId, 'team-'.$event->teamColour, $teamFound);
            }
        }
    }

    public function onTeamPlayerRemoved(TeamPlayerRemoved $event)
    {
        $game = $this->redis->hgetall($this->getBaseKey().':game:' . $event->gameId);

        if ($game['playoff'] == 1) {
            $teamA = $this->redis->hgetall($this->baseKey.':season:'.$game['season'].':playoffteam:A');
            $teamB = $this->redis->hgetall($this->baseKey.':season:'.$game['season'].':playoffteam:B');

            $playoffGamesPlayed = $this->redis->hgetall($this->baseKey.':season:'.$game['season'].':playoffgamesplayed:');
            $newGameCount = $playoffGamesPlayed[$event->playerId] - 1;
            $playoffGamesPlayed[$event->playerId] = $newGameCount;
            $this->redis->hmset($this->baseKey.':season:'.$game['season'].':playoffgamesplayed:', $playoffGamesPlayed);



            if (isset($teamA[$event->playerId])) {
                $teamFound = 'A';
            } elseif (isset($teamB[$event->playerId])) {
                $teamFound = 'B';
            } else {
                $altTeam = 'A';
                $altColour = Game::WHITE_TEAM;
                if ($event->teamColour == $altColour) {
                    $altColour = Game::BLACK_TEAM;
                }
                if ($altTeam == $this->getOrDefault($game, 'team-'.$altColour, $altTeam)) {
                    $altTeam = 'B';
                }
                $teamFound = $this->getOrDefault($game, 'team-'.$event->teamColour, $altTeam);
            }

            //we need to track the player teams UNLESS they have never played a game aside from the game being removed
            if ($newGameCount == 0) {
                $this->redis->hdel($this->baseKey.':season:'.$game['season'].':playoffteam:'.$teamFound, $event->playerId);
            }

            $this->redis->hdel($this->getBaseKey().':game:' . $event->gameId, 'team-'.$event->teamColour);
        }
    }


    public function onGameAdded(GameAdded $event)
    {

        $obj = [];
        $obj["id"] = $event->getAggregateId();
        $obj["playoff"] = $event->playoff;
        $obj["season"] = $event->season;
        $obj["gameNumber"] = $event->gameNumber;

        $this->redis->hmset($this->getBaseKey().':game:' . $event->getAggregateId(), $obj);
        if ($event->playoff == 1) {
            $this->redis->hset($this->getBaseKey().':playoff-seasons:', $event->season, $event->season);
        }
    }

    public function onGameEdited(GameEdited $event)
    {

        $obj = [];
        $obj["id"] = $event->getAggregateId();
        $obj["playoff"] = $event->playoff;
        $obj["season"] = $event->season;
        $obj["gameNumber"] = $event->gameNumber;

        $this->redis->hmset($this->getBaseKey().':game:' . $event->getAggregateId(), $obj);
        if ($event->playoff == 1) {
            $this->redis->hset($this->getBaseKey().':playoff-seasons:', $event->season, $event->season);
        }
    }

    public function onGameCompleted(GameCompleted $event) {

        $game = $this->redis->hgetall($this->getBaseKey().':game:' . $event->gameId);

        if ($game['playoff'] == 1) {

            $seasonGameTrack = $this->redis->hgetall($this->getBaseKey().':season:'.$game['season'].':gamewins:');
            $team = $game['team-'.$event->winningTeam];
            $winCount = $this->getOrDefault($seasonGameTrack, $team) + 1;
            $seasonGameTrack[$team] = $winCount;
            $this->redis->hmset($this->getBaseKey().':season:'.$game['season'].':gamewins:',$seasonGameTrack);
            if ($winCount >= 4) {
                $this->redis->hset($this->getBaseKey().':season-winner:', $game['season'], $team);

                $this->storeRecord();
            }
        }
    }

    public function onGameUnCompleted(GameUnCompleted $event)
    {
        $game = $this->redis->hgetall($this->getBaseKey().':game:' . $event->gameId);

        if ($game['playoff'] == 1) {

            $seasonGameTrack = $this->redis->hgetall($this->getBaseKey().':season:'.$game['season'].':gamewins:');
            $team = $game['team-'.$event->winningTeam];
            $priorWinCount = $this->getOrDefault($seasonGameTrack, $team);
            $winCount = $priorWinCount - 1;
            $seasonGameTrack[$team] = $winCount;
            $this->redis->hmset($this->getBaseKey().':season:'.$game['season'].':gamewins:',$seasonGameTrack);
            if ($priorWinCount = 4) {
                $this->redis->hdel($this->getBaseKey().':season-winner:', $game['season']);
                $teamPlayers = $this->redis->hvals($this->baseKey.':season:'.$game['season'].':playoffteam:'.$team);
                $this->recordStore->unsetRecord($this->baseKey.str_pad($game['season'], 2, "0", STR_PAD_LEFT),$teamPlayers);
            }
        }
    }


    private function storeRecord()
    {
        $seasons = $this->redis->hvals($this->getBaseKey().':playoff-seasons:');

        foreach ($seasons as $season) {
            $team = $this->redis->hget($this->getBaseKey().':season-winner:', $season);
            $otherTeam = 'A';
            if ($team == $otherTeam) {
                $otherTeam = 'B';
            }
            $seasonGameTrack = $this->redis->hgetall($this->getBaseKey().':season:'.$season.':gamewins:');
            $teamPlayers = $this->redis->hgetall($this->baseKey.':season:'.$season.':playoffteam:'.$team);
            $recordEntries = [];
            /** Paul Rajotte (Season 1, 4-3) */
            foreach ($teamPlayers as $teamPlayer) {
                $recordEntries[] = [
                    'entry' =>   RecordStore::playerEncode($teamPlayer) ." (".RecordStore::seasonEncode($season).", ".$seasonGameTrack[$team]."-".$seasonGameTrack[$otherTeam].")",
                    'playerKeys' => [$teamPlayer]
                ];

            }
            $this->recordStore->setRecord($this->baseKey.str_pad($season, 2, "0", STR_PAD_LEFT), 'Cup Winners: Season '.$season, $recordEntries);
        }
    }


    public function subscribe(Dispatcher $events)
    {
        $events->listen(
            [
                GameCompleted::class,
                GameAdded::class,
                TeamPlayerAdded::class,
                TeamPlayerRemoved::class,
                GameUnCompleted::class,
                GameEdited::class
            ],
            CupWinners::class . '@handleEvent'
        );
    }
}
