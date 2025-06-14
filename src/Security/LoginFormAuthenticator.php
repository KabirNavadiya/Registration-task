<?php

namespace App\Security;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\AbstractLoginFormAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\CustomCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\PassportInterface;
use Symfony\Component\Security\Http\EntryPoint\AuthenticationEntryPointInterface;

class LoginFormAuthenticator extends AbstractLoginFormAuthenticator implements AuthenticationEntryPointInterface
{
    private UserRepository $userRepository;
    private RouterInterface $router;
    public function __construct(UserRepository $userRepository, RouterInterface $router)
    {
        $this->userRepository = $userRepository;
        $this->router = $router;
    }

//    public function supports(Request $request): ?bool
//    {
//        return $request->getPathInfo() === '/login' && $request->isMethod('POST');
//    }

    public function authenticate(Request $request): PassportInterface
    {

        $email = $request->request->get('email');
        $password = $request->request->get('password');
        return new Passport(
            new UserBadge($email,function($userIdentifier){
                $user = $this->userRepository->findOneBy(['email' => $userIdentifier]);

                if(!$user){
                    throw new UserNotFoundException();

                }
                return $user;

            }),
            new PasswordCredentials($password),
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        return new RedirectResponse(
            $this->router->generate('app_homepage')
        );
    }

//    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
//    {
//        $request->getSession()->set(Security::AUTHENTICATION_ERROR, $exception);
//
//        return new RedirectResponse(
//            $this->router->generate('app_login')
//        );
//    }

//    public function start(Request $request, AuthenticationException $authException = null): Response
//    {
//        return new RedirectResponse(
//            $this->router->generate('app_login')
//        );
//    }
    protected function getLoginUrl(Request $request): string
    {
        return $this->router->generate('app_login');
    }
}
