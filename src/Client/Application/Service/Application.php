<?php declare(strict_types=1);

namespace EryseClient\Client\Application\Service;

use EryseClient\Common\Service\AbstractService;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

/**
 * Class Application
 *
 * @package EryseClient\Client\Application\Service
 */
class Application extends AbstractService
{
    /** @var string */
    protected $name;

    /** @var string */
    protected $emailInfo;

    /** @var string */
    protected $emailAdmin;

    /** @var float */
    protected $membershipPrice;

    /**
     * Application constructor.
     *
     * @param ParameterBagInterface $parameterBag
     */
    public function __construct(ParameterBagInterface $parameterBag)
    {
        $this->name = $parameterBag->get("eryse")["client"]["name"];
        $this->emailAdmin = $parameterBag->get("eryse")["client"]["emails"]["admin"];
        $this->emailInfo = $parameterBag->get("eryse")["client"]["emails"]["info"];
        $this->membershipPrice = $parameterBag->get("eryse")["client"]["membership"]["price"];
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getEmailInfo(): string
    {
        return $this->emailInfo;
    }

    /**
     * @return string
     */
    public function getEmailAdmin(): string
    {
        return $this->emailAdmin;
    }

    /**
     * @return float
     */
    public function getMembershipPrice(): float
    {
        return $this->membershipPrice;
    }

}
