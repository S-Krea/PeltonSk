<?php

namespace App\Mail;

use App\Entity\Player;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Translation\TranslatableMessage;
use Symfony\Contracts\Translation\TranslatorInterface;

class PlayerMailer
{
    public function __construct(
        private readonly MailerInterface $mailer,
        private readonly string $publicFrom,
        private readonly TranslatorInterface $translator
    ) {
    }

    public function sendRegistrationMail(Player $player)
    {
        $subject = new TranslatableMessage('mail.player_registration.subject', ['app_name' => 'Padel Play']);
        $message = new TemplatedEmail();
        $message->from($this->publicFrom);
        $message->to($player->getEmail());
        $message->subject($subject->trans($this->translator));
        $message
            ->htmlTemplate('mails/player/registration.html.twig')
            ->context(['player' => $player]);

        $this->mailer->send($message);
    }

    public function sendLostPassword(Player $player)
    {
        $subject = new TranslatableMessage('mail.password_request.subject', ['app_name' => 'Padel Play']);
        $message = new TemplatedEmail();
        $message->from($this->publicFrom);
        $message->to($player->getEmail());
        $message->subject($subject);
        $message->htmlTemplate('mails/player/password_request.html.twig')
            ->context(['user' => $player]);

        $this->mailer->send($message);
    }
}
