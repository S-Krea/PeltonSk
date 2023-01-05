<?php

namespace App\State\Player\Processor;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use ApiPlatform\Symfony\Validator\Exception\ValidationException;
use App\ApiResource\Player\Profile;
use App\ApiResource\Player\RegisterRequest;
use App\Entity\Player;
use App\Event\PlayerRegisteredEvent;
use App\Repository\UserRepository;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UserRegisterProcessor implements ProcessorInterface
{
    public function __construct(
        protected readonly UserPasswordHasherInterface $hasher,
        protected readonly UserRepository $userRepository,
        protected readonly ValidatorInterface $validator,
        protected readonly EventDispatcherInterface $eventDispatcher
    ) {
    }

    /**
     * @param RegisterRequest $data
     */
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): Profile
    {
        $user = new Player();

        $hashedPassword = $this->hasher->hashPassword($user, $data->password);

        $user->setEmail($data->email);
        $user->setFirstName($data->firstname);
        $user->setLastName($data->lastname);
        $user->setUsername($data->username);
        $user->setPassword($hashedPassword);
        $user->setPhoneNumber($data->phoneNumber);
        $user->generateVerification();

        $errors = $this->validator->validate($user);
        if ($errors->count() > 0) {
            throw new ValidationException($errors);
        }

        $this->userRepository->save($user, true);
        $registrationEvent = new PlayerRegisteredEvent($user);
        $this->eventDispatcher->dispatch($registrationEvent);

        return Profile::fromPlayer($user);
    }
}
