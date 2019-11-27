<?php declare(strict_types=1);

namespace EryseClient\Common\DataCollector;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\DataCollector\DataCollector;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Class UserDataCollector
 *
 * @package EryseClient\Server\User\DataCollector
 */
class EryseDataCollector extends DataCollector
{
    private const COLLECTOR_NAME = "eryse.app";

    /**
     * @var UserInterface
     */
    private $user;

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
     * @param \Throwable|null $exception
     */
    public function collect(Request $request, Response $response, \Throwable $exception = null)
    {
        $this->data = [
            "user" => $this->user,
        ];
    }

    /**
     * @return string
     */
    public function getName()
    {
        return self::COLLECTOR_NAME;
    }

    /**
     *
     */
    public function reset()
    {
        $this->data = [];
    }

    /**
     * @return UserInterface|null
     */
    public function getUser(): ?UserInterface
    {
        return $this->data["user"];
    }

}