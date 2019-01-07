<?php

namespace EryseClient\Controller\Api;


use EryseClient\Service\CacheService;
use EryseClient\Traits\LoggerTrait;
use Psr\Cache\CacheItemPoolInterface;
use Psr\SimpleCache\CacheInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class ResponseController
 * @package EryseClient\Controller\Api
 */
class ResponseController extends AbstractController
{
    use LoggerTrait;

    const PACKAGE_STRUCTURES = 'raitocz/eryse-structures';
    const PACKAGE_SERVICES = 'raitocz/eryse-services';
    const CACHEKEY_PACKAGES = 'controller.api.response.ping-packages';

    /**
     * @Route("/api/ping", name="api-ping")
     * @return JsonResponse
     */
    public function ping()
    {
        $data = array(
            'status' => 'OK'
        );

        return new JsonResponse($data);
    }

    /**
     * @Route("/api/packagesVersions", name="api-packages-versions")
     * @param KernelInterface $kernel
     * @param CacheService $cacheService
     * @return JsonResponse
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function packagesVersions(KernelInterface $kernel, CacheService $cacheService){
        $versions = $cacheService->callCached(self::CACHEKEY_PACKAGES, function() use($kernel){
            $packages = json_decode(file_get_contents($kernel->getProjectDir() . '/vendor/composer/installed.json'));

            foreach($packages as $package){
                if($package->name == self::PACKAGE_SERVICES || $package->name == self::PACKAGE_STRUCTURES){
                    $versions[$package->name] = array(
                        'version' => $package->version,
                        'time' => $package->time
                    );
                }
            }

            return $versions;
        }, CacheService::EXPIRES_AFTER_DAY);

        $data = array(
            'packages' => $versions
        );

        return new JsonResponse($data);
    }

}