<?php


class RecordsLeastGoalsInARegularSeasonGameTest extends \App\Infrastructure\Test\TestCaseCore
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

    public function test_record()
    {
        $gameId = $this->generateGame(2, 1);

        $records = $this->recordStore->getRecords();
        $entries = $records[\App\Listeners\Records\LeastGoalsInARegularSeasonGame::BASE_KEY]['entries'];
        $this->assertEquals("[season=1]: [gameId=".$gameId." gameNumber=1] - 2 Goals. 10-2 for Colored Team. Goalies: ([playerId=".$this->chrisLee.'] white vs. [playerId='.$this->davidR.'] colored)', $entries[0]);
    }

    public function test_multi_game()
    {
        $gameId = $this->generateGame(2, 1);
        $gameId = $this->generateGame(2, 2);
        $records = $this->recordStore->getRecords();
        $this->assertEquals(2, count($records[\App\Listeners\Records\LeastGoalsInARegularSeasonGame::BASE_KEY]['entries']));

    }

    public function test_multi_game_for_longer_game()
    {
        $gameId = $this->generateGame(5, 1);
        $mostGoalsGameId = $this->generateGame(2, 2);
        $records = $this->recordStore->getRecords();

        $entries = $records[\App\Listeners\Records\LeastGoalsInARegularSeasonGame::BASE_KEY]['entries'];
        $this->assertEquals(1, count($entries));
        $this->assertEquals("[season=1]: [gameId=".$mostGoalsGameId." gameNumber=2] - 2 Goals. 10-2 for Colored Team. Goalies: ([playerId=".$this->chrisLee.'] white vs. [playerId='.$this->davidR.'] colored)', $entries[0]);
    }


    private function generateGame($maxGoals, $gameNum)
    {
        $gameDate = '2016-07-27';
        $start = '9:00 AM';
        $end = '9:30 AM';
        $playoff = 0;
        $season = 1;

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
