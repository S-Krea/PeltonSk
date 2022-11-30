<?php

namespace App\ApiResource\Player;

use ApiPlatform\Metadata\Get;
use App\Entity\Player;
use App\State\Player\Provider\CurrentUserPofileProvider;

#[Get(
    uriTemplate: '/me',
    provider: CurrentUserPofileProvider::class,
    openapiContext: [
        'tags' => ['Player'],
    ]
)]
class Profile
{
    public function __construct(
        public readonly string $username,
        public readonly string $email,
        public readonly string $lastname,
        public readonly string $firstname,
    ) {
    }

    public static function fromPlayer(Player $player): self
    {
        return new Profile($player->getUsername(), $player->getEmail(), $player->getLastName(), $player->getFirstName());
    }
}
