<?php
namespace Ttree\Identicons\Factory;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "Ttree.Identicons".      *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU General Public License, either version 3 of the   *
 * License, or (at your option) any later version.                        *
 *                                                                        *
 * The TYPO3 project - inspiring people to share!                         *
 *                                                                        */

use Ttree\Identicons\Domain\Model\Identicon;
use TYPO3\Flow\Annotations as Flow;
use Doctrine\ORM\Mapping as ORM;
use TYPO3\Media\Domain\Model\Image;

/**
 * Identicon Factory
 *
 * @Flow\Scope("singleton")
 */
class IdenticonFactory {

	/**
	 * @var \Ttree\Identicons\Generator\GeneratorInterface
	 * @Flow\Inject
	 */
	protected $identiconGenerator;

	/**
	 * @Flow\Inject
	 * @var \TYPO3\Flow\Resource\ResourceManager
	 */
	protected $resourceManager;

	/**
	 * @var \TYPO3\Media\Domain\Repository\ImageRepository
	 * @Flow\Inject
	 */
	protected $imageRepository;

	/**
	 * @var \Ttree\Identicons\Domain\Repository\IdenticonRepository
	 * @Flow\Inject
	 */
	protected $identiconRepository;

	/**
	 * @var \TYPO3\Flow\Persistence\PersistenceManagerInterface
	 * @Flow\Inject
	 */
	protected $persistenceManager;

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
	 * @param string $hash
	 * @return \Ttree\Identicons\Domain\Model\Identicon
	 */
	public function create($hash) {
		$identicon = $this->identiconRepository->findByIdentifier($hash);
		if ($identicon === NULL) {
			$identicon = new Identicon($this->createImageFromService($hash), $hash);
			if ($this->settings['persist'] === TRUE) {
				$this->identiconRepository->add($identicon);
			}
			$this->persistenceManager->persistAll();
		}

		return $identicon;
	}

	/**
	 * @param string $hash
	 * @return Image
	 */
	protected function createImageFromService($hash) {
		$hash = md5($hash);
		ob_start();
		imagepng($this->identiconGenerator->generate(md5($hash)), NULL, 9, PNG_ALL_FILTERS);
		$content = ob_get_contents();
		ob_clean();

		$resource = $this->resourceManager->createResourceFromContent($content, $hash . '.png');

		return new Image($resource);
	}
}

?>