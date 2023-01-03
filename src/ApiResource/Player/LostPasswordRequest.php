<?php

namespace App\ApiResource\Player;

use ApiPlatform\Metadata\Post;
use App\State\Player\Processor\LostPasswordProcessor;

#[Post(
    uriTemplate: '/password/request',
    openapiContext: [
        'tags' => ['Player'],
    ],
    read: false,
    processor: LostPasswordProcessor::class
)]
class LostPasswordRequest
{
    public string $email;
}
