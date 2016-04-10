<?php
namespace Ttree\Identicons\Controller;

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
 * Identicon Controller
 *
 * @package Ttree\Identicons\Controller
 */
class IdenticonsController extends \TYPO3\Flow\Mvc\Controller\ActionController {

	/**
	 * @var \Ttree\Identicons\Factory\IdenticonFactory
	 * @Flow\Inject
	 */
	protected $identiconFactory;

	/**
	 * @var \Ttree\Identicons\Service\SettingsService
	 * @Flow\Inject
	 */
	protected $settingsService;

	/**
	 * @param string $hash
	 * @return string
	 */
	public function generateAction($hash) {
		$ttl = $this->settingsService->getCacheControlMaxAge();

		$this->response->setHeader('Content-Type', 'image/png');
		$this->response->setHeader('Cache-Control', sprintf('max-age=%i, public', $ttl));
		$this->response->setHeader('Expires', date(DATE_RFC1123,time()+$ttl));

		$identicon = $this->identiconFactory->create($hash);

		return $identicon->render();
	}

}

?>