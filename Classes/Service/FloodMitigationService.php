<?php
namespace Ttree\Identicons\Service;

use Neos\Cache\Exception;
use Neos\Flow\Annotations as Flow;
use Neos\Cache\Frontend\VariableFrontend;
use Neos\Flow\Security\Exception\AccessDeniedException;
use Neos\Flow\Utility\Now;

/**
 * @Flow\Scope("singleton")
 */
class FloodMitigationService
{
    /**
     * @var VariableFrontend
     * @Flow\Inject
     */
    protected $cache;

    /**
     * @var SettingsService
     * @Flow\Inject
     */
    protected $settingsService;

    /**
     * @param string $ipAddress
     * @throws AccessDeniedException
     * @throws Exception
     */
    public function registerAccess($ipAddress)
    {
        $cacheKey = $this->generateCacheKey($ipAddress);
        if (false === $cacheValue = $this->cache->get($cacheKey)) {
            $cacheValue = 1;
        } else {
            $cacheValue++;
        }
        $this->cache->set($cacheKey, $cacheValue);

        if ($cacheValue > $this->settingsService->get('flood.limit')) {
            throw new AccessDeniedException('Your IP address is currently blocked by our rate limiting system', 1376922729);
        }
    }

    /**
     * @param string $clientIpAddress
     * @return string
     */
    protected function generateCacheKey($clientIpAddress)
    {
        $now = new Now();
        $currentMinute = $now->getTimestamp() - $now->getTimestamp() % 60;

        return md5($clientIpAddress) . '_' . $currentMinute;
    }
}
