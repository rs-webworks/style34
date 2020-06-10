<?php declare(strict_types=1);

namespace EryseClient\Server\User\EventListener;

use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use EryseClient\Common\Service\MailService;
use EryseClient\Server\Token\Repository\TokenRepository;
use EryseClient\Server\Token\Service\TokenService;
use EryseClient\Server\User\Event\AfterRegistrationEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

/**
 * Class AfterRegistrationListener
 */
class SendRegistrationEmailSubscriber implements EventSubscriberInterface
{
    /**
     * @var MailService
     */
    private MailService $mailService;
    /**
     * @var TokenService
     */
    private TokenService $tokenService;
    /**
     * @var TokenRepository
     */
    private TokenRepository $tokenRepository;

    /**
     * AfterRegistrationSubscriber constructor.
     *
     * @param MailService $mailService
     * @param TokenService $tokenService
     * @param TokenRepository $tokenRepository
     */
    public function __construct(MailService $mailService, TokenService $tokenService, TokenRepository $tokenRepository)
    {
        $this->mailService = $mailService;
        $this->tokenService = $tokenService;
        $this->tokenRepository = $tokenRepository;
    }

    /**
     * @return array|void
     */
    public static function getSubscribedEvents()
    {
        return [
            AfterRegistrationEvent::class => 'sendRegistrationEmail'
        ];
    }

    /**
     * @param AfterRegistrationEvent $event
     *
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws TransportExceptionInterface
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    private function sendRegistrationEmail(AfterRegistrationEvent $event): void
    {
        $userEntity = $event->getUserEntity();
        $token = $this->tokenService->getActivationToken($userEntity);
        $this->mailService->sendActivationMail($userEntity, $token);
        $this->tokenRepository->save($token);
    }

}
