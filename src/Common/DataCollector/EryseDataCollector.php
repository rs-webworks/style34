<?php declare(strict_types=1);

namespace EryseClient\Common\DataCollector;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\DataCollector\DataCollector;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;
use Throwable;

/**
 * Class UserDataCollector
 *
 *
 */
class EryseDataCollector extends DataCollector
{
    private const COLLECTOR_NAME = 'eryse.app';

    /**
     * @var UserInterface
     */
    private ?UserInterface $user;

    /**
     * UserDataCollector constructor.
     *
     * @param Security $security
     */
    public function __construct(Security $security)
    {
        $this->user = $security->getUser();
    }


    /**
     * @param Request $request
     * @param Response $response
     * @param Throwable|null $exception
     */
    public function collect(Request $request, Response $response, Throwable $exception = null): void
    {
        $this->data = [
            'user' => $this->user,
        ];
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return self::COLLECTOR_NAME;
    }

    /**
     *
     */
    public function reset(): void
    {
        $this->data = [];
    }

    /**
     * @return UserInterface|null
     */
    public function getUser(): ?UserInterface
    {
        return $this->data['user'];
    }

}
