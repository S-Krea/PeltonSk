<?php

namespace App\State\Player\Processor;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\ApiResource\Player\Profile;
use App\Repository\UserRepository;
use Symfony\Bundle\SecurityBundle\Security;

class UpdateProfileProcessor implements ProcessorInterface
{
    public function __construct(protected readonly Security $security, protected readonly UserRepository $userRepository)
    {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = [])
    {
        $player = $this->security->getUser();
        /* @var Profile $data */
        $data->updateEntity($player);

        $this->userRepository->save($player, true);

        return Profile::fromPlayer($player);
    }
}
