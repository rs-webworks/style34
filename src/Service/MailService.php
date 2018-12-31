<?php

namespace eRyseClient\Service;

use eRyseClient\Entity\Profile\Profile;
use eRyseClient\Entity\Token\Token;
use eRyseClient\Kernel;
use eRyseClient\Traits\TranslatorTrait;
use Swift_Message;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;

/**
 * Class MailService
 * @package eRyseClient\Service
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
     * @param TranslatorInterface $translator
     * @param Response $renderer
     */
    public function __construct(\Swift_Mailer $mailer, Environment $renderer)
    {
        $this->mailer = $mailer;
        $this->renderer = $renderer;
    }

    /**
     * @param Profile $profile
     * @param Token $token
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function sendActivationMail(Profile $profile, Token $token): void
    {
        $message = (new Swift_Message(Kernel::SITE_NAME . ' - '. $this->translator->trans('email-activation-title', [], 'profile')))
            ->setFrom(Kernel::INFO_MAIL)
            ->setTo($profile->getEmail())
            ->setBody(
                $this->renderer->render(
                    'emails/profile-activation.html.twig',
                    array(
                        'profile' => $profile,
                        'token' => $token
                    )
                ),
                'text/html'
            )
        ;

        $this->mailer->send($message);
    }

    /**
     * @param Profile $profile
     * @param Token $token
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function sendRequestResetPasswordMail(Profile $profile, Token $token){
        $message = (new Swift_Message(Kernel::SITE_NAME . ' - '. $this->translator->trans('email-request-reset-password-title', [], 'profile')))
            ->setFrom(Kernel::INFO_MAIL)
            ->setTo($profile->getEmail())
            ->setBody(
                $this->renderer->render(
                    'emails/profile-request-reset-password.twig',
                    array(
                        'profile' => $profile,
                        'token' => $token
                    )
                ),
                'text/html'
            )
        ;

        $this->mailer->send($message);
    }
}