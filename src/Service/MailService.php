<?php declare(strict_types=1);

namespace EryseClient\Service;

use EryseClient\Entity\Client\Token\Token;
use EryseClient\Entity\Server\User\User;
use EryseClient\Kernel;
use EryseClient\Utility\TranslatorTrait;
use Swift_Mailer;
use Swift_Message;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

/**
 * Class MailService
 * @package EryseClient\Service
 */
class MailService extends AbstractService
{

    use TranslatorTrait;

    /** @var Swift_Mailer $mailer */
    protected $mailer;

    /** @var Response $renderer */
    protected $renderer;

    /**
     * MailService constructor.
     * @param Swift_Mailer $mailer
     * @param Environment $renderer
     */
    public function __construct(Swift_Mailer $mailer, Environment $renderer)
    {
        $this->mailer = $mailer;
        $this->renderer = $renderer;
    }

    /**
     * @param User $user
     * @param Token $token
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function sendActivationMail(User $user, Token $token): void
    {
        $message = (new Swift_Message(
            Kernel::SITE_NAME . ' - ' . $this->translator->trans('email-activation-title', [], 'profile')
        ))->setFrom(Kernel::INFO_MAIL)
            ->setTo($user->getEmail())
            ->setBody(
                $this->renderer->render(
                    '_emails/user-activation.html.twig',
                    [
                        'user' => $user,
                        'token' => $token
                    ]
                ),
                'text/html'
            );

        $this->mailer->send($message);
    }

    /**
     * @param User $user
     * @param Token $token
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function sendRequestResetPasswordMail(User $user, Token $token)
    {
        $message = (new Swift_Message(
            Kernel::SITE_NAME . ' - ' . $this->translator->trans('email-request-reset-password-title', [], 'profile')
        ))->setFrom(Kernel::INFO_MAIL)
            ->setTo($user->getEmail())
            ->setBody(
                $this->renderer->render(
                    '_emails/user-request-reset-password.twig',
                    [
                        'user' => $user,
                        'token' => $token
                    ]
                ),
                'text/html'
            );

        $this->mailer->send($message);
    }
}
