<?php
/**
 * Created by PhpStorm.
 * User: pauleverton
 * Date: 2016-08-10
 * Time: 6:21 PM
 */
namespace App\Listeners\Records;

use App\Infrastructure\Database\IRedisDB;
use App\Jobs\PurgeUrl;
use App\Listeners\Players;
use Illuminate\Foundation\Bus\DispatchesJobs;

class RecordStore
{
    use DispatchesJobs;
    private $redis;
    private $players;

    public function __construct(IRedisDB $redis, Players $players)
    {
        $this->redis = $redis;
        $this->players = $players;
    }

    public function setRecord($recordKey, $header, $values)
    {
        $this->redis->hset('records:headers', $recordKey, $header);

        $recordEntries = $this->redis->hgetall('record:'.$recordKey);

        $players = $this->players->getAll();

        foreach ($players as $player) {
            foreach ($recordEntries as $recordEntryKey => $entry) {
                $this->redis->hdel('player-records:'.$player['id'], $recordKey);
            }
        }

        $this->redis->del('record:'.$recordKey);

        $i = 0;
        foreach ($values as $recordEntry) {
            $i = $i + 1;
            $this->redis->hset('record:'.$recordKey, $recordKey.':'.$i, $recordEntry['entry']);
            foreach ($recordEntry['playerKeys'] as $playerKey) {
                $this->redis->hset('player-records:'.$playerKey, $recordKey.':'.$i, $recordKey);
            }
        }
    }

    public function unsetRecord($recordKey, $players)
    {
        $this->redis->hdel('records:headers', $recordKey);
        $recordKeys = $this->redis->hgetall('record:'.$recordKey);

        $this->redis->del('record:'.$recordKey);

        foreach ($recordKeys as $key => $entry) {

            foreach ($players as $playerKey) {
                $this->redis->hdel('player-records:'.$playerKey, $key);
            }
        }
    }

    public function getPlayerRecords($playerId)
    {
        $records = [];

        $obj = $this->redis->hgetall('records:headers');
        $playerRecords = $this->redis->hgetall('player-records:'.$playerId);



        foreach ($obj as $recordKey => $header) {
            $entries = [];
            $allEntries = $this->redis->hgetall('record:'.$recordKey);
            foreach ($allEntries as $entryKey => $entryValue) {
                if (isset($playerRecords[$entryKey])) {
                    $entries[] = $entryValue;
                }
            }

            if (count($entries) > 0) {
                $records[$recordKey] = [
                    'recordKey' => $recordKey,
                    'header' => $header,
                    'entries' => $entries,
                    'parsedEntries' => $this->parseEntries($entries)
                ];
            }
        }

        $records = array_sort($records, function ($value) {
            return $value['recordKey'];
        });

        return $records;
    }


    public function getRecords()
    {
        $records = [];

        $obj = $this->redis->hgetall('records:headers');

        foreach ($obj as $recordKey => $header) {
            $records[$recordKey] = [
                'recordKey' => $recordKey,
                'header' => $header,
                'entries' => $this->redis->hvals('record:'.$recordKey),
                'parsedEntries' => $this->parseEntries($this->redis->hvals('record:'.$recordKey))
            ];
        }

        $records = array_sort($records, function ($value) {
            return $value['recordKey'];
        });

        return $records;
    }


    public static function playerEncode($playerId)
    {
        return "[playerId=".$playerId."]";
    }
    public static function seasonEncode($season)
    {
        return "[season=".$season."]";
    }
    public static function gameEncode($gameId, $gameNum)
    {
        return "[gameId=".$gameId." gameNumber=".$gameNum."]";
    }

    private function parseEntries($entries)
    {
        $returnEntries = [];
        foreach ($entries as $entry) {
            $returnRecord = $entry;
            if (preg_match('(\[playerId=([a-zA-Z0-9\-]+)\])', $returnRecord, $matches)) {
                $playerId = $matches[1];
                $player = $this->players->getById($playerId);
                $playerLink = "<a href='players/".$player['id']."/stats/player'>".$player['firstName'].' '.$player['lastName']."</a>";
                $returnRecord = str_replace($matches[0], $playerLink, $returnRecord);
            }

            if (preg_match('(\[playerId=([a-zA-Z0-9\-]+)\])', $returnRecord, $matches)) {
                $playerId = $matches[1];
                $player = $this->players->getById($playerId);
                $playerLink = "<a href='players/".$player['id']."/stats/player'>".$player['firstName'].' '.$player['lastName']."</a>";
                $returnRecord = str_replace($matches[0], $playerLink, $returnRecord);
            }

            if (preg_match('(\[season=([0-9]+)\])', $returnRecord, $matches)) {
                $seasonId = $matches[1];
                $returnRecord = str_replace($matches[0], 'Season '.$seasonId, $returnRecord);
            }

            if (preg_match('(\[gameId=([a-zA-Z0-9\-]+) gameNumber=([0-9]+)\])', $returnRecord, $matches)) {
                $gameId = $matches[1];
                $gameNum = $matches[2];
                $returnRecord = str_replace($matches[0], 'Game '.$gameNum, $returnRecord);
            }
            $returnEntries[] = $returnRecord;
        }
        return $returnEntries;
    }


}
