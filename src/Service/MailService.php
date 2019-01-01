<?php

namespace EryseClient\Service;

use EryseClient\Entity\User\User;
use EryseClient\Entity\Token\Token;
use EryseClient\Kernel;
use EryseClient\Traits\TranslatorTrait;
use Swift_Message;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;

/**
 * Class MailService
 * @package EryseClient\Service
 */
class MailService extends AbstractService
{

    use TranslatorTrait;

    /** @var \Swift_Mailer $mailer */
    protected $mailer;

    /** @var Response $renderer */
    protected $renderer;

    /**
     * MailService constructor.
     * @param \Swift_Mailer $mailer
     * @param Environment $renderer
     */
    public function __construct(\Swift_Mailer $mailer, Environment $renderer)
    {
        $this->mailer = $mailer;
        $this->renderer = $renderer;
    }

    /**
     * @param User $user
     * @param Token $token
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function sendActivationMail(User $user, Token $token): void
    {
        $message = (new Swift_Message(Kernel::SITE_NAME . ' - '. $this->translator->trans('email-activation-title', [], 'profile')))
            ->setFrom(Kernel::INFO_MAIL)
            ->setTo($user->getEmail())
            ->setBody(
                $this->renderer->render(
                    'emails/user-activation.html.twig',
                    array(
                        'user' => $user,
                        'token' => $token
                    )
                ),
                'text/html'
            )
        ;

        $this->mailer->send($message);
    }

    /**
     * @param User $user
     * @param Token $token
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function sendRequestResetPasswordMail(User $user, Token $token){
        $message = (new Swift_Message(Kernel::SITE_NAME . ' - '. $this->translator->trans('email-request-reset-password-title', [], 'profile')))
            ->setFrom(Kernel::INFO_MAIL)
            ->setTo($user->getEmail())
            ->setBody(
                $this->renderer->render(
                    'emails/user-request-reset-password.twig',
                    array(
                        'user' => $user,
                        'token' => $token
                    )
                ),
                'text/html'
            )
        ;

        $this->mailer->send($message);
    }
}