<?php
namespace Ttree\Identicons\Authorization\Interceptor;

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

/**
 * Flood Mitigation
 *
 * @Flow\Scope("singleton")
 */
class FloodMitigation implements \TYPO3\Flow\Security\Authorization\InterceptorInterface {

	/**
	 * @var \Ttree\Identicons\Service\FloodMitigationService
	 * @Flow\Inject
	 */
	protected $floodMitigationService;

	/**
	 * @var \TYPO3\Flow\Core\Bootstrap
	 * @Flow\Inject
	 */
	protected $bootstrap;

	/**
	 * Invokes nothing, always throws an AccessDenied Exception.
	 *
	 * @return boolean Always returns FALSE
	 * @throws \TYPO3\Flow\Security\Exception\AccessDeniedException
	 */
	public function invoke() {
		/** @var \TYPO3\Flow\Http\Request $requestHandler */
		$requestHandler = $this->bootstrap->getActiveRequestHandler()->getHttpRequest();
		if (!$this->floodMitigationService->validateAccessByClientIpAddress($requestHandler->getClientIpAddress())) {
			throw new \TYPO3\Flow\Security\Exception\AccessDeniedException('Your IP address is currently blocked by our rate limiting system', 1376924106);
		}

		return TRUE;
	}

}

?>