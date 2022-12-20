<?php

namespace App\ApiResource\Player;

use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Put;
use App\Entity\Player;
use App\Entity\Sport;
use App\State\Player\Processor\UpdateProfileProcessor;
use App\State\Player\Provider\CurrentUserPofileProvider;

#[Get(
    uriTemplate: '/me',
    openapiContext: [
        'tags' => ['Player'],
    ],
    provider: CurrentUserPofileProvider::class
)]
#[Put(
    uriTemplate: '/me',
    openapiContext: [
        'tags' => ['Player'],
    ],
    read: false,
    processor: UpdateProfileProcessor::class,
    denormalizationContext: ['relation_mapping' => ['sports' => Sport::class]],
)]
class Profile
{
    public function __construct(
        public readonly string $username,
        public readonly string $email,
        public readonly string $lastname,
        public readonly string $firstname,
        /** @var array<Sport> $sports */
        public ?array $sports,
    ) {
    }

    public static function fromPlayer(Player $player): self
    {
        return new Profile($player->getUsername(), $player->getEmail(), $player->getLastName(), $player->getFirstName(), array_values($player->getSports()->toArray()));
    }

    public function updateEntity(Player &$player)
    {
        $player->setEmail($this->email);
        $player->setUsername($this->username);
        $player->setLastName($this->lastname);
        $player->setFirstName($this->firstname);

        $player->resetSports();

        foreach ($this->sports as $sport) {
            $player->addSport($sport);
        }
    }
}
