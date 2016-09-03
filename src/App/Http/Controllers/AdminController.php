<?php

namespace App\Http\Controllers;

use App\Commands\Game\AddFullGame;
use App\Listeners\Games;
use App\Listeners\Players;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;

class AdminController extends Controller
{
    private $games;
    private $players;

    /**
     * AdminController constructor.
     * @param $games
     */
    public function __construct(Games $games, Players $players)
    {
        $this->games = $games;
        $this->players = $players;
        $this->middleware('auth');
        $this->middleware('web');

    }


    public function addGame()
    {
        $players = $this->players->getAll();
        return view('app.admin.game.add', ["players" => $players]);
    }

    public function storeGame(Request $request)
    {


        $validationRules = [
            'gameDate' => 'required|date_format:Y-m-d',
            'start' => 'required|date_format:g:i A',
            'end' => 'required|date_format:g:i A',
            'playoff' => 'required',
            'season' => 'required',
            'gameNumber' => 'required'
        ];

        for($i=1; $i<=18; $i++){
            if ($i > 1) {
                $prev = $i-1;
                $validationRules['blackGoal'.$prev] = 'required_with:blackGoal'.$i.'|required_with:blackAssist'.$prev;
                $validationRules['whiteGoal'.$prev] = 'required_with:whiteGoal'.$i.'|required_with:whiteAssist'.$prev;
            }
        }

        $this->validate($request, $validationRules);
        $data = $request->all();
        $command = new AddFullGame(
            $data['gameDate'],
            $data['start'],
            $data['end'],
            $data['playoff'],
            $data['season'],
            $data['gameNumber']
        );




        foreach ($data as $input => $value) {
            if (!is_null($value)) {
                if (strpos($input, 'bplayer') !== false) {
                    $command->addBlackPlayer($value);
                }
                if (strpos($input, 'wplayer') !== false) {
                    $command->addWhitePlayer($value);
                }
                if (strpos($input, 'bgoalie') !== false) {
                    $command->addBlackGoalie($value);
                }
                if (strpos($input, 'wgoalie') !== false) {
                    $command->addWhiteGoalie($value);
                }
            }
        }

        for ($i = 1; $i <= 18; $i++) {
            $blackG = '';
            $blackA = '';
            $whiteG = '';
            $whiteA = '';
            if (isset($data['blackGoal'.$i])) {
                $blackG = $data['blackGoal'.$i];
            }
            if (isset($data['blackAssist'.$i])) {
                $blackA = $data['blackAssist'.$i];
            }
            if (isset($data['whiteGoal'.$i])) {
                $whiteG = $data['whiteGoal'.$i];
            }
            if (isset($data['whiteAssist'.$i])) {
                $whiteA = $data['whiteAssist'.$i];
            }
            if(trim($blackG) != ''){
                $command->addBlackPoint($i, $blackG, $blackA);
            }
            if(trim($whiteG) != ''){
                $command->addWhitePoint($i, $whiteG, $whiteA);
            }
        }


        $this->dispatchCommand($command,$request);

        return redirect()->route('player-stats');
    }


    public function randopage()
    {
        return view('app.admin.rando');
    }

    public function hack()
    {
        $gameDate = '2016-07-27';
        $start = '9:00 AM';
        $end = '9:30 AM';
        $playoff = 0;
        $season = $this->games->getLatestSeason();
        $gameNumber = $this->games->getLatestGame()+1;

        $command = new \App\Commands\Game\AddFullGame(
            $gameDate,
            $start,
            $end,
            $playoff,
            $season,
            $gameNumber
        );

        $allPlayers = $this->players->getAll();
        $playerCount = 0;
        $blackPlayer = '';


        foreach ($allPlayers as $allPlayer) {
            if ($allPlayer['lastName'] !== 'Everton') {
                $playerCount++;
                if ($playerCount > 9) {
                    continue;
                }
                if ($playerCount === 1) {
                    $command->addBlackGoalie($allPlayer['id']);
                } elseif ($playerCount === 6) {
                    $command->addWhiteGoalie($allPlayer['id']);
                } elseif ($playerCount < 6) {
                    $blackPlayer = $allPlayer['id'];
                    $command->addBlackPlayer($allPlayer['id']);
                } else {
                    $command->addWhitePlayer($allPlayer['id']);
                }
            }
        }
        $me = '';
        foreach ($allPlayers as $allPlayer) {
            if ($allPlayer['lastName'] === 'Everton') {
                $command->addWhitePlayer($allPlayer['id']);
                $me = $allPlayer['id'];
            }
        }


        $hack = intval(Input::get('goals'));


        for ($i = 1; $i <= $hack; $i++) {

            $command->addWhitePoint($i, $me, '');
        }
        for ($i = 1; $i <= 10; $i++) {
            $command->addBlackPoint($i, $blackPlayer, '');
        }

        $this->dispatch($command);

        return redirect()->route('player-stats');
    }

    public function hackPost(Request $request)
    {

    }
}
