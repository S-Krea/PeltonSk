<?php

namespace App\Mail;

use App\Entity\Player;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Translation\TranslatableMessage;

class PlayerMailer
{
    public function __construct(
        private readonly MailerInterface $mailer,
        private readonly string $publicFrom
    ) {
    }

    public function sendRegistrationMail(Player $player)
    {
        $message = new TemplatedEmail();
        $message->from($this->publicFrom);
        $message->to($player->getEmail());
        $message->subject(new TranslatableMessage('mail.player_registration.subject'));
        $message->htmlTemplate('mails/player/registration.html.twig')
            ->context(['player' => $player]);

        $this->mailer->send($message);
    }
}
