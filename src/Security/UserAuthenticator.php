<?php

namespace App\Security;

use App\Entity\User;
use App\Entity\Subscriptions;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Http\Util\TargetPathTrait;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Http\SecurityRequestAttributes;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\CsrfTokenBadge;
use Symfony\Component\Security\Http\Authenticator\AbstractLoginFormAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\RememberMeBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;

class UserAuthenticator extends AbstractLoginFormAuthenticator
{
    use TargetPathTrait;

    public const LOGIN_ROUTE = 'app_login';

    public function __construct(private UrlGeneratorInterface $urlGenerator, private EntityManagerInterface $entityManager)
    {
    }

    public function authenticate(Request $request): Passport
    {
        $email = $request->getPayload()->getString('email');
        $user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $email]);
        if ($user instanceof User) {
            $subscription = $this->entityManager->getRepository(Subscriptions::class)
                ->createQueryBuilder('s')
                ->where('s.idUser = :id')
                ->setParameter('id', $user->getId())
                ->orderBy('s.dateDebut', 'DESC')
                ->setMaxResults(1)
                ->getQuery()
                ->getOneOrNullResult()
            ;
            if ($subscription && $subscription->getDateFin() < new \DateTime()) {
                $roles = $user->getRoles();
                if (($key = array_search('ROLE_SUBSCRIBED', $roles)) !== false) {
                    unset($roles[$key]);
                    $user->setRoles($roles);
                    $roles = $user->getRoles();
                    $this->entityManager->persist($user);

                    $this->entityManager->flush();
                }
            }
        }

        $request->getSession()->set(SecurityRequestAttributes::LAST_USERNAME, $email);

        return new Passport(
            new UserBadge($email),
            new PasswordCredentials($request->getPayload()->getString('password')),
            [
                new CsrfTokenBadge('authenticate', $request->getPayload()->getString('_csrf_token')),
                new RememberMeBadge(),
            ]
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        $user = $token->getUser();
        if ($user instanceof User) {
            $subscription = $this->entityManager->getRepository(Subscriptions::class)
                ->createQueryBuilder('s')
                ->where('s.idUser = :id')
                ->setParameter('id', $user->getId())
                ->orderBy('s.dateFin', 'DESC')
                ->setMaxResults(1)
                ->getQuery()
                ->getOneOrNullResult()
            ;
            if ($subscription && $subscription->getDateFin() < new \DateTime()) {
                $roles = $user->getRoles();
                if (($key = array_search('ROLE_SUBSCRIBED', $roles)) !== false) {
                    unset($roles[$key]);
                    $user->setRoles($roles);
                    $roles = $user->getRoles();
                    $this->entityManager->persist($user);
                    $token->setUser($user);

                    $this->entityManager->flush();
                }
            }
        }

        if ($targetPath = $this->getTargetPath($request->getSession(), $firewallName)) {
            return new RedirectResponse($targetPath);
        }

        // For example:
        return new RedirectResponse($this->urlGenerator->generate('app_home'));
        // throw new \Exception('TODO: provide a valid redirect inside '.__FILE__);
    }

    protected function getLoginUrl(Request $request): string
    {
        return $this->urlGenerator->generate(self::LOGIN_ROUTE);
    }
}



