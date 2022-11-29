<?php

namespace App\ApiResource\User;

use ApiPlatform\Metadata\Get;
use App\Entity\User;
use App\State\User\Provider\CurrentUserPofileProvider;

#[Get(shortName: 'User', uriTemplate: '/me', provider: CurrentUserPofileProvider::class)]
class Profile
{
    public function __construct(
        public readonly string $username,
        public readonly string $email,
        public readonly string $lastname,
        public readonly string $firstname
    ) {
    }

    public static function fromUser(User $user): self
    {
        return new Profile($user->getUsername(), $user->getEmail(), $user->getLastName(), $user->getFirstName());
    }
}
