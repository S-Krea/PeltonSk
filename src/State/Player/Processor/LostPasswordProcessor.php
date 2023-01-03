<?php

namespace App\State\Player\Processor;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\ApiResource\Player\LostPasswordRequest;
use App\Mail\PlayerMailer;
use App\Repository\PlayerRepository;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class LostPasswordProcessor implements ProcessorInterface
{
    public function __construct(protected readonly PlayerMailer $mailer, protected readonly PlayerRepository $playerRepository)
    {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = [])
    {
        if (!$data instanceof LostPasswordRequest) {
            throw new BadRequestHttpException();
        }

        $email = $data->email;
        $user = $this->playerRepository->findOneBy(['email' => $email]);

        if (!$user) {
            return;
        }

        $user->setResetAt(new \DateTimeImmutable());
        $user->setResetToken(bin2hex(random_bytes(32)));
        $this->playerRepository->save($user, true);
        $this->mailer->sendLostPassword($user);

        return;
    }
}
