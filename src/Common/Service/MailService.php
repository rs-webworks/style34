<?php

declare(strict_types=1);

namespace EryseClient\Common\Service;

use EryseClient\Server\Token\Entity\Token;
use EryseClient\Common\Utility\TranslatorAwareTrait;
use EryseClient\Server\User\Entity\User;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

/**
 * Class MailService.
 */
class MailService extends AbstractService
{
    use TranslatorAwareTrait;

    /** @var MailerInterface $mailer */
    protected $mailer;

    /** @var Response $renderer */
    protected $renderer;

    /** @var ParameterBagInterface */
    private $parameterBag;

    /**
     * MailService constructor.
     */
    public function __construct(MailerInterface $mailer, Environment $renderer, ParameterBagInterface $parameterBag)
    {
        $this->mailer = $mailer;
        $this->renderer = $renderer;
        $this->parameterBag = $parameterBag;
    }

    /**
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws TransportExceptionInterface
     */
    public function sendActivationMail(User $user, Token $token): void
    {
        $message = (new Email())->from($this->parameterBag->get('eryseClient.emails.info'))
            ->html(
                $this->renderer->render(
                    '_emails/user-activation.html.twig',
                    [
                        'user' => $user,
                        'token' => $token,
                    ]
                )
            )
            ->to($user->getEmail())
            ->subject(
                $this->parameterBag->get('eryseClient.name') . ' - ' . $this->translator->trans(
                    'email-activation-title',
                    [],
                    'profile'
                )
            );

        $this->mailer->send($message);
    }

    /**
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws TransportExceptionInterface
     */
    public function sendRequestResetPasswordMail(User $user, Token $token)
    {
        $message = (new Email())->from($this->parameterBag->get('eryseClient.emails.info'))
            ->html(
                $this->renderer->render(
                    '_emails/user-request-reset-password.twig',
                    [
                        'user' => $user,
                        'token' => $token,
                    ]
                )
            )
            ->to($user->getEmail())
            ->subject(
                $this->parameterBag->get('eryseClient.name') . ' - ' . $this->translator->trans(
                    'email-request-reset-password-title',
                    [],
                    'profile'
                )
            );

        $this->mailer->send($message);
    }
}
