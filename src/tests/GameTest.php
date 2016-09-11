<?php

use App\Aggregates\Game;

class GameTest extends \App\Infrastructure\Test\TestCaseCore
{
    public function test_add_game()
    {
        $blackOne = $this->genPlayerWithName('First', 'Last');
        $blackTwo = $this->genPlayerWithName('First', 'Last');
        $whiteOne = $this->genPlayerWithName('First', 'Last');
        $whiteTwo = $this->genPlayerWithName('First', 'Last');
        $gameDate = '2016-07-27';
        $start = '9:00 AM';
        $end = '9:30 AM';
        $playoff = '1';
        $season = 1;
        $gameNumber = 1;
        $blackPoints = 10;
        $whitePoints = 6;

        $command = new \App\Commands\Game\AddFullGame(
            $gameDate,
            $start,
            $end,
            $playoff,
            $season,
            $gameNumber
        );
        
        $command->addBlackPlayer($blackOne);
        $command->addBlackGoalie($blackTwo);
        $command->addWhitePlayer($whiteOne);
        $command->addWhiteGoalie($whiteTwo);

        for ($i = 1; $i <= $blackPoints; $i++) {
            $command->addBlackPoint($i, $blackOne, $blackTwo);
        }
        for ($i = 1; $i <= $whitePoints; $i++) {
            $command->addWhitePoint($i, $whiteOne, $whiteTwo);
        }

        $gameId = $this->dispatch($command);
        
        
        

        /** @var \App\Aggregates\Game $gameAgg */
        $gameAgg = $this->aggregateRepository->get($gameId);
        $game = $this->games->getById($gameId);

        $this->assertEquals($blackPoints, $game[\App\Aggregates\Game::BLACK_TEAM."Points"]);
        $this->assertEquals($whitePoints, $game[\App\Aggregates\Game::WHITE_TEAM."Points"]);


        $this->assertEquals($blackPoints, $gameAgg->getBlackPointTotal());
        $this->assertEquals($whitePoints, $gameAgg->getWhitePointTotal());
    }

    public function test_get_latest_game_and_season()
    {
        $blackOne = $this->genPlayerWithName('First', 'Last');
        $blackTwo = $this->genPlayerWithName('First', 'Last');
        $whiteOne = $this->genPlayerWithName('First', 'Last');
        $whiteTwo = $this->genPlayerWithName('First', 'Last');

        $gameDate = '2016-07-27';
        $start = '9:00 AM';
        $end = '9:30 AM';
        $playoff = 0;
        $oldSeason = 9;
        $oldGameNumber = 9;
        $blackPoints = 10;
        $whitePoints = 6;

        $command = new \App\Commands\Game\AddFullGame(
            $gameDate,
            $start,
            $end,
            $playoff,
            $oldSeason,
            $oldGameNumber
        );

        $command->addBlackPlayer($blackOne);
        $command->addBlackGoalie($blackTwo);
        $command->addWhitePlayer($whiteOne);
        $command->addWhiteGoalie($whiteTwo);

        for ($i = 1; $i <= $blackPoints; $i++) {
            $command->addBlackPoint($i, $blackOne, $blackTwo);
        }
        for ($i = 1; $i <= $whitePoints; $i++) {
            $command->addWhitePoint($i, $whiteOne, $whiteTwo);
        }

        $gameId = $this->dispatch($command);


        $gameDate = '2016-07-27';
        $start = '9:00 AM';
        $end = '9:30 AM';
        $playoff = 0;
        $season = 10;
        $gameNumber = 10;
        $blackPoints = 10;
        $whitePoints = 6;

        $command = new \App\Commands\Game\AddFullGame(
            $gameDate,
            $start,
            $end,
            $playoff,
            $season,
            $gameNumber
        );

        $command->addBlackPlayer($blackOne);
        $command->addBlackGoalie($blackTwo);
        $command->addWhitePlayer($whiteOne);
        $command->addWhiteGoalie($whiteTwo);

        for ($i = 1; $i <= $blackPoints; $i++) {
            $command->addBlackPoint($i, $blackOne, $blackTwo);
        }
        for ($i = 1; $i <= $whitePoints; $i++) {
            $command->addWhitePoint($i, $whiteOne, $whiteTwo);
        }

        $gameId = $this->dispatch($command);


        $latestGame = $this->games->getLatestGame();
        $latestSeason = $this->games->getLatestSeason();


        $this->assertEquals($gameNumber, $latestGame);
        $this->assertEquals($season, $latestSeason);
    }



    public function test_game_scenario()
    {
        $gameDate = '2016-07-27';
        $start = '9:00 AM';
        $end = '9:30 AM';
        $playoff = 0;
        $season = 15;
        $gameNumber = 24;
        $blackPoints = 10;
        $whitePoints = 1;

        $chrisLee = $this->genPlayerWithName("Chris", "Lee");
        $zachR = $this->genPlayerWithName("Zach", "Riet");
        $kevenB = $this->genPlayerWithName("Keven", "Barron");
        $ghislainD = $this->genPlayerWithName("Ghislain", "Druwé");
        $paulE = $this->genPlayerWithName("Paul", "Everton");
        $paulG = $this->genPlayerWithName("Paul", "Gagne");
        $davidR = $this->genPlayerWithName("David", "Robin");
        $chrisR = $this->genPlayerWithName("Chris", "Robin");
        $jeremieR  = $this->genPlayerWithName("Jeremie", "Robin");
        $jacquesAuger = $this->genPlayerWithName("Jacques", "Auger");
        $colinLemoine = $this->genPlayerWithName("Colin", "Lemoine");



        $command = new \App\Commands\Game\AddFullGame(
            $gameDate,
            $start,
            $end,
            $playoff,
            $season,
            $gameNumber
        );


        $command->addWhiteGoalie($chrisLee);
        $command->addWhitePlayer($zachR);
        $command->addWhitePlayer($kevenB);
        $command->addWhitePlayer($ghislainD);
        $command->addWhitePlayer($paulE);
        $command->addWhitePlayer($paulG);


        $command->addBlackGoalie($davidR);
        $command->addBlackPlayer($chrisR);
        $command->addBlackPlayer($jeremieR);
        $command->addBlackPlayer($jacquesAuger);
        $command->addBlackPlayer($colinLemoine);


        $command->addWhitePoint(1, $ghislainD, $zachR);


        $command->addBlackPoint(1, $chrisR, $colinLemoine);
        $command->addBlackPoint(2, $jacquesAuger, $chrisR);
        $command->addBlackPoint(3, $jacquesAuger, $chrisR);
        $command->addBlackPoint(4, $chrisR, $colinLemoine);
        $command->addBlackPoint(5, $chrisR, '');
        $command->addBlackPoint(6, $colinLemoine, $jacquesAuger);
        $command->addBlackPoint(7, $colinLemoine, $chrisR);
        $command->addBlackPoint(8, $jacquesAuger, $colinLemoine);
        $command->addBlackPoint(9, $jeremieR, $chrisR);
        $command->addBlackPoint(10, $colinLemoine, $jeremieR);

        $gameId = $this->dispatch($command);

        /** @var \App\Aggregates\Game $gameAgg */
        $gameAgg = $this->aggregateRepository->get($gameId);
        $game = $this->games->getById($gameId);

        $this->assertEquals('30', $game['gameTime']);
        $this->assertEquals($gameDate, $game['gameDate']);
        $this->assertEquals($playoff, $game['playoff']);
        $this->assertEquals($season, $game['season']);
        $this->assertEquals($gameNumber, $game['gameNumber']);

        $this->assertEquals($gameAgg->getGameDate(), $game['gameDate']);
        $this->assertEquals($gameAgg->getPlayoff(), $game['playoff']);
        $this->assertEquals($gameAgg->getSeason(), $game['season']);
        $this->assertEquals($gameAgg->getGameNumber(), $game['gameNumber']);

        $this->assertEquals($blackPoints, $game[\App\Aggregates\Game::BLACK_TEAM."Points"]);
        $this->assertEquals($whitePoints, $game[\App\Aggregates\Game::WHITE_TEAM."Points"]);


        $this->assertEquals($blackPoints, $gameAgg->getBlackPointTotal());
        $this->assertEquals($whitePoints, $gameAgg->getWhitePointTotal());

        $this->assertEquals($blackPoints, $game[Game::BLACK_TEAM."Points"]);
        $this->assertEquals($whitePoints, $game[Game::WHITE_TEAM."Points"]);

        $this->assertEquals($blackPoints, count($game[Game::BLACK_TEAM."Goals"]));
        $this->assertEquals($whitePoints, count($game[Game::WHITE_TEAM."Goals"]));

        $this->assertEquals(Game::BLACK_TEAM, $game["winningTeam"]);


        $this->assertEquals($ghislainD, $game[Game::WHITE_TEAM."Goals"][1]['goalPlayerId']);
        $this->assertEquals($zachR, $game[Game::WHITE_TEAM."Goals"][1]['assistPlayerId']);


        $this->assertEquals($chrisR, $game[Game::BLACK_TEAM."Goals"][1]['goalPlayerId']);
        $this->assertEquals($colinLemoine, $game[Game::BLACK_TEAM."Goals"][1]['assistPlayerId']);

        $this->assertEquals($jacquesAuger, $game[Game::BLACK_TEAM."Goals"][2]['goalPlayerId']);
        $this->assertEquals($chrisR, $game[Game::BLACK_TEAM."Goals"][2]['assistPlayerId']);


        $this->assertEquals($jacquesAuger, $game[Game::BLACK_TEAM."Goals"][3]['goalPlayerId']);
        $this->assertEquals($chrisR, $game[Game::BLACK_TEAM."Goals"][3]['assistPlayerId']);

        $this->assertEquals($chrisR, $game[Game::BLACK_TEAM."Goals"][4]['goalPlayerId']);
        $this->assertEquals($colinLemoine, $game[Game::BLACK_TEAM."Goals"][4]['assistPlayerId']);

        $this->assertEquals($chrisR, $game[Game::BLACK_TEAM."Goals"][5]['goalPlayerId']);
        $this->assertEquals('', $game[Game::BLACK_TEAM."Goals"][5]['assistPlayerId']);

        $this->assertEquals($colinLemoine, $game[Game::BLACK_TEAM."Goals"][6]['goalPlayerId']);
        $this->assertEquals($jacquesAuger, $game[Game::BLACK_TEAM."Goals"][6]['assistPlayerId']);

        $this->assertEquals($colinLemoine, $game[Game::BLACK_TEAM."Goals"][7]['goalPlayerId']);
        $this->assertEquals($chrisR, $game[Game::BLACK_TEAM."Goals"][7]['assistPlayerId']);

        $this->assertEquals($jacquesAuger, $game[Game::BLACK_TEAM."Goals"][8]['goalPlayerId']);
        $this->assertEquals($colinLemoine, $game[Game::BLACK_TEAM."Goals"][8]['assistPlayerId']);

        $this->assertEquals($jeremieR, $game[Game::BLACK_TEAM."Goals"][9]['goalPlayerId']);
        $this->assertEquals($chrisR, $game[Game::BLACK_TEAM."Goals"][9]['assistPlayerId']);

        $this->assertEquals($colinLemoine, $game[Game::BLACK_TEAM."Goals"][10]['goalPlayerId']);
        $this->assertEquals($jeremieR, $game[Game::BLACK_TEAM."Goals"][10]['assistPlayerId']);
    }


    public function test_game_edit_scenario()
    {
        $gameDate = '2016-07-27';
        $start = '9:00 AM';
        $end = '9:30 AM';
        $playoff = 0;
        $season = 15;
        $gameNumber = 24;
        $blackPoints = 10;
        $whitePoints = 2;

        $chrisLee = $this->genPlayerWithName("Chris", "Lee");
        $zachR = $this->genPlayerWithName("Zach", "Riet");
        $kevenB = $this->genPlayerWithName("Keven", "Barron");
        $ghislainD = $this->genPlayerWithName("Ghislain", "Druwé");
        $paulE = $this->genPlayerWithName("Paul", "Everton");
        $paulG = $this->genPlayerWithName("Paul", "Gagne");
        $davidR = $this->genPlayerWithName("David", "Robin");
        $chrisR = $this->genPlayerWithName("Chris", "Robin");
        $jeremieR  = $this->genPlayerWithName("Jeremie", "Robin");
        $jacquesAuger = $this->genPlayerWithName("Jacques", "Auger");
        $colinLemoine = $this->genPlayerWithName("Colin", "Lemoine");



        $command = new \App\Commands\Game\AddFullGame(
            $gameDate,
            $start,
            $end,
            $playoff,
            $season,
            $gameNumber
        );


        $command->addWhiteGoalie($chrisLee);
        $command->addWhitePlayer($zachR);
        $command->addWhitePlayer($kevenB);
        $command->addWhitePlayer($ghislainD);
        $command->addWhitePlayer($paulE);
        $command->addWhitePlayer($paulG);


        $command->addBlackGoalie($davidR);
        $command->addBlackPlayer($chrisR);
        $command->addBlackPlayer($jeremieR);
        $command->addBlackPlayer($jacquesAuger);
        $command->addBlackPlayer($colinLemoine);


        $command->addWhitePoint(1, $ghislainD, $zachR);


        $command->addBlackPoint(1, $chrisR, $colinLemoine);
        $command->addBlackPoint(2, $jacquesAuger, $chrisR);
        $command->addBlackPoint(3, $jacquesAuger, $chrisR);
        $command->addBlackPoint(4, $chrisR, $colinLemoine);
        $command->addBlackPoint(5, $chrisR, '');
        $command->addBlackPoint(6, $colinLemoine, $jacquesAuger);
        $command->addBlackPoint(7, $colinLemoine, $chrisR);
        $command->addBlackPoint(8, $jacquesAuger, $colinLemoine);
        $command->addBlackPoint(9, $jeremieR, $chrisR);
        $command->addBlackPoint(10, $colinLemoine, $jeremieR);

        $gameId = $this->dispatch($command);

        $gameDate = '2016-07-28';
        $start = '9:00 AM';
        $end = '9:45 AM';
        $playoff = 1;
        $season = 16;
        $gameNumber = 2;
        $command = new \App\Commands\Game\EditFullGame(
            $gameId,
            $gameDate,
            $start,
            $end,
            $playoff,
            $season,
            $gameNumber
        );


        $command->addWhiteGoalie($chrisLee);
        $command->addWhitePlayer($zachR);
        $command->addWhitePlayer($kevenB);
        $command->addWhitePlayer($ghislainD);
        $command->addWhitePlayer($jeremieR);
        $command->addWhitePlayer($paulG);


        $command->addBlackGoalie($davidR);
        $command->addBlackPlayer($chrisR);
        $command->addBlackPlayer($paulE);
        $command->addBlackPlayer($jacquesAuger);
        $command->addBlackPlayer($colinLemoine);


        $command->addWhitePoint(1, $jeremieR, $paulG);
        $command->addWhitePoint(2, $jeremieR, $paulG);


        $command->addBlackPoint(1, $paulE, $colinLemoine);
        $command->addBlackPoint(2, $paulE, $chrisR);
        $command->addBlackPoint(3, $paulE, $chrisR);
        $command->addBlackPoint(4, $paulE, $colinLemoine);
        $command->addBlackPoint(5, $paulE, '');
        $command->addBlackPoint(6, $paulE, $jacquesAuger);
        $command->addBlackPoint(7, $paulE, $chrisR);
        $command->addBlackPoint(8, $paulE, $colinLemoine);
        $command->addBlackPoint(9, $paulE, $chrisR);
        $command->addBlackPoint(10, $paulE, $colinLemoine);

        $gameId = $this->dispatch($command);




        /** @var \App\Aggregates\Game $gameAgg */
        $gameAgg = $this->aggregateRepository->get($gameId);
        $game = $this->games->getById($gameId);


        $this->assertEquals($gameDate, $game['gameDate']);
        $this->assertEquals($playoff, $game['playoff']);
        $this->assertEquals($season, $game['season']);
        $this->assertEquals($gameNumber, $game['gameNumber']);
        $this->assertEquals($gameAgg->getGameDate(), $game['gameDate']);
        $this->assertEquals($gameAgg->getPlayoff(), $game['playoff']);
        $this->assertEquals($gameAgg->getSeason(), $game['season']);
        $this->assertEquals($gameAgg->getGameNumber(), $game['gameNumber']);


        $this->assertEquals('45', $game['gameTime']);

        $this->assertEquals($blackPoints, $game[\App\Aggregates\Game::BLACK_TEAM."Points"]);
        $this->assertEquals($whitePoints, $game[\App\Aggregates\Game::WHITE_TEAM."Points"]);


        $this->assertEquals($blackPoints, $gameAgg->getBlackPointTotal());
        $this->assertEquals($whitePoints, $gameAgg->getWhitePointTotal());

        $this->assertEquals($blackPoints, $game[Game::BLACK_TEAM."Points"]);
        $this->assertEquals($whitePoints, $game[Game::WHITE_TEAM."Points"]);

        $this->assertEquals($blackPoints, count($game[Game::BLACK_TEAM."Goals"]));
        $this->assertEquals($whitePoints, count($game[Game::WHITE_TEAM."Goals"]));

        $this->assertEquals(Game::BLACK_TEAM, $game["winningTeam"]);


        $this->assertEquals($jeremieR, $game[Game::WHITE_TEAM."Goals"][1]['goalPlayerId']);
        $this->assertEquals($paulG, $game[Game::WHITE_TEAM."Goals"][1]['assistPlayerId']);
        $this->assertEquals($jeremieR, $game[Game::WHITE_TEAM."Goals"][2]['goalPlayerId']);
        $this->assertEquals($paulG, $game[Game::WHITE_TEAM."Goals"][2]['assistPlayerId']);


        $this->assertEquals($paulE, $game[Game::BLACK_TEAM."Goals"][1]['goalPlayerId']);
        $this->assertEquals($colinLemoine, $game[Game::BLACK_TEAM."Goals"][1]['assistPlayerId']);

        $this->assertEquals($paulE, $game[Game::BLACK_TEAM."Goals"][2]['goalPlayerId']);
        $this->assertEquals($chrisR, $game[Game::BLACK_TEAM."Goals"][2]['assistPlayerId']);


        $this->assertEquals($paulE, $game[Game::BLACK_TEAM."Goals"][3]['goalPlayerId']);
        $this->assertEquals($chrisR, $game[Game::BLACK_TEAM."Goals"][3]['assistPlayerId']);

        $this->assertEquals($paulE, $game[Game::BLACK_TEAM."Goals"][4]['goalPlayerId']);
        $this->assertEquals($colinLemoine, $game[Game::BLACK_TEAM."Goals"][4]['assistPlayerId']);

        $this->assertEquals($paulE, $game[Game::BLACK_TEAM."Goals"][5]['goalPlayerId']);
        $this->assertEquals('', $game[Game::BLACK_TEAM."Goals"][5]['assistPlayerId']);

        $this->assertEquals($paulE, $game[Game::BLACK_TEAM."Goals"][6]['goalPlayerId']);
        $this->assertEquals($jacquesAuger, $game[Game::BLACK_TEAM."Goals"][6]['assistPlayerId']);

        $this->assertEquals($paulE, $game[Game::BLACK_TEAM."Goals"][7]['goalPlayerId']);
        $this->assertEquals($chrisR, $game[Game::BLACK_TEAM."Goals"][7]['assistPlayerId']);

        $this->assertEquals($paulE, $game[Game::BLACK_TEAM."Goals"][8]['goalPlayerId']);
        $this->assertEquals($colinLemoine, $game[Game::BLACK_TEAM."Goals"][8]['assistPlayerId']);

        $this->assertEquals($paulE, $game[Game::BLACK_TEAM."Goals"][9]['goalPlayerId']);
        $this->assertEquals($chrisR, $game[Game::BLACK_TEAM."Goals"][9]['assistPlayerId']);

        $this->assertEquals($paulE, $game[Game::BLACK_TEAM."Goals"][10]['goalPlayerId']);
        $this->assertEquals($colinLemoine, $game[Game::BLACK_TEAM."Goals"][10]['assistPlayerId']);
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
