<?php

namespace App\Controller;

use App\Repository\PlayerRepository;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/verify/{token}', name: 'verify_account')]
class VerifyPlayerAccountController
{
    public function __construct(protected readonly PlayerRepository $playerRepository, protected string $appUrl)
    {
    }

    public function __invoke(Request $request, $token)
    {
        $player = $this->playerRepository->findOneBy(['verificationToken' => $token]);
        if (!$player) {
            throw new NotFoundHttpException();
        }

        $player->setVerified(true);
        $player->setVerificationToken(null);

        $this->playerRepository->save($player, true);

        return new RedirectResponse($this->appUrl);
    }
}
