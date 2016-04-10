<?php
namespace Ttree\Identicons\Security\Aspect;

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
 * Class GrantAccessAspect
 *
 * @Flow\Aspect
 * @Flow\Scope("singleton")
 * @package Ttree\Identicons\Security\Aspect
 */
class AccessValidationAspect {

	/**
	 * @var \Ttree\Identicons\Security\AccessValidationInterface
	 * @Flow\Inject
	 */
	protected $accessValidation;

	/**
	 * @Flow\Before("setting(Ttree.Identicons.access.enable) && method(public Ttree\Identicons\Controller\IdenticonsController->generateAction())")
	 * @param \TYPO3\Flow\Aop\JoinPointInterface $joinPoint The current join point
	 * @return string
	 */
	public function floodMitigation(\TYPO3\Flow\Aop\JoinPointInterface $joinPoint) {
		$hash = $joinPoint->getMethodArgument('hash');
		$this->accessValidation->check($hash);
	}

}

?>