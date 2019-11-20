<?php declare(strict_types=1);

namespace EryseClient\Common\Service;

use EryseClient\Client\Token\Entity\Token;
use EryseClient\Common\Utility\TranslatorTrait;
use EryseClient\Server\User\Entity\User;
use Swift_Mailer;
use Swift_Message;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
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

    /** @var ParameterBagInterface */
    private $parameterBag;

    /**
     * MailService constructor.
     * @param Swift_Mailer $mailer
     * @param Environment $renderer
     * @param ParameterBagInterface $parameterBag
     */
    public function __construct(Swift_Mailer $mailer, Environment $renderer, ParameterBagInterface $parameterBag)
    {
        $this->mailer = $mailer;
        $this->renderer = $renderer;
        $this->parameterBag = $parameterBag;
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
            $this->parameterBag->get("eryseClient.name") . ' - ' . $this->translator->trans(
                'email-activation-title',
                [],
                'profile'
            )
        ))->setFrom($this->parameterBag->get("eryseClient.emails.info"))
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
            $this->parameterBag->get("eryseClient.name") . ' - ' . $this->translator->trans(
                'email-request-reset-password-title',
                [],
                'profile'
            )
        ))->setFrom($this->parameterBag->get("eryseClient.emails.info"))
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
