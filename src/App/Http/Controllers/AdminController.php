<?php

namespace App\Http\Controllers;

use App\Commands\Game\AddFullGame;
use App\Commands\Player\AddPlayer;
use App\Commands\Player\EditPlayer;
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
        view()->share('active_navbar_addgame', true);
        $players = $this->players->getAll();
        return view('app.admin.games.add', ["players" => $players]);
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

    public function addPlayer()
    {
        view()->share('active_navbar_addplayer', true);
        return view('app.admin.players.add');
    }

    public function storePlayer(Request $request)
    {
        $validationRules = [
            'firstName' => 'required',
            'lastName' => 'required'
        ];

        $this->validate($request, $validationRules);
        $data = $request->all();
        $command = new AddPlayer(
            $data['firstName'],
            $data['lastName']
        );

        $this->dispatchCommand($command,$request);

        return redirect()->route('player-stats');
    }

    public function editPlayer($playerId)
    {
        $player = $this->players->getById($playerId);
        return view('app.admin.players.edit', ["player" => $player]);
    }

    public function updatePlayer(Request $request, $playerId)
    {
        $validationRules = [
            'firstName' => 'required',
            'lastName' => 'required'
        ];

        $this->validate($request, $validationRules);
        $data = $request->all();
        $command = new EditPlayer(
            $playerId,
            $data['firstName'],
            $data['lastName']
        );

        $this->dispatchCommand($command,$request);

        return redirect()->route('player-stats');
    }
}
