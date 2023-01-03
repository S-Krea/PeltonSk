<?php

namespace App\Controller;

use App\Entity\Player;
use App\Repository\PlayerRepository;
use App\Validator\SecuredPassword;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Twig\Environment;

#[Route(path: '/reset_password/{token}', name: 'reset_password')]
class ResetPasswordController
{
    public function __construct(
        protected readonly PlayerRepository $playerRepository,
        protected readonly RequestStack $requestStack,
        protected readonly Environment $twig,
        protected readonly CsrfTokenManagerInterface $csrfTokenManager,
        protected readonly UserPasswordHasherInterface $passwordHasher,
        protected readonly ValidatorInterface $validator,
        protected string $appUrl
    ) {
    }

    public function __invoke($token)
    {
        $player = $this->playerRepository->findOneBy(['resetToken' => $token]);
        $passwords = null;

        if (!$player) {
            throw new NotFoundHttpException();
        }

        $request = $this->requestStack->getCurrentRequest();
        $submittedToken = $request->request->get('_csrf');

        $csrfToken = new CsrfToken('update_password_'.$player->getEmail(), $submittedToken);
        if ($request->isMethod(Request::METHOD_POST)) {
            if (!$this->csrfTokenManager->isTokenValid($csrfToken)) {
                $this->addFlash('error', 'Erreur CSRF');

                return $this->renderForm($player);
            }

            $passwords = $request->get('password');
            if ($passwords['first'] !== $passwords['second']) {
                $this->addFlash('error', 'Les 2 mots de passes ne sont pas identiques.');

                return $this->renderForm($player);
            }

            $validationErrors = $this->validator->validate($passwords['first'], new SecuredPassword());
            if ($validationErrors->count() > 0) {
                foreach ($validationErrors as $validationError) {
                    $this->addFlash('error', $validationError->getMessage());
                }

                return $this->renderForm($player);
            }

            $hashedPassword = $this->passwordHasher->hashPassword($player, $passwords['first']);
            $player->setPassword($hashedPassword);
            $this->playerRepository->save($player, true);

            $this->addFlash('success', 'Mot de passe modifié.');

            return new RedirectResponse($this->appUrl);
        }

        return $this->renderForm($player);
    }

    protected function addFlash($type, $message)
    {
        $this->requestStack->getSession()->getFlashBag()->add($type, $message);
    }

    protected function renderForm(Player $player)
    {
        return new Response($this->twig->render('security/reset_password.html.twig', ['user' => $player]));
    }
}
