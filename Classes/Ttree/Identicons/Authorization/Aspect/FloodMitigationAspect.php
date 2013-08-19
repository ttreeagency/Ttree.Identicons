<?php
namespace Ttree\Identicons\Authorization\Aspect;

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
use TYPO3\Flow\Reflection\ObjectAccess;

/**
 * Flood Mitigation Aspect
 *
 * @Flow\Aspect
 * @Flow\Scope("singleton")
 */
class FloodMitigationAspect {

	/**
	 * @var \Ttree\Identicons\Service\FloodMitigationService
	 * @Flow\Inject
	 */
	protected $floodMitigationService;

	/**
	 * @Flow\Before("setting(Ttree.Identicons.flood.enable) && method(public Ttree\Identicons\Controller\IdenticonsController->generateAction())")
	 * @param \TYPO3\Flow\Aop\JoinPointInterface $joinPoint The current join point
	 * @return string
	 */
	public function floodMitigation(\TYPO3\Flow\Aop\JoinPointInterface $joinPoint) {
		$proxy = $joinPoint->getProxy();
		$request = ObjectAccess::getProperty($proxy, 'request', TRUE);
		$this->floodMitigationService->registerRequest($request);
	}

}

?>