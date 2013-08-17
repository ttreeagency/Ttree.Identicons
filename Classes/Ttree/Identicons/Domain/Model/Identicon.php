<?php
namespace Ttree\Identicons\Domain\Model;

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
use Doctrine\ORM\Mapping as ORM;

/**
 * Identicon Entity
 *
 * @Flow\Entity
 */
class Identicon {

	/**
	 * @var string
	 * @ORM\Id
	 */
	protected $token;

	/**
	 * @var \TYPO3\Media\Domain\Model\Image
	 * @ORM\OneToOne(cascade={"persist"})
	 */
	protected $image;

	/**
	 * @param \TYPO3\Media\Domain\Model\Image $image
	 * @param string $token
	 */
	function __construct(\TYPO3\Media\Domain\Model\Image $image, $token) {
		$this->image = $image;
		$this->token = $token;
	}

	/**
	 * @return string
	 */
	public function getToken() {
		return $this->token;
	}

	/**
	 * @return string
	 */
	public function render() {
		$content = '';
		if ($this->getImage() !== NULL && $this->getImage()->getResource() !== NULL) {
			$handle  = fopen($this->getImage()->getResource()->getUri(), 'rb');
			$content = stream_get_contents($handle);
			fclose($handle);
		}

		return $content;
	}

	/**
	 * @return \TYPO3\Media\Domain\Model\Image
	 */
	public function getImage() {
		return $this->image;
	}
}

?>