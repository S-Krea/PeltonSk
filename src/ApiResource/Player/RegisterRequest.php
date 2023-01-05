<?php

namespace App\ApiResource\Player;

use ApiPlatform\Metadata\Post;
use App\State\Player\Processor\UserRegisterProcessor;
use App\Validator\SecuredPassword;
use Symfony\Component\Validator\Constraints\Email;

#[Post(
    uriTemplate: '/register',
    openapiContext: [
        'summary' => 'Register an account',
        'description' => 'To register an new account, provide the following information',
        'tags' => ['Player'],
        '',
    ],
    processor: UserRegisterProcessor::class,
)]
final class RegisterRequest
{
    public function __construct(
        public readonly string $username,
        #[Email]
        public readonly string $email,
        #[SecuredPassword]
        public readonly string $password,
        public readonly string $lastname,
        public readonly string $firstname,
        public readonly ?string $phoneNumber = null,
    ) {
    }
}
