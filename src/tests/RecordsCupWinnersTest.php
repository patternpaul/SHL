<?php


class RecordsCupWinnersTest extends \App\Infrastructure\Test\TestCaseCore
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


    public function test_cup_winners_when_game_edited()
    {
        $this->generateMultipleGamesForAGivenSeason(1, 50, 0);
        $this->generateGame(1, 1, 1);
        $this->generateGame(2, 2, 1);
        $this->generateGame(2, 3, 1);
        $gameId = $this->generateGame(4, 4, 1);

        $records = $this->recordStore->getRecords();
        $entries = $records[\App\Listeners\Records\CupWinners::BASE_KEY.'01']['entries'];
        $this->assertEquals(5, count($entries));

        $season = 1;
        $playoff = 1;
        $gameDate = '2016-07-27';
        $start = '9:00 AM';
        $end = '9:30 AM';

        $command = new \App\Commands\Game\EditFullGame(
            $gameId,
            $gameDate,
            $start,
            $end,
            $playoff,
            $season,
            4
        );


        $command->addWhiteGoalie($this->chrisLee);
        $command->addBlackGoalie($this->davidR);
        $command->addWhitePlayer($this->zachR);
        $command->addBlackPlayer($this->chrisR);
        $command->addWhitePlayer($this->kevenB);
        $command->addBlackPlayer($this->jeremieR);
        $command->addWhitePlayer($this->ghislainD);
        $command->addBlackPlayer($this->jacquesAuger);
        $command->addWhitePlayer($this->paulE);
        $command->addBlackPlayer($this->colinLemoine);
        $command->addWhitePlayer($this->paulG);




        for ($i=1; $i <= 12; $i++) {
            $command->addWhitePoint($i, $this->ghislainD, $this->zachR);
        }

        $command->addBlackPoint(1, $this->chrisR, $this->colinLemoine);
        $command->addBlackPoint(2, $this->jacquesAuger, $this->jacquesAuger);
        $command->addBlackPoint(3, $this->jacquesAuger, $this->jacquesAuger);
        $command->addBlackPoint(4, $this->chrisR, $this->jacquesAuger);
        $command->addBlackPoint(5, $this->chrisR, $this->jacquesAuger);
        $command->addBlackPoint(6, $this->chrisR, $this->colinLemoine);
        $command->addBlackPoint(7, $this->colinLemoine, $this->colinLemoine);
        $command->addBlackPoint(8, $this->jacquesAuger, $this->colinLemoine);
        $command->addBlackPoint(9, $this->jacquesAuger, $this->colinLemoine);
        $command->addBlackPoint(10, $this->jacquesAuger, $this->colinLemoine);
        $this->dispatch($command);

        $records = $this->recordStore->getRecords();
        $this->assertTrue(!isset($records[\App\Listeners\Records\CupWinners::BASE_KEY.'01']));

        $this->generateGame(2, 5, 1);
        $records = $this->recordStore->getRecords();
        $entries = $records[\App\Listeners\Records\CupWinners::BASE_KEY.'01']['entries'];
        $this->assertEquals(5, count($entries));

    }



    public function test_cup_winners_order_of_players_should_not_matter()
    {
        $johnDoe = $this->genPlayerWithName("John", "Doe");
        $jackBlack = $this->genPlayerWithName("Jack", "Black");


        $season = 1;
        $playoff = 1;
        $gameDate = '2016-07-27';
        $start = '9:00 AM';
        $end = '9:30 AM';

        $command = new \App\Commands\Game\AddFullGame(
            $gameDate,
            $start,
            $end,
            $playoff,
            $season,
            1
        );

        $command->addWhiteGoalie($this->chrisLee);
        $command->addBlackGoalie($this->davidR);
        $command->addWhitePlayer($this->zachR);
        $command->addBlackPlayer($this->chrisR);
        $command->addWhitePlayer($this->kevenB);
        $command->addBlackPlayer($this->jeremieR);
        $command->addWhitePlayer($this->ghislainD);
        $command->addBlackPlayer($this->jacquesAuger);
        $command->addWhitePlayer($this->paulE);
        $command->addBlackPlayer($this->colinLemoine);
        $command->addWhitePlayer($this->paulG);

        for ($i=1; $i <= 10; $i++) {
            $command->addWhitePoint($i, $this->ghislainD, $this->zachR);
        }

        for ($i=1; $i <= 2; $i++) {
            $command->addBlackPoint($i, $this->chrisR, $this->colinLemoine);
        }
        $this->dispatch($command);

        $command = new \App\Commands\Game\AddFullGame(
            $gameDate,
            $start,
            $end,
            $playoff,
            $season,
            2
        );

        $command->addWhiteGoalie($this->chrisLee);
        $command->addBlackGoalie($this->davidR);
        $command->addWhitePlayer($this->zachR);
        $command->addBlackPlayer($this->chrisR);
        $command->addWhitePlayer($this->kevenB);
        $command->addBlackPlayer($this->jeremieR);
        $command->addWhitePlayer($this->ghislainD);
        $command->addBlackPlayer($this->jacquesAuger);
        $command->addWhitePlayer($this->paulE);
        $command->addBlackPlayer($this->colinLemoine);
        $command->addWhitePlayer($this->paulG);

        for ($i=1; $i <= 10; $i++) {
            $command->addWhitePoint($i, $this->ghislainD, $this->zachR);
        }

        for ($i=1; $i <= 2; $i++) {
            $command->addBlackPoint($i, $this->chrisR, $this->colinLemoine);
        }
        $this->dispatch($command);

        $command = new \App\Commands\Game\AddFullGame(
            $gameDate,
            $start,
            $end,
            $playoff,
            $season,
            3
        );

        $command->addWhiteGoalie($this->chrisLee);
        $command->addBlackGoalie($this->davidR);
        $command->addWhitePlayer($this->zachR);
        $command->addBlackPlayer($this->chrisR);
        $command->addWhitePlayer($this->kevenB);
        $command->addBlackPlayer($this->jeremieR);
        $command->addWhitePlayer($this->ghislainD);
        $command->addBlackPlayer($this->jacquesAuger);
        $command->addWhitePlayer($this->paulE);
        $command->addBlackPlayer($this->colinLemoine);
        $command->addWhitePlayer($this->paulG);

        for ($i=1; $i <= 10; $i++) {
            $command->addWhitePoint($i, $this->ghislainD, $this->zachR);
        }

        for ($i=1; $i <= 2; $i++) {
            $command->addBlackPoint($i, $this->chrisR, $this->colinLemoine);
        }
        $this->dispatch($command);

        $command = new \App\Commands\Game\AddFullGame(
            $gameDate,
            $start,
            $end,
            $playoff,
            $season,
            4
        );


        $command->addWhitePlayer($johnDoe);
        $command->addWhitePlayer($jackBlack);
        $command->addWhiteGoalie($this->chrisLee);
        $command->addBlackGoalie($this->davidR);
        $command->addWhitePlayer($this->zachR);
        $command->addBlackPlayer($this->chrisR);
        $command->addWhitePlayer($this->kevenB);
        $command->addBlackPlayer($this->jeremieR);
        $command->addWhitePlayer($this->ghislainD);
        $command->addBlackPlayer($this->jacquesAuger);
        $command->addWhitePlayer($this->paulE);
        $command->addBlackPlayer($this->colinLemoine);
        $command->addWhitePlayer($this->paulG);

        for ($i=1; $i <= 10; $i++) {
            $command->addWhitePoint($i, $this->ghislainD, $this->zachR);
        }

        for ($i=1; $i <= 2; $i++) {
            $command->addBlackPoint($i, $this->chrisR, $this->colinLemoine);
        }
        $this->dispatch($command);

        $records = $this->recordStore->getRecords();
        $entries = $records[\App\Listeners\Records\CupWinners::BASE_KEY.'01']['entries'];
        $this->assertEquals(8, count($entries));

    }

    public function test_cup_winners()
    {
        $this->generateMultipleGamesForAGivenSeason(1, 50, 0);
        $this->generateGame(1, 1, 1);
        $this->generateGame(2, 2, 1);
        $this->generateGame(12, 3, 1);
        $this->generateGame(12, 4, 1);
        $this->generateGame(2, 5, 1);
        $this->generateGame(12, 6, 1);
        $this->generateGame(4, 7, 1);

        $records = $this->recordStore->getRecords();
        $entries = $records[\App\Listeners\Records\CupWinners::BASE_KEY.'01']['entries'];
        $this->assertEquals(5, count($entries));
    }

    public function test_cup_winners_multiple_season()
    {
        $this->generateMultipleGamesForAGivenSeason(1, 50, 0);
        $this->generateGame(1, 1, 1);
        $this->generateGame(2, 2, 1);
        $this->generateGame(12, 3, 1);
        $this->generateGame(12, 4, 1);
        $this->generateGame(2, 5, 1);
        $this->generateGame(12, 6, 1);
        $this->generateGame(4, 7, 1);
        $this->generateMultipleGamesForAGivenSeason(2, 50, 0);
        $this->generateGame(1, 1, 2);
        $this->generateGame(2, 2, 2);
        $this->generateGame(12, 3, 2);
        $this->generateGame(12, 4, 2);
        $this->generateGame(2, 5, 2);
        $this->generateGame(12, 6, 2);
        $this->generateGame(12, 7, 2);


        $records = $this->recordStore->getRecords();
        $entries = $records[\App\Listeners\Records\CupWinners::BASE_KEY.'01']['entries'];
        $this->assertEquals(5, count($entries));
        $entries = $records[\App\Listeners\Records\CupWinners::BASE_KEY.'02']['entries'];
        $this->assertEquals(6, count($entries));
    }


    private function generateGame($maxGoals, $gameNum, $season)
    {
        $gameDate = '2016-07-27';
        $start = '9:00 AM';
        $end = '9:30 AM';
        $playoff = 1;
        $season = $season;

        $command = new \App\Commands\Game\AddFullGame(
            $gameDate,
            $start,
            $end,
            $playoff,
            $season,
            $gameNum
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



        for ($i=1; $i <= $maxGoals; $i++) {
            $command->addWhitePoint($i, $this->ghislainD, $this->zachR);
        }

        $command->addBlackPoint(1, $this->chrisR, $this->colinLemoine);
        $command->addBlackPoint(2, $this->jacquesAuger, $this->jacquesAuger);
        $command->addBlackPoint(3, $this->jacquesAuger, $this->jacquesAuger);
        $command->addBlackPoint(4, $this->chrisR, $this->jacquesAuger);
        $command->addBlackPoint(5, $this->chrisR, $this->jacquesAuger);
        $command->addBlackPoint(6, $this->chrisR, $this->colinLemoine);
        $command->addBlackPoint(7, $this->colinLemoine, $this->colinLemoine);
        $command->addBlackPoint(8, $this->jacquesAuger, $this->colinLemoine);
        $command->addBlackPoint(9, $this->jacquesAuger, $this->colinLemoine);
        $command->addBlackPoint(10, $this->jacquesAuger, $this->colinLemoine);


        $gameId = $this->dispatch($command);
        return $gameId;
    }

    private function generateMultipleGamesForAGivenSeason($seasonId = 1, $gameCount = 50, $playoffs = 0)
    {
        $gameDate = '2016-07-27';
        $start = '9:00 AM';
        $end = '9:30 AM';
        $playoff = $playoffs;
        $season = $seasonId;

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
            $command->addBlackPoint(6, $this->chrisR, $this->jacquesAuger);
            $command->addBlackPoint(7, $this->colinLemoine, $this->chrisR);
            $command->addBlackPoint(8, $this->jacquesAuger, $this->colinLemoine);
            $command->addBlackPoint(9, $this->jacquesAuger, $this->chrisR);
            $command->addBlackPoint(10, $this->jacquesAuger, $this->jeremieR);
            $gameId = $this->dispatch($command);

        }
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
