<?php

use App\Aggregates\Game;
use App\Commands\Player\AddPlayer;

class GoalieStatsTest extends \App\Infrastructure\Test\TestCaseCore
{

    public $chrisLee;
    public $zachR;
    public $kevenB;
    public $ghislainD;
    public $paulE;
    public $paulG;
    public $davidR;
    public $chrisR;
    public $jeremieR;
    public $jacquesAuger;
    public $colinLemoine;

    public function setUp()
    {
        parent::setUp(); // TODO: Change the autogenerated stub


        $this->chrisLee = $this->genPlayerWithName("Chris", "Lee");
        $this->zachR = $this->genPlayerWithName("Zach", "Riet");
        $this->kevenB = $this->genPlayerWithName("Keven", "Barron");
        $this->ghislainD = $this->genPlayerWithName("Ghislain", "Druwé");
        $this->paulE = $this->genPlayerWithName("Paul", "Everton");
        $this->paulG = $this->genPlayerWithName("Paul", "Gagne");
        $this->davidR = $this->genPlayerWithName("David", "Robin");
        $this->chrisR = $this->genPlayerWithName("Chris", "Robin");
        $this->jeremieR  = $this->genPlayerWithName("Jeremie", "Robin");
        $this->jacquesAuger = $this->genPlayerWithName("Jacques", "Auger");
        $this->colinLemoine = $this->genPlayerWithName("Colin", "Lemoine");
    }

    public function test_game_scenario()
    {
        $season = 1;
        $gameCount = 50;
        $playoff = 0;
        $this->generateMultipleGamesForAGivenSeason($season, $gameCount, $playoff);
        $statsLine = $this->goalieStats->getCalcGoalieStatLine($this->chrisLee, $season, $playoff);



        $this->assertEquals(500, $statsLine['goalsAgainst']);
        $this->assertEquals(50, $statsLine['gamesPlayed']);
        $this->assertEquals(10, $statsLine['goalsAgainstAverage']);
        $this->assertEquals(0, $statsLine['wins']);
        $this->assertEquals(50, $statsLine['losses']);
        $this->assertEquals(-50, $statsLine['plusMinus']);
        $this->assertEquals(0, $statsLine['winPercentage']);
        $this->assertEquals(0, $statsLine['goals']);
        $this->assertEquals(0, $statsLine['assists']);
        $this->assertEquals(0, $statsLine['points']);
        $this->assertEquals(0, $statsLine['shutOuts']);
        $this->assertEquals(1500, $statsLine['minutesPlayed']);
        $this->assertEquals(0.33, $statsLine['goalsPerMinute']);


        $statsLine = $this->goalieStats->getCalcGoalieStatLine($this->davidR, $season, $playoff);

        $this->assertEquals(0, $statsLine['goalsAgainst']);
        $this->assertEquals(50, $statsLine['gamesPlayed']);
        $this->assertEquals(0, $statsLine['goalsAgainstAverage']);
        $this->assertEquals(50, $statsLine['wins']);
        $this->assertEquals(0, $statsLine['losses']);
        $this->assertEquals(50, $statsLine['plusMinus']);
        $this->assertEquals(100, $statsLine['winPercentage']);
        $this->assertEquals(0, $statsLine['goals']);
        $this->assertEquals(50, $statsLine['assists']);
        $this->assertEquals(50, $statsLine['points']);
        $this->assertEquals(50, $statsLine['shutOuts']);
        $this->assertEquals(1500, $statsLine['minutesPlayed']);
        $this->assertEquals(0, $statsLine['goalsPerMinute']);
    }


    public function test_player_not_in_stats_scenario()
    {
        $season = 1;
        $gameCount = 50;
        $playoff = 0;
        $this->generateMultipleGamesForAGivenSeason($season, $gameCount, $playoff);
        $statsLines = $this->goalieStats->getCalcStatLines('all', 'all');

        $this->assertTrue(!isset($statsLines[$this->jacquesAuger]));
    }


    public function test_multi_season_scenario()
    {
        $season = 1;
        $secondSeason = 2;
        $gameCount = 50;
        $playoff = 0;
        $this->generateMultipleGamesForAGivenSeason($season, $gameCount, $playoff);
        $this->generateMultipleGamesForAGivenSeason($season, 4, '1');
        $this->generateMultipleGamesForAGivenSeason($secondSeason, $gameCount, $playoff);

        $statsLines = $this->goalieStats->getCalcStatLines('all', 'all');

        $statsLine = $statsLines[$this->chrisLee];

        $this->assertEquals(1040, $statsLine['goalsAgainst']);
        $this->assertEquals(104, $statsLine['gamesPlayed']);
        $this->assertEquals(10, $statsLine['goalsAgainstAverage']);
        $this->assertEquals(0, $statsLine['wins']);
        $this->assertEquals(104, $statsLine['losses']);
        $this->assertEquals(-104, $statsLine['plusMinus']);
        $this->assertEquals(0, $statsLine['winPercentage']);
        $this->assertEquals(0, $statsLine['goals']);
        $this->assertEquals(0, $statsLine['assists']);
        $this->assertEquals(0, $statsLine['points']);
        $this->assertEquals(0, $statsLine['shutOuts']);
        $this->assertEquals(3120, $statsLine['minutesPlayed']);
        $this->assertEquals(0.33, $statsLine['goalsPerMinute']);


        $statsLine = $statsLines[$this->davidR];

        $this->assertEquals(0, $statsLine['goalsAgainst']);
        $this->assertEquals(104, $statsLine['gamesPlayed']);
        $this->assertEquals(0, $statsLine['goalsAgainstAverage']);
        $this->assertEquals(104, $statsLine['wins']);
        $this->assertEquals(0, $statsLine['losses']);
        $this->assertEquals(104, $statsLine['plusMinus']);
        $this->assertEquals(100, $statsLine['winPercentage']);
        $this->assertEquals(0, $statsLine['goals']);
        $this->assertEquals(104, $statsLine['assists']);
        $this->assertEquals(104, $statsLine['points']);
        $this->assertEquals(104, $statsLine['shutOuts']);
        $this->assertEquals(3120, $statsLine['minutesPlayed']);
        $this->assertEquals(0, $statsLine['goalsPerMinute']);


        $statsLines = $this->goalieStats->getCalcStatLines($season, 'all');

        $statsLine = $statsLines[$this->chrisLee];

        $this->assertEquals(540, $statsLine['goalsAgainst']);
        $this->assertEquals(54, $statsLine['gamesPlayed']);
        $this->assertEquals(10, $statsLine['goalsAgainstAverage']);
        $this->assertEquals(0, $statsLine['wins']);
        $this->assertEquals(54, $statsLine['losses']);
        $this->assertEquals(-54, $statsLine['plusMinus']);
        $this->assertEquals(0, $statsLine['winPercentage']);
        $this->assertEquals(0, $statsLine['goals']);
        $this->assertEquals(0, $statsLine['assists']);
        $this->assertEquals(0, $statsLine['points']);
        $this->assertEquals(0, $statsLine['shutOuts']);
        $this->assertEquals(1620, $statsLine['minutesPlayed']);
        $this->assertEquals(0.33, $statsLine['goalsPerMinute']);

        $statsLine = $statsLines[$this->davidR];

        $this->assertEquals(0, $statsLine['goalsAgainst']);
        $this->assertEquals(54, $statsLine['gamesPlayed']);
        $this->assertEquals(0, $statsLine['goalsAgainstAverage']);
        $this->assertEquals(54, $statsLine['wins']);
        $this->assertEquals(0, $statsLine['losses']);
        $this->assertEquals(54, $statsLine['plusMinus']);
        $this->assertEquals(100, $statsLine['winPercentage']);
        $this->assertEquals(0, $statsLine['goals']);
        $this->assertEquals(54, $statsLine['assists']);
        $this->assertEquals(54, $statsLine['points']);
        $this->assertEquals(54, $statsLine['shutOuts']);
        $this->assertEquals(1620, $statsLine['minutesPlayed']);
        $this->assertEquals(0, $statsLine['goalsPerMinute']);

        $statsLines = $this->goalieStats->getCalcStatLines($season, 0);

        $statsLine = $statsLines[$this->chrisLee];

        $this->assertEquals(500, $statsLine['goalsAgainst']);
        $this->assertEquals(50, $statsLine['gamesPlayed']);
        $this->assertEquals(10, $statsLine['goalsAgainstAverage']);
        $this->assertEquals(0, $statsLine['wins']);
        $this->assertEquals(50, $statsLine['losses']);
        $this->assertEquals(-50, $statsLine['plusMinus']);
        $this->assertEquals(0, $statsLine['winPercentage']);
        $this->assertEquals(0, $statsLine['goals']);
        $this->assertEquals(0, $statsLine['assists']);
        $this->assertEquals(0, $statsLine['points']);
        $this->assertEquals(0, $statsLine['shutOuts']);
        $this->assertEquals(1500, $statsLine['minutesPlayed']);
        $this->assertEquals(0.33, $statsLine['goalsPerMinute']);


        $statsLine = $statsLines[$this->davidR];

        $this->assertEquals(0, $statsLine['goalsAgainst']);
        $this->assertEquals(50, $statsLine['gamesPlayed']);
        $this->assertEquals(0, $statsLine['goalsAgainstAverage']);
        $this->assertEquals(50, $statsLine['wins']);
        $this->assertEquals(0, $statsLine['losses']);
        $this->assertEquals(50, $statsLine['plusMinus']);
        $this->assertEquals(100, $statsLine['winPercentage']);
        $this->assertEquals(0, $statsLine['goals']);
        $this->assertEquals(50, $statsLine['assists']);
        $this->assertEquals(50, $statsLine['points']);
        $this->assertEquals(50, $statsLine['shutOuts']);
        $this->assertEquals(1500, $statsLine['minutesPlayed']);
        $this->assertEquals(0, $statsLine['goalsPerMinute']);


        $statsLines = $this->goalieStats->getCalcStatLines($season, 1);

        $statsLine = $statsLines[$this->chrisLee];

        $this->assertEquals(40, $statsLine['goalsAgainst']);
        $this->assertEquals(4, $statsLine['gamesPlayed']);
        $this->assertEquals(10, $statsLine['goalsAgainstAverage']);
        $this->assertEquals(0, $statsLine['wins']);
        $this->assertEquals(4, $statsLine['losses']);
        $this->assertEquals(-4, $statsLine['plusMinus']);
        $this->assertEquals(0, $statsLine['winPercentage']);
        $this->assertEquals(0, $statsLine['goals']);
        $this->assertEquals(0, $statsLine['assists']);
        $this->assertEquals(0, $statsLine['points']);
        $this->assertEquals(0, $statsLine['shutOuts']);
        $this->assertEquals(120, $statsLine['minutesPlayed']);
        $this->assertEquals(0.33, $statsLine['goalsPerMinute']);


        $statsLine = $statsLines[$this->davidR];

        $this->assertEquals(0, $statsLine['goalsAgainst']);
        $this->assertEquals(4, $statsLine['gamesPlayed']);
        $this->assertEquals(0, $statsLine['goalsAgainstAverage']);
        $this->assertEquals(4, $statsLine['wins']);
        $this->assertEquals(0, $statsLine['losses']);
        $this->assertEquals(4, $statsLine['plusMinus']);
        $this->assertEquals(100, $statsLine['winPercentage']);
        $this->assertEquals(0, $statsLine['goals']);
        $this->assertEquals(4, $statsLine['assists']);
        $this->assertEquals(4, $statsLine['points']);
        $this->assertEquals(4, $statsLine['shutOuts']);
        $this->assertEquals(120, $statsLine['minutesPlayed']);
        $this->assertEquals(0, $statsLine['goalsPerMinute']);
    }


    public function test_edit_game()
    {
        $season = 1;
        $gameCount = 50;
        $playoff = 0;
        $gameDate = '2016-07-27';
        $start = '9:00 AM';
        $end = '9:30 AM';
        $lastGameId = $this->generateMultipleGamesForAGivenSeason($season, $gameCount, $playoff);

        $command = new \App\Commands\Game\EditFullGame(
            $lastGameId,
            $gameDate,
            $start,
            $end,
            $playoff,
            $season,
            50
        );


        $command->addWhiteGoalie($this->chrisLee);
        $command->addWhitePlayer($this->zachR);
        $command->addWhitePlayer($this->kevenB);
        $command->addWhitePlayer($this->ghislainD);
        $command->addWhitePlayer($this->paulE);
        $command->addWhitePlayer($this->paulG);


        $command->addBlackGoalie($this->davidR);
        $command->addBlackPlayer($this->chrisR);
        $command->addBlackPlayer($this->jeremieR);
        $command->addBlackPlayer($this->jacquesAuger);
        $command->addBlackPlayer($this->colinLemoine);


        $command->addWhitePoint(1, $this->paulG, $this->paulE);
        $command->addWhitePoint(2, $this->paulG, $this->paulE);
        $command->addWhitePoint(3, $this->paulG, $this->paulE);
        $command->addWhitePoint(4, $this->paulG, $this->paulE);
        $command->addWhitePoint(5, $this->paulG, '');
        $command->addWhitePoint(6, $this->paulG, $this->paulE);
        $command->addWhitePoint(7, $this->paulG, $this->paulE);
        $command->addWhitePoint(8, $this->paulG, $this->paulE);
        $command->addWhitePoint(9, $this->paulG, $this->paulE);
        $command->addWhitePoint(10, $this->paulG, $this->paulE);

        $this->dispatch($command);



        $statsLine = $this->goalieStats->getCalcGoalieStatLine($this->chrisLee, $season, $playoff);



        $this->assertEquals(490, $statsLine['goalsAgainst']);
        $this->assertEquals(50, $statsLine['gamesPlayed']);
        $this->assertEquals(9.8, $statsLine['goalsAgainstAverage']);
        $this->assertEquals(1, $statsLine['wins']);
        $this->assertEquals(49, $statsLine['losses']);
        $this->assertEquals(-48, $statsLine['plusMinus']);
        $this->assertEquals(2, $statsLine['winPercentage']);
        $this->assertEquals(0, $statsLine['goals']);
        $this->assertEquals(0, $statsLine['assists']);
        $this->assertEquals(0, $statsLine['points']);
        $this->assertEquals(1, $statsLine['shutOuts']);
        $this->assertEquals(1500, $statsLine['minutesPlayed']);
        $this->assertEquals(0.33, $statsLine['goalsPerMinute']);


        $statsLine = $this->goalieStats->getCalcGoalieStatLine($this->davidR, $season, $playoff);

        $this->assertEquals(10, $statsLine['goalsAgainst']);
        $this->assertEquals(50, $statsLine['gamesPlayed']);
        $this->assertEquals(0.2, $statsLine['goalsAgainstAverage']);
        $this->assertEquals(49, $statsLine['wins']);
        $this->assertEquals(1, $statsLine['losses']);
        $this->assertEquals(48, $statsLine['plusMinus']);
        $this->assertEquals(98, $statsLine['winPercentage']);
        $this->assertEquals(0, $statsLine['goals']);
        $this->assertEquals(49, $statsLine['assists']);
        $this->assertEquals(49, $statsLine['points']);
        $this->assertEquals(49, $statsLine['shutOuts']);
        $this->assertEquals(1500, $statsLine['minutesPlayed']);
        $this->assertEquals(0.01, $statsLine['goalsPerMinute']);
    }



    private function generateMultipleGamesForAGivenSeason($seasonId = 1, $gameCount = 50, $playoffs = 0)
    {
        $gameDate = '2016-07-27';
        $start = '9:00 AM';
        $end = '9:30 AM';
        $playoff = $playoffs;
        $season = $seasonId;
        $gameId = '';

        for($i = 1; $i <= $gameCount; $i++) {
            $command = new \App\Commands\Game\AddFullGame(
                $gameDate,
                $start,
                $end,
                $playoff,
                $season,
                $i
            );


            $command->addWhiteGoalie($this->chrisLee);
            $command->addWhitePlayer($this->zachR);
            $command->addWhitePlayer($this->kevenB);
            $command->addWhitePlayer($this->ghislainD);
            $command->addWhitePlayer($this->paulE);
            $command->addWhitePlayer($this->paulG);


            $command->addBlackGoalie($this->davidR);
            $command->addBlackPlayer($this->chrisR);
            $command->addBlackPlayer($this->jeremieR);
            $command->addBlackPlayer($this->jacquesAuger);
            $command->addBlackPlayer($this->colinLemoine);


            $command->addBlackPoint(1, $this->chrisR, $this->colinLemoine);
            $command->addBlackPoint(2, $this->jacquesAuger, $this->davidR);
            $command->addBlackPoint(3, $this->jacquesAuger, $this->chrisR);
            $command->addBlackPoint(4, $this->chrisR, $this->colinLemoine);
            $command->addBlackPoint(5, $this->chrisR, '');
            $command->addBlackPoint(6, $this->colinLemoine, $this->jacquesAuger);
            $command->addBlackPoint(7, $this->colinLemoine, $this->chrisR);
            $command->addBlackPoint(8, $this->jacquesAuger, $this->colinLemoine);
            $command->addBlackPoint(9, $this->jeremieR, $this->chrisR);
            $command->addBlackPoint(10, $this->colinLemoine, $this->jeremieR);
            $gameId = $this->dispatch($command);
        }
        return $gameId;
    }

    public function test_getting_a_persons_goalie_stats()
    {
        $season = 1;
        $gameCount = 50;
        $playoff = 0;
        $this->generateMultipleGamesForAGivenSeason($season, $gameCount, $playoff);
        $allStats = $this->goalieStats->getGoalieStats($this->chrisLee, $playoff);
        $statsLine = $allStats[1];



        $this->assertEquals(500, $statsLine['goalsAgainst']);
        $this->assertEquals(50, $statsLine['gamesPlayed']);
        $this->assertEquals(10, $statsLine['goalsAgainstAverage']);
        $this->assertEquals(0, $statsLine['wins']);
        $this->assertEquals(50, $statsLine['losses']);
        $this->assertEquals(-50, $statsLine['plusMinus']);
        $this->assertEquals(0, $statsLine['winPercentage']);
        $this->assertEquals(0, $statsLine['goals']);
        $this->assertEquals(0, $statsLine['assists']);
        $this->assertEquals(0, $statsLine['points']);
        $this->assertEquals(0, $statsLine['shutOuts']);
        $this->assertEquals(1500, $statsLine['minutesPlayed']);
        $this->assertEquals(0.33, $statsLine['goalsPerMinute']);


        $statsLine = $allStats['all'];

        $this->assertEquals(500, $statsLine['goalsAgainst']);
        $this->assertEquals(50, $statsLine['gamesPlayed']);
        $this->assertEquals(10, $statsLine['goalsAgainstAverage']);
        $this->assertEquals(0, $statsLine['wins']);
        $this->assertEquals(50, $statsLine['losses']);
        $this->assertEquals(-50, $statsLine['plusMinus']);
        $this->assertEquals(0, $statsLine['winPercentage']);
        $this->assertEquals(0, $statsLine['goals']);
        $this->assertEquals(0, $statsLine['assists']);
        $this->assertEquals(0, $statsLine['points']);
        $this->assertEquals(0, $statsLine['shutOuts']);
        $this->assertEquals(1500, $statsLine['minutesPlayed']);
        $this->assertEquals(0.33, $statsLine['goalsPerMinute']);
    }

    public function test_getting_a_persons_goalie_stats_for_multiple_seasons()
    {
        $season = 1;
        $gameCount = 50;
        $playoff = 0;
        $this->generateMultipleGamesForAGivenSeason($season, $gameCount, $playoff);
        $season = 2;
        $gameCount = 50;
        $playoff = 0;
        $this->generateMultipleGamesForAGivenSeason($season, $gameCount, $playoff);
        $allStats = $this->goalieStats->getGoalieStats($this->chrisLee, $playoff);
        $statsLine = $allStats[1];



        $this->assertEquals(500, $statsLine['goalsAgainst']);
        $this->assertEquals(50, $statsLine['gamesPlayed']);
        $this->assertEquals(10, $statsLine['goalsAgainstAverage']);
        $this->assertEquals(0, $statsLine['wins']);
        $this->assertEquals(50, $statsLine['losses']);
        $this->assertEquals(-50, $statsLine['plusMinus']);
        $this->assertEquals(0, $statsLine['winPercentage']);
        $this->assertEquals(0, $statsLine['goals']);
        $this->assertEquals(0, $statsLine['assists']);
        $this->assertEquals(0, $statsLine['points']);
        $this->assertEquals(0, $statsLine['shutOuts']);
        $this->assertEquals(1500, $statsLine['minutesPlayed']);
        $this->assertEquals(0.33, $statsLine['goalsPerMinute']);

        $statsLine = $allStats[2];

        $this->assertEquals(500, $statsLine['goalsAgainst']);
        $this->assertEquals(50, $statsLine['gamesPlayed']);
        $this->assertEquals(10, $statsLine['goalsAgainstAverage']);
        $this->assertEquals(0, $statsLine['wins']);
        $this->assertEquals(50, $statsLine['losses']);
        $this->assertEquals(-50, $statsLine['plusMinus']);
        $this->assertEquals(0, $statsLine['winPercentage']);
        $this->assertEquals(0, $statsLine['goals']);
        $this->assertEquals(0, $statsLine['assists']);
        $this->assertEquals(0, $statsLine['points']);
        $this->assertEquals(0, $statsLine['shutOuts']);
        $this->assertEquals(1500, $statsLine['minutesPlayed']);
        $this->assertEquals(0.33, $statsLine['goalsPerMinute']);

        $statsLine = $allStats['all'];

        $this->assertEquals(1000, $statsLine['goalsAgainst']);
        $this->assertEquals(100, $statsLine['gamesPlayed']);
        $this->assertEquals(10, $statsLine['goalsAgainstAverage']);
        $this->assertEquals(0, $statsLine['wins']);
        $this->assertEquals(100, $statsLine['losses']);
        $this->assertEquals(-100, $statsLine['plusMinus']);
        $this->assertEquals(0, $statsLine['winPercentage']);
        $this->assertEquals(0, $statsLine['goals']);
        $this->assertEquals(0, $statsLine['assists']);
        $this->assertEquals(0, $statsLine['points']);
        $this->assertEquals(0, $statsLine['shutOuts']);
        $this->assertEquals(3000, $statsLine['minutesPlayed']);
        $this->assertEquals(0.33, $statsLine['goalsPerMinute']);
    }

    public function test_getting_a_persons_goalie_stats_who_has_never_played_goalie_will_not_have_all_calcs()
    {
        $season = 1;
        $gameCount = 50;
        $playoff = 0;
        $this->generateMultipleGamesForAGivenSeason($season, $gameCount, $playoff);
        $allStats = $this->goalieStats->getGoalieStats($this->paulE, $playoff);

        $this->assertTrue(!isset($allStats['all']));
    }

    private function genPlayerWithName($firstName, $lastName)
    {
        $email = 'email';
        $phoneNumber = 'phoneNumber';
        $height = 'height';
        $shoots = 'shoots';
        $favProPlayer = 'favProPlayer';
        $favProTeam = 'favProTeam';

        $command = new AddPlayer(
            $firstName,
            $lastName,
            $email,
            $phoneNumber,
            $height,
            $shoots,
            $favProPlayer,
            $favProTeam
        );
        $playerId = $this->dispatch($command);
        return $playerId;
    }
}
