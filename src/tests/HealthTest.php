<?php

class HealthTest extends \App\Infrastructure\Test\TestCase
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
        parent::setUp();

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

    public function test_get_persons_player_stats_who_has_never_played_as_a_player_will_not_have_all_calcs()
    {
        $season = 1;
        $gameCount = 50;
        $playoff = 0;
        $latestGameId = $this->generateMultipleGamesForAGivenSeason($season, $gameCount, $playoff);

        $this->visit('/stats/players')
            ->see('Season '.$season)
            ->see('Player Stats');
        $this->visit('/records')
            ->see('Records');
        $this->visit('/stats/players/season/'.$season.'/playoff/'.$playoff)
            ->see('Season '.$season)
            ->see('Player Stats');
        $this->visit('/stats/goalies/season/'.$season.'/playoff/'.$playoff)
            ->see('Season '.$season)
            ->see('Player Stats');
        $this->visit('/seasons/'.$season)
            ->see('Season '.$season);
        $this->visit('/games/'.$latestGameId)
            ->see('Game '.$gameCount)
            ->see('Season '.$season);
        $this->visit('players/'.$this->chrisLee.'/stats/player')
            ->see('Chris Lee')
            ->see('Regular Season Stats');
        $this->visit('players/'.$this->chrisLee.'/stats/goalie')
            ->see('Chris Lee')
            ->see('Regular Season Stats');
        $this->visit('players/'.$this->chrisLee.'/records')
            ->see('Chris Lee');
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
