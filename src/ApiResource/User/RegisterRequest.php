<?php

namespace App\ApiResource\User;

use ApiPlatform\Metadata\Post;
use App\Entity\User;
use App\State\User\Processor\UserRegisterProcessor;

#[Post(
    class: User::class,
    shortName: 'User',
    uriTemplate: '/register',
    openapiContext: ['summary' => 'Register an account', 'description' => 'To register an new account, provide the following information'],
    processor: UserRegisterProcessor::class,
    read: false,
)]
final class RegisterRequest
{
    public function __construct(
        public readonly string $username,
        public readonly string $email,
        public readonly string $password,
        public readonly string $lastname,
        public readonly string $firstname
    ) {
    }
}
