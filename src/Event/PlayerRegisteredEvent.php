<?php

namespace App\Event;

use App\Entity\Player;
use Symfony\Contracts\EventDispatcher\Event;

class PlayerRegisteredEvent extends Event
{
    public const NAME = 'player.registered';

    public function __construct(protected readonly Player $player)
    {
    }

    public function getPlayer(): Player
    {
        return $this->player;
    }
}
