<?php

namespace App\Infrastructure\Twig;

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
        if (trim($playerId) == '') {
            return '';
        } else {
            $player = $playerCollection[$playerId];
            if ($short) {
                return '<a href="/players/'.$playerId.'/stats/player" >'.$player['shortName'].'</a>';
            } else {
                return '<a href="/players/'.$playerId.'/stats/player" >'.$player['fullName'].'</a>';
            }
        }
    }
}
