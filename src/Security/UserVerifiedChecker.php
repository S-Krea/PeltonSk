<?php

namespace App\Security;

use App\Entity\Player;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAccountStatusException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Translation\TranslatableMessage;
use Symfony\Contracts\Translation\TranslatorInterface;

class UserVerifiedChecker implements UserCheckerInterface
{
    public function __construct(protected readonly TranslatorInterface $translator)
    {
    }

    public function checkPreAuth(UserInterface $user)
    {
        if (!$user instanceof Player) {
            return;
        }

        if (!$user->isVerified()) {
            $message = new TranslatableMessage('authentication.unverified_account', domain: 'validators');
            throw new CustomUserMessageAccountStatusException($message->trans($this->translator));
        }
    }

    public function checkPostAuth(UserInterface $user)
    {
        // TODO: Implement checkPostAuth() method.
    }
}
