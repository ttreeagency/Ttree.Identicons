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
use TYPO3\Flow\Utility\Arrays;

/**
 * @Flow\Scope("singleton")
 */
class SettingsService {

	/**
	 * @var array
	 */
	protected $settings;

	/**
	 * @param array $settings
	 */
	public function injectSettings(array $settings) {
		$this->settings = $settings;
	}

	/**
	 * @param string $path
	 * @return string|array|boolean
	 */
	public function get($path) {
		return Arrays::getValueByPath($this->settings, $path);
	}

	/**
	 * @return integer
	 */
	public function getCacheControlMaxAge() {
		return (int)$this->get('ttl') ?: 2592000;
	}

}
?>