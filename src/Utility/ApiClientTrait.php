<?php

namespace EryseClient\Utility;

use RaitoCZ\EryseServices\Service\ApiClient;

/**
 * Trait ApiClientTrait
 * @package EryseClient\Utility
 */
trait ApiClientTrait
{

    /** @var ApiClient */
    protected $apiClient;

    /**
     * @required
     * @param ApiClient $apiClient
     */
    public function setRsaService(ApiClient $apiClient)
    {
        $this->apiClient = $apiClient;
    }
}