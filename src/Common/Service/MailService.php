<?php

declare(strict_types=1);

namespace EryseClient\Common\Service;

use EryseClient\Server\Token\Entity\TokenEntity;
use EryseClient\Common\Utility\TranslatorAwareTrait;
use EryseClient\Server\User\Entity\UserEntity;
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
    protected MailerInterface $mailer;

    /** @var Response $renderer */
    protected $renderer;

    /** @var ParameterBagInterface */
    private ParameterBagInterface $parameterBag;

    /**
     * MailService constructor.
     *
     * @param MailerInterface $mailer
     * @param Environment $renderer
     * @param ParameterBagInterface $parameterBag
     */
    public function __construct(MailerInterface $mailer, Environment $renderer, ParameterBagInterface $parameterBag)
    {
        $this->mailer = $mailer;
        $this->renderer = $renderer;
        $this->parameterBag = $parameterBag;
    }

    /**
     * @param UserEntity $user
     * @param TokenEntity $token
     *
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws TransportExceptionInterface
     */
    public function sendActivationMail(UserEntity $user, TokenEntity $token): void
    {
        $message = (new Email())->from($this->parameterBag->get('eryseClient.emails.info'))
            ->html(
                $this->renderer->render(
                    '_email/user-activation.html.twig',
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
     * @param UserEntity $user
     * @param TokenEntity $token
     *
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws TransportExceptionInterface
     */
    public function sendRequestResetPasswordMail(UserEntity $user, TokenEntity $token) : void
    {
        $message = (new Email())->from($this->parameterBag->get('eryseClient.emails.info'))
            ->html(
                $this->renderer->render(
                    '_email/user-request-reset-password.twig',
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
