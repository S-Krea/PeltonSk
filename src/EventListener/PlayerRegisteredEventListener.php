<?php

namespace App\EventListener;

use App\Event\PlayerRegisteredEvent;
use App\Mail\PlayerMailer;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

#[AsEventListener(event: PlayerRegisteredEvent::class)]
class PlayerRegisteredEventListener
{
    public function __construct(protected readonly PlayerMailer $playerMailer)
    {
    }

    public function __invoke(PlayerRegisteredEvent $event): void
    {
        $player = $event->getPlayer();

        $this->playerMailer->sendRegistrationMail($player);
    }
}
