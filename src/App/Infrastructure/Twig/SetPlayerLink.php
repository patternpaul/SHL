<?php

namespace App\Infrastructure\Twig;

use Illuminate\Support\Facades\Auth;

class SetPlayerLink extends \Twig_Extension
{
    /** @noinspection PhpMissingParentCallCommonInspection */
    public function getFunctions()
    {
        return [
            'classname' => new \Twig_SimpleFunction('setPlayerLink', [$this, 'setPlayerLink'])
        ];
    }

    public function getName()
    {
        return 'set_player_link_twig_extension';
    }

    /** @noinspection PhpInconsistentReturnPointsInspection */
    public function setPlayerLink($playerId, $playerCollection, $short = false)
    {
        $route = '';
        if (Auth::check()) {
            $route = route('admin-player-page-player-stats',[$playerId]);
        } else {
            $route = route('player-page-player-stats',[$playerId]);
        }

        if (trim($playerId) == '') {
            return '';
        } else {
            $player = $playerCollection[$playerId];
            if ($short) {
                return '<a href="'.$route.'" >'.$player['shortName'].'</a>';
            } else {
                return '<a href="'.$route.'" >'.$player['fullName'].'</a>';
            }
        }
    }
}
