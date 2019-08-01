<?php declare(strict_types=1);
namespace EryseClient\Utility;

use RaitoCZ\EryseServices\Service\ApiClientInterface;

/**
 * Trait ApiClientTrait
 * @package EryseClient\Utility
 */
trait ApiClientTrait
{

    /** @var ApiClientInterface */
    protected $apiClient;

    /**
     * @required
     * @param ApiClientInterface $apiClient
     */
    public function setRsaService(ApiClientInterface $apiClient)
    {
        $this->apiClient = $apiClient;
    }
}