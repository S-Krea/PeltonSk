<?php

namespace App\State\User\Processor;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use ApiPlatform\Symfony\Validator\Exception\ValidationException;
use App\ApiResource\User\Profile;
use App\ApiResource\User\RegisterRequest;
use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UserRegisterProcessor implements ProcessorInterface
{
    public function __construct(
        protected readonly UserPasswordHasherInterface $hasher,
        protected readonly UserRepository $userRepository,
        protected readonly ValidatorInterface $validator
    ) {
    }

    /**
     * @param RegisterRequest $data
     */
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): Profile
    {
        $user = new User();

        $hashedPassword = $this->hasher->hashPassword($user, $data->password);

        $user->setEmail($data->email);
        $user->setFirstName($data->firstname);
        $user->setLastName($data->lastname);
        $user->setUsername($data->username);
        $user->setPassword($hashedPassword);

        $errors = $this->validator->validate($user);
        if ($errors->count() > 0) {
            throw new ValidationException($errors);
        }

        $this->userRepository->save($user, true);

        return new Profile(
            $user->getUsername(),
            $user->getEmail(),
            $user->getLastName(),
            $user->getFirstName()
        );
    }
}
