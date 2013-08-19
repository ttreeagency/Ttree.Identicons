<?php
namespace Ttree\Identicons\Service;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "Ttree.Identicons".      *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU General Public License, either version 3 of the   *
 * License, or (at your option) any later version.                        *
 *                                                                        *
 * The TYPO3 project - inspiring people to share!                         *
 *                                                                        */

use TYPO3\Flow\Annotations as Flow;
use TYPO3\Flow\Mvc\ActionRequest;
use TYPO3\Flow\Utility\Now;

/**
 * @Flow\Scope("singleton")
 */
class FloodMitigationService {

	/**
	 * @var \TYPO3\Flow\Cache\Frontend\VariableFrontend
	 * @Flow\Inject
	 */
	protected $cache;

	/**
	 * @var \Ttree\Identicons\Service\SettingsService
	 * @Flow\Inject
	 */
	protected $settingsService;

	/**
	 * @param ActionRequest $request
	 * @throws \TYPO3\Flow\Security\Exception\AccessDeniedException
	 */
	public function registerRequest(ActionRequest $request) {
		$cacheKey = $this->generateCacheKey($request->getHttpRequest()->getClientIpAddress());
		if (FALSE === $cacheValue = $this->cache->get($cacheKey)) {
			$cacheValue = 1;
		} else {
			$cacheValue++;
		}
		$this->cache->set($cacheKey, $cacheValue);

		if ($cacheValue > $this->settingsService->get('flood.limit')) {
			throw new \TYPO3\Flow\Security\Exception\AccessDeniedException('Rate Limiting Protection', 1376922729);
		}
	}

	/**
	 * @param string $clientIpAddress
	 * @return bool
	 */
	public function validateAccessByClientIpAddress($clientIpAddress) {
		$cacheKey = $this->generateCacheKey($clientIpAddress);
		if (FALSE === $cacheValue = $this->cache->get($cacheKey)) {
			$access = TRUE;
		} else {
			$access = $cacheValue <= $this->settingsService->get('flood.limit');
		}
		return $access;
	}

	/**
	 * @param string $clientIpAddress
	 * @return string
	 */
	protected function generateCacheKey($clientIpAddress) {
		$now = new Now();
		$currentMinute = $now->getTimestamp() - $now->getTimestamp() % 60;

		return md5($clientIpAddress) . '_' . $currentMinute;
	}
}
?>