<?php declare(strict_types=1);
namespace EryseClient\Utility;

use RaitoCZ\EryseServices\Service\RsaService;

/**
 * Trait RsaServiceTrait
 * @package EryseClient\Utility
 */
trait RsaServiceTrait
{

    /** @var RsaService */
    protected $rsaService;

    /**
     * @required
     * @param RsaService $rsaService
     */
    public function setRsaService(RsaService $rsaService)
    {
        $this->rsaService = $rsaService;
    }
}