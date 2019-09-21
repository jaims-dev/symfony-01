<?php


namespace App\Controller;

use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController
{
    /**
     * @var \Twig_Environment
     */
    private $twig;

    public function __construct(\Twig_Environment $twig) {
        $this->twig = $twig;
    }

    /**
     * @Route("/login", name = "security_login")
     * @param AuthenticationUtils $authenticationUtils
     * @return Response
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function login(AuthenticationUtils $authenticationUtils) {
        return new Response( $this->twig->render('security/login.html.twig', [
                "last_username" => $authenticationUtils->getLastUsername(),
                "error" => $authenticationUtils->getLastAuthenticationError()
            ])
        );
    }

    /**
     * @Route("/logout", name = "security_logout")
     */
    public function logout() {
        // This is overtaken by Symfony framework. We have to define the route, Symfony takes care of the rest
    }

    /**
     * @Route("/confirm/{token}", name="security_confirm")
     */
    public function confirm(string $token, UserRepository $userRepository, EntityManagerInterface $entityManager) {
        $user = $userRepository->findOneBy([
            'confirmationToken' => $token
        ]);

        if ($user !== null) {
            $user->setEnabled(true);
            $user->setConfirmationToken('');
            $entityManager->flush();
        }

        return new Response(
            $this->twig->render(
            'security/confirmation.html.twig',
                [
                'user' => $user
                ]
            )
        );

    }
}