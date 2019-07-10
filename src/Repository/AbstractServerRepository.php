<?php declare(strict_types=1);


namespace EryseClient\Repository;

use Doctrine\Common\Persistence\ObjectRepository;
use Psr\Http\Message\ResponseInterface;
use RaitoCZ\EryseServices\Service\ApiClientInterface;
use UnexpectedValueException;

/**
 * Class AbstractServerRepository
 * @package eRyseClient\Repository
 */
abstract class AbstractServerRepository
{
    /** @var ApiClientInterface */
    protected $apiClient;

    public function __construct(ApiClientInterface $apiClient)
    {
        $this->apiClient = $apiClient;
    }

}