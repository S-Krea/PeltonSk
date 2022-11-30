<?php

namespace App\State\Player\Provider;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\ApiResource\Player\Profile;
use App\Entity\Player;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class CurrentUserPofileProvider implements ProviderInterface
{
    private ?UserInterface $user;

    public function __construct(Security $security)
    {
        $this->user = $security->getUser();
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): Profile|null
    {
        if (!$this->user instanceof Player) {
            return null;
        }

        return Profile::fromPlayer($this->user);
    }
}
