<?php

namespace App\Infrastructure\Test;


use App\Aggregates\Game;
use App\Infrastructure\Aggregate\AggregateRepository;
use App\Infrastructure\Aggregate\IAggregateRepository;
use App\Infrastructure\AggregateFactory;
use App\Infrastructure\Database\InMemoryRedisDB;
use App\Infrastructure\Event\EventStore\InMemoryEventStore;
use App\Infrastructure\Event\TestEventBus;
use App\Infrastructure\Event\TestEventUser;
use App\Infrastructure\Logger;
use App\Infrastructure\Util\Encrypt\IKeyRepo;
use App\Infrastructure\Util\Encrypt\MockKeyRepo;

use App\Listeners\Games;
use App\Listeners\GoalieStats;
use App\Listeners\Players;
use App\Listeners\PlayerStats;
use App\Listeners\Records\AssistClub;
use App\Listeners\Records\CupWinners;
use App\Listeners\Records\GoalClub;
use App\Listeners\Records\LeastGoalsInARegularSeasonGame;
use App\Listeners\Records\LongestRegularSeasonGame;
use App\Listeners\Records\MostAssistsInARegularSeason;
use App\Listeners\Records\MostGoalsInARegularSeason;
use App\Listeners\Records\MostGoalsInARegularSeasonGame;
use App\Listeners\Records\MostPointsInARegularSeason;
use App\Listeners\Records\PointClub;
use App\Listeners\Records\RecordStore;
use App\Listeners\Records\RegularSeasonOvertimeGames;
use App\Listeners\Records\RegularSeasonShutOutClub;
use App\Listeners\Records\ShortestRegularSeasonGame;
use App\Listeners\Records\TeamRegularSeasonRecord;
use PHPUnit_Framework_TestCase;

class TestCaseCore extends PHPUnit_Framework_TestCase
{

    /** @var  IAggregateRepository */
    public $aggregateRepository;
    
    /** @var  InMemoryEventStore */
    private $eventStore;

    private $eventBus;
    /** @var  InMemoryRedisDB */
    private $redis;

    /**
     * @var Players
     */
    public $players;

    /**
     * @var Games
     */
    public $games;

    /**
     * @var PlayerStats
     */
    public $stats;


    /**
     * @var GoalieStats
     */
    public $goalieStats;

    /**
     * @var RecordStore
     */
    public $recordStore;

    /**
     * @var GoalClub
     */
    public $goalClub;

    /**
     * @var AssistClub
     */
    public $assistClub;

    /**
     * @var PointClub
     */
    public $pointClub;

    /**
     * @var TeamRegularSeasonRecord
     */
    public $teamRegularSeasonRecord;

    /**
     * @var RegularSeasonOvertimeGames
     */
    public $regularSeasonOvertimeRecord;

    /**
     * @var LongestRegularSeasonGame
     */
    public $longestRegularSeasonGame;


    /**
     * @var ShortestRegularSeasonGame
     */
    public $shortestRegularSeasonGame;

    /** @var  MostGoalsInARegularSeasonGame */
    public $mostGoalsInARegularSeasonGame;

    /** @var  LeastGoalsInARegularSeasonGame */
    public $leastGoalsInARegularSeasonGame;

    /** @var  MostPointsInARegularSeason */
    public $mostPointsInARegularSeason;

    /** @var  MostGoalsInARegularSeason */
    public $mostGoalsInARegularSeason;

    /** @var  MostAssistsInARegularSeason */
    public $mostAssistsInARegularSeason;

    /** @var  RegularSeasonShutOutClub */
    public $regularSeasonShutOutClub;

    /** @var  CupWinners */
    public $cupWinners;

    private $has_been_setup = false;


    public function tearDown()
    {
        $this->redis->test_reset();
        $this->eventStore->test_reset();
    }


    /**
     *
     */
    public function setUp()
    {
        if (!$this->has_been_setup) {
            $this->redis = new InMemoryRedisDB();
            $this->eventStore = new InMemoryEventStore();
            $this->eventBus = new TestEventBus();

            $this->aggregateRepository = new AggregateRepository(
                $this->eventStore,
                $this->eventBus
            );

            $this->players = new Players($this->redis);
            $this->eventBus->register($this->players);
            $this->recordStore = new RecordStore($this->redis, $this->players);

            $this->goalClub = new GoalClub($this->redis, $this->recordStore);
            $this->eventBus->register($this->goalClub);

            $this->assistClub = new AssistClub($this->redis, $this->recordStore);
            $this->eventBus->register($this->assistClub);

            $this->pointClub = new PointClub($this->redis, $this->recordStore);
            $this->eventBus->register($this->pointClub);




            $this->games = new Games($this->redis);
            $this->eventBus->register($this->games);

            $this->stats = new PlayerStats($this->redis);
            $this->eventBus->register($this->stats);

            $this->goalieStats = new GoalieStats($this->redis);
            $this->eventBus->register($this->goalieStats);

            $this->teamRegularSeasonRecord = new TeamRegularSeasonRecord($this->redis, $this->recordStore);
            $this->eventBus->register($this->teamRegularSeasonRecord);

            $this->regularSeasonOvertimeRecord = new RegularSeasonOvertimeGames($this->redis, $this->recordStore);
            $this->eventBus->register($this->regularSeasonOvertimeRecord);

            $this->longestRegularSeasonGame = new LongestRegularSeasonGame($this->redis, $this->recordStore);
            $this->eventBus->register($this->longestRegularSeasonGame);

            $this->shortestRegularSeasonGame = new ShortestRegularSeasonGame($this->redis, $this->recordStore);
            $this->eventBus->register($this->shortestRegularSeasonGame);

            $this->mostGoalsInARegularSeasonGame = new MostGoalsInARegularSeasonGame($this->redis, $this->recordStore);
            $this->eventBus->register($this->mostGoalsInARegularSeasonGame);

            $this->leastGoalsInARegularSeasonGame = new LeastGoalsInARegularSeasonGame($this->redis, $this->recordStore);
            $this->eventBus->register($this->leastGoalsInARegularSeasonGame);

            $this->mostPointsInARegularSeason = new MostPointsInARegularSeason($this->redis, $this->recordStore);
            $this->eventBus->register($this->mostPointsInARegularSeason);

            $this->mostGoalsInARegularSeason = new MostGoalsInARegularSeason($this->redis, $this->recordStore);
            $this->eventBus->register($this->mostGoalsInARegularSeason);

            $this->mostAssistsInARegularSeason = new MostAssistsInARegularSeason($this->redis, $this->recordStore);
            $this->eventBus->register($this->mostAssistsInARegularSeason);

            $this->regularSeasonShutOutClub = new RegularSeasonShutOutClub($this->redis, $this->recordStore);
            $this->eventBus->register($this->regularSeasonShutOutClub);

            $this->cupWinners = new CupWinners($this->redis, $this->recordStore);
            $this->eventBus->register($this->cupWinners);

            Logger::$should_log = false;

            $this->has_been_setup = true;



        }

    }

    protected function dispatch($job)
    {
        $jobClass = get_class($job);
        $id = "";
        $id = $job->handle($this->aggregateRepository);

        return $id;
    }
}
