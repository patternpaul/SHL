<?php

use App\Aggregates\Game;

class PlayerStatsTest extends \App\Infrastructure\Test\TestCaseCore
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
        $statsLine = $this->stats->getCalcPlayerStatLine($this->ghislainD, $season, $playoff);

        $this->assertEquals(50, $statsLine['goals']);
        $this->assertEquals(0, $statsLine['assists']);
        $this->assertEquals(50, $statsLine['points']);
        $this->assertEquals(1, $statsLine['goalsPerGame']);
        $this->assertEquals(0, $statsLine['assistsPerGame']);
        $this->assertEquals(1, $statsLine['pointsPerGame']);
        $this->assertEquals(100, $statsLine['teamGoalsPercentage']);
        $this->assertEquals(0, $statsLine['wins']);
        $this->assertEquals(50, $statsLine['losses']);
        $this->assertEquals(-50, $statsLine['plusMinus']);
        $this->assertEquals(0, $statsLine['winPercentage']);
        $this->assertEquals(0, $statsLine['winPercentage']);
        $this->assertEquals(0, $statsLine['gameWinningGoals']);


        $statsLine = $this->stats->getCalcPlayerStatLine($this->colinLemoine, $season, $playoff);

        $this->assertEquals(150, $statsLine['goals']);
        $this->assertEquals(150, $statsLine['assists']);
        $this->assertEquals(300, $statsLine['points']);
        $this->assertEquals(3, $statsLine['goalsPerGame']);
        $this->assertEquals(3, $statsLine['assistsPerGame']);
        $this->assertEquals(6, $statsLine['pointsPerGame']);
        $this->assertEquals(30, $statsLine['teamGoalsPercentage']);
        $this->assertEquals(50, $statsLine['wins']);
        $this->assertEquals(0, $statsLine['losses']);
        $this->assertEquals(50, $statsLine['plusMinus']);
        $this->assertEquals(100, $statsLine['winPercentage']);
        $this->assertEquals(50, $statsLine['gameWinningGoals']);
    }

    public function test_goalie_get_stat_line_scenario_should_not_throw_divide_by_zero_error()
    {
        $season = 1;
        $gameCount = 1;
        $playoff = 0;
        $this->generateMultipleGamesForAGivenSeason($season, $gameCount, $playoff);

        $statsLine = $this->stats->getCalcPlayerStatLine($this->davidR, $season, $playoff);

        $this->assertEquals(0, $statsLine['goals']);
        $this->assertEquals(0, $statsLine['assists']);
        $this->assertEquals(0, $statsLine['points']);
        $this->assertEquals(0, $statsLine['goalsPerGame']);
        $this->assertEquals(0, $statsLine['assistsPerGame']);
        $this->assertEquals(0, $statsLine['pointsPerGame']);
        $this->assertEquals(0, $statsLine['teamGoalsPercentage']);
        $this->assertEquals(0, $statsLine['wins']);
        $this->assertEquals(0, $statsLine['losses']);
        $this->assertEquals(0, $statsLine['plusMinus']);
        $this->assertEquals(0, $statsLine['winPercentage']);
        $this->assertEquals(0, $statsLine['gameWinningGoals']);
    }


    public function test_edit_game_scenario()
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


        $command->addWhitePoint(1, $this->ghislainD, $this->paulE);
        $command->addWhitePoint(2, $this->ghislainD, $this->paulE);
        $command->addWhitePoint(3, $this->ghislainD, $this->paulE);
        $command->addWhitePoint(4, $this->ghislainD, $this->paulE);
        $command->addWhitePoint(5, $this->ghislainD, '');
        $command->addWhitePoint(6, $this->ghislainD, $this->paulE);
        $command->addWhitePoint(7, $this->ghislainD, $this->paulE);
        $command->addWhitePoint(8, $this->ghislainD, $this->paulE);
        $command->addWhitePoint(9, $this->ghislainD, $this->paulE);
        $command->addWhitePoint(10, $this->ghislainD, $this->paulE);

        $this->dispatch($command);



        $statsLine = $this->stats->getCalcPlayerStatLine($this->ghislainD, $season, $playoff);

        $this->assertEquals(59, $statsLine['goals']);
        $this->assertEquals(0, $statsLine['assists']);
        $this->assertEquals(59, $statsLine['points']);
        $this->assertEquals(1.18, $statsLine['goalsPerGame']);
        $this->assertEquals(0, $statsLine['assistsPerGame']);
        $this->assertEquals(1.18, $statsLine['pointsPerGame']);
        $this->assertEquals(100, $statsLine['teamGoalsPercentage']);
        $this->assertEquals(1, $statsLine['wins']);
        $this->assertEquals(49, $statsLine['losses']);
        $this->assertEquals(-48, $statsLine['plusMinus']);
        $this->assertEquals(2, $statsLine['winPercentage']);
        $this->assertEquals(1, $statsLine['gameWinningGoals']);


        $statsLine = $this->stats->getCalcPlayerStatLine($this->colinLemoine, $season, $playoff);

        $this->assertEquals(147, $statsLine['goals']);
        $this->assertEquals(147, $statsLine['assists']);
        $this->assertEquals(294, $statsLine['points']);
        $this->assertEquals(2.94, $statsLine['goalsPerGame']);
        $this->assertEquals(2.94, $statsLine['assistsPerGame']);
        $this->assertEquals(5.88, $statsLine['pointsPerGame']);
        $this->assertEquals(30, $statsLine['teamGoalsPercentage']);
        $this->assertEquals(49, $statsLine['wins']);
        $this->assertEquals(1, $statsLine['losses']);
        $this->assertEquals(48, $statsLine['plusMinus']);
        $this->assertEquals(98, $statsLine['winPercentage']);
        $this->assertEquals(49, $statsLine['gameWinningGoals']);
    }



    public function test_goalie_not_in_stats_scenario()
    {
        $season = 1;
        $gameCount = 50;
        $playoff = 0;
        $this->generateMultipleGamesForAGivenSeason($season, $gameCount, $playoff);
        $statsLines = $this->stats->getCalcStatLines('all', 'all');

        $this->assertTrue(!isset($statsLines[$this->davidR]));
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

        $statsLines = $this->stats->getCalcStatLines('all', 'all');

        $statsLine = $statsLines[$this->ghislainD];

        $this->assertEquals(104, $statsLine['goals']);
        $this->assertEquals(0, $statsLine['assists']);
        $this->assertEquals(104, $statsLine['points']);
        $this->assertEquals(1, $statsLine['goalsPerGame']);
        $this->assertEquals(0, $statsLine['assistsPerGame']);
        $this->assertEquals(1, $statsLine['pointsPerGame']);
        $this->assertEquals(100, $statsLine['teamGoalsPercentage']);
        $this->assertEquals(0, $statsLine['wins']);
        $this->assertEquals(104, $statsLine['losses']);
        $this->assertEquals(-104, $statsLine['plusMinus']);
        $this->assertEquals(0, $statsLine['winPercentage']);
        $this->assertEquals(0, $statsLine['winPercentage']);
        $this->assertEquals(0, $statsLine['gameWinningGoals']);


        $statsLine = $statsLines[$this->colinLemoine];

        $this->assertEquals(312, $statsLine['goals']);
        $this->assertEquals(312, $statsLine['assists']);
        $this->assertEquals(624, $statsLine['points']);
        $this->assertEquals(3, $statsLine['goalsPerGame']);
        $this->assertEquals(3, $statsLine['assistsPerGame']);
        $this->assertEquals(6, $statsLine['pointsPerGame']);
        $this->assertEquals(30, $statsLine['teamGoalsPercentage']);
        $this->assertEquals(104, $statsLine['wins']);
        $this->assertEquals(0, $statsLine['losses']);
        $this->assertEquals(104, $statsLine['plusMinus']);
        $this->assertEquals(100, $statsLine['winPercentage']);
        $this->assertEquals(104, $statsLine['gameWinningGoals']);



        $statsLines = $this->stats->getCalcStatLines($season, 'all');

        $statsLine = $statsLines[$this->ghislainD];

        $this->assertEquals(54, $statsLine['goals']);
        $this->assertEquals(0, $statsLine['assists']);
        $this->assertEquals(54, $statsLine['points']);
        $this->assertEquals(1, $statsLine['goalsPerGame']);
        $this->assertEquals(0, $statsLine['assistsPerGame']);
        $this->assertEquals(1, $statsLine['pointsPerGame']);
        $this->assertEquals(100, $statsLine['teamGoalsPercentage']);
        $this->assertEquals(0, $statsLine['wins']);
        $this->assertEquals(54, $statsLine['losses']);
        $this->assertEquals(-54, $statsLine['plusMinus']);
        $this->assertEquals(0, $statsLine['winPercentage']);
        $this->assertEquals(0, $statsLine['winPercentage']);
        $this->assertEquals(0, $statsLine['gameWinningGoals']);


        $statsLine = $statsLines[$this->colinLemoine];

        $this->assertEquals(162, $statsLine['goals']);
        $this->assertEquals(162, $statsLine['assists']);
        $this->assertEquals(324, $statsLine['points']);
        $this->assertEquals(3, $statsLine['goalsPerGame']);
        $this->assertEquals(3, $statsLine['assistsPerGame']);
        $this->assertEquals(6, $statsLine['pointsPerGame']);
        $this->assertEquals(30, $statsLine['teamGoalsPercentage']);
        $this->assertEquals(54, $statsLine['wins']);
        $this->assertEquals(0, $statsLine['losses']);
        $this->assertEquals(54, $statsLine['plusMinus']);
        $this->assertEquals(100, $statsLine['winPercentage']);
        $this->assertEquals(54, $statsLine['gameWinningGoals']);


        $statsLines = $this->stats->getCalcStatLines($season, 0);

        $statsLine = $statsLines[$this->ghislainD];

        $this->assertEquals(50, $statsLine['goals']);
        $this->assertEquals(0, $statsLine['assists']);
        $this->assertEquals(50, $statsLine['points']);
        $this->assertEquals(1, $statsLine['goalsPerGame']);
        $this->assertEquals(0, $statsLine['assistsPerGame']);
        $this->assertEquals(1, $statsLine['pointsPerGame']);
        $this->assertEquals(100, $statsLine['teamGoalsPercentage']);
        $this->assertEquals(0, $statsLine['wins']);
        $this->assertEquals(50, $statsLine['losses']);
        $this->assertEquals(-50, $statsLine['plusMinus']);
        $this->assertEquals(0, $statsLine['winPercentage']);
        $this->assertEquals(0, $statsLine['winPercentage']);
        $this->assertEquals(0, $statsLine['gameWinningGoals']);


        $statsLine = $statsLines[$this->colinLemoine];

        $this->assertEquals(150, $statsLine['goals']);
        $this->assertEquals(150, $statsLine['assists']);
        $this->assertEquals(300, $statsLine['points']);
        $this->assertEquals(3, $statsLine['goalsPerGame']);
        $this->assertEquals(3, $statsLine['assistsPerGame']);
        $this->assertEquals(6, $statsLine['pointsPerGame']);
        $this->assertEquals(30, $statsLine['teamGoalsPercentage']);
        $this->assertEquals(50, $statsLine['wins']);
        $this->assertEquals(0, $statsLine['losses']);
        $this->assertEquals(50, $statsLine['plusMinus']);
        $this->assertEquals(100, $statsLine['winPercentage']);
        $this->assertEquals(50, $statsLine['gameWinningGoals']);


        $statsLines = $this->stats->getCalcStatLines($season, 1);

        $statsLine = $statsLines[$this->ghislainD];

        $this->assertEquals(4, $statsLine['goals']);
        $this->assertEquals(0, $statsLine['assists']);
        $this->assertEquals(4, $statsLine['points']);
        $this->assertEquals(1, $statsLine['goalsPerGame']);
        $this->assertEquals(0, $statsLine['assistsPerGame']);
        $this->assertEquals(1, $statsLine['pointsPerGame']);
        $this->assertEquals(100, $statsLine['teamGoalsPercentage']);
        $this->assertEquals(0, $statsLine['wins']);
        $this->assertEquals(4, $statsLine['losses']);
        $this->assertEquals(-4, $statsLine['plusMinus']);
        $this->assertEquals(0, $statsLine['winPercentage']);
        $this->assertEquals(0, $statsLine['winPercentage']);
        $this->assertEquals(0, $statsLine['gameWinningGoals']);


        $statsLine = $statsLines[$this->colinLemoine];

        $this->assertEquals(12, $statsLine['goals']);
        $this->assertEquals(12, $statsLine['assists']);
        $this->assertEquals(24, $statsLine['points']);
        $this->assertEquals(3, $statsLine['goalsPerGame']);
        $this->assertEquals(3, $statsLine['assistsPerGame']);
        $this->assertEquals(6, $statsLine['pointsPerGame']);
        $this->assertEquals(30, $statsLine['teamGoalsPercentage']);
        $this->assertEquals(4, $statsLine['wins']);
        $this->assertEquals(0, $statsLine['losses']);
        $this->assertEquals(4, $statsLine['plusMinus']);
        $this->assertEquals(100, $statsLine['winPercentage']);
        $this->assertEquals(4, $statsLine['gameWinningGoals']);
    }


    public function test_get_persons_player_stats()
    {
        $season = 1;
        $gameCount = 50;
        $playoff = 0;
        $this->generateMultipleGamesForAGivenSeason($season, $gameCount, $playoff);
        $allStats = $this->stats->getPlayerStats($this->ghislainD, $playoff);
        $statsLine = $allStats[1];

        $this->assertEquals(50, $statsLine['goals']);
        $this->assertEquals(0, $statsLine['assists']);
        $this->assertEquals(50, $statsLine['points']);
        $this->assertEquals(1, $statsLine['goalsPerGame']);
        $this->assertEquals(0, $statsLine['assistsPerGame']);
        $this->assertEquals(1, $statsLine['pointsPerGame']);
        $this->assertEquals(100, $statsLine['teamGoalsPercentage']);
        $this->assertEquals(0, $statsLine['wins']);
        $this->assertEquals(50, $statsLine['losses']);
        $this->assertEquals(-50, $statsLine['plusMinus']);
        $this->assertEquals(0, $statsLine['winPercentage']);
        $this->assertEquals(0, $statsLine['winPercentage']);
        $this->assertEquals(0, $statsLine['gameWinningGoals']);

        $statsLine = $allStats['all'];

        $this->assertEquals(50, $statsLine['goals']);
        $this->assertEquals(0, $statsLine['assists']);
        $this->assertEquals(50, $statsLine['points']);
        $this->assertEquals(1, $statsLine['goalsPerGame']);
        $this->assertEquals(0, $statsLine['assistsPerGame']);
        $this->assertEquals(1, $statsLine['pointsPerGame']);
        $this->assertEquals(100, $statsLine['teamGoalsPercentage']);
        $this->assertEquals(0, $statsLine['wins']);
        $this->assertEquals(50, $statsLine['losses']);
        $this->assertEquals(-50, $statsLine['plusMinus']);
        $this->assertEquals(0, $statsLine['winPercentage']);
        $this->assertEquals(0, $statsLine['winPercentage']);
        $this->assertEquals(0, $statsLine['gameWinningGoals']);
    }

    public function test_get_a_persons_player_stats_over_multiple_seasons()
    {
        $season = 1;
        $gameCount = 50;
        $playoff = 0;
        $this->generateMultipleGamesForAGivenSeason($season, $gameCount, $playoff);
        $season = 2;
        $gameCount = 50;
        $playoff = 0;
        $this->generateMultipleGamesForAGivenSeason($season, $gameCount, $playoff);
        $allStats = $this->stats->getPlayerStats($this->ghislainD, $playoff);
        $statsLine = $allStats[1];

        $this->assertEquals(50, $statsLine['goals']);
        $this->assertEquals(0, $statsLine['assists']);
        $this->assertEquals(50, $statsLine['points']);
        $this->assertEquals(1, $statsLine['goalsPerGame']);
        $this->assertEquals(0, $statsLine['assistsPerGame']);
        $this->assertEquals(1, $statsLine['pointsPerGame']);
        $this->assertEquals(100, $statsLine['teamGoalsPercentage']);
        $this->assertEquals(0, $statsLine['wins']);
        $this->assertEquals(50, $statsLine['losses']);
        $this->assertEquals(-50, $statsLine['plusMinus']);
        $this->assertEquals(0, $statsLine['winPercentage']);
        $this->assertEquals(0, $statsLine['winPercentage']);
        $this->assertEquals(0, $statsLine['gameWinningGoals']);

        $statsLine = $allStats[2];

        $this->assertEquals(50, $statsLine['goals']);
        $this->assertEquals(0, $statsLine['assists']);
        $this->assertEquals(50, $statsLine['points']);
        $this->assertEquals(1, $statsLine['goalsPerGame']);
        $this->assertEquals(0, $statsLine['assistsPerGame']);
        $this->assertEquals(1, $statsLine['pointsPerGame']);
        $this->assertEquals(100, $statsLine['teamGoalsPercentage']);
        $this->assertEquals(0, $statsLine['wins']);
        $this->assertEquals(50, $statsLine['losses']);
        $this->assertEquals(-50, $statsLine['plusMinus']);
        $this->assertEquals(0, $statsLine['winPercentage']);
        $this->assertEquals(0, $statsLine['winPercentage']);
        $this->assertEquals(0, $statsLine['gameWinningGoals']);

        $statsLine = $allStats['all'];

        $this->assertEquals(100, $statsLine['goals']);
        $this->assertEquals(0, $statsLine['assists']);
        $this->assertEquals(100, $statsLine['points']);
        $this->assertEquals(1, $statsLine['goalsPerGame']);
        $this->assertEquals(0, $statsLine['assistsPerGame']);
        $this->assertEquals(1, $statsLine['pointsPerGame']);
        $this->assertEquals(100, $statsLine['teamGoalsPercentage']);
        $this->assertEquals(0, $statsLine['wins']);
        $this->assertEquals(100, $statsLine['losses']);
        $this->assertEquals(-100, $statsLine['plusMinus']);
        $this->assertEquals(0, $statsLine['winPercentage']);
        $this->assertEquals(0, $statsLine['winPercentage']);
        $this->assertEquals(0, $statsLine['gameWinningGoals']);
    }

    public function test_get_persons_player_stats_who_has_never_played_as_a_player_will_not_have_all_calcs()
    {
        $season = 1;
        $gameCount = 50;
        $playoff = 0;
        $this->generateMultipleGamesForAGivenSeason($season, $gameCount, $playoff);
        $allStats = $this->stats->getPlayerStats($this->chrisLee, $playoff);

        $this->assertTrue(!isset($allStats['all']));
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


            $command->addWhitePoint(1, $this->ghislainD, $this->zachR);


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



    private function genPlayerWithName($firstName, $lastName)
    {
        $email = 'email';
        $phoneNumber = 'phoneNumber';
        $height = 'height';
        $shoots = 'shoots';
        $favProPlayer = 'favProPlayer';
        $favProTeam = 'favProTeam';

        $command = new \App\Commands\Player\AddPlayer(
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
