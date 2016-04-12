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

use Doctrine\ORM\Mapping as ORM;
use Ttree\Identicons\Domain\Model\Identicon;
use Ttree\Identicons\Domain\Model\IdenticonConfiguration;
use Ttree\Identicons\Domain\Repository\IdenticonRepository;
use Ttree\Identicons\Generator\GeneratorInterface;
use TYPO3\Flow\Annotations as Flow;
use TYPO3\Flow\Cache\Frontend\VariableFrontend;
use TYPO3\Flow\Persistence\PersistenceManagerInterface;
use TYPO3\Flow\Resource\ResourceManager;
use TYPO3\Media\Domain\Model\Image;
use TYPO3\Media\Domain\Repository\ImageRepository;

/**
 * Identicon Factory
 *
 * @Flow\Scope("singleton")
 */
class IdenticonFactory
{
    /**
     * @var GeneratorInterface
     * @Flow\Inject
     */
    protected $identiconGenerator;

    /**
     * @var ResourceManager
     * @Flow\Inject
     */
    protected $resourceManager;

    /**
     * @var ImageRepository
     * @Flow\Inject
     */
    protected $imageRepository;

    /**
     * @var IdenticonRepository
     * @Flow\Inject
     */
    protected $identiconRepository;

    /**
     * @var PersistenceManagerInterface
     * @Flow\Inject
     */
    protected $persistenceManager;

    /**
     * @var boolean
     * @Flow\InjectConfiguration(path="persist")
     */
    protected $persistenceEnabled;

    /**
     * @var VariableFrontend
     * @Flow\Inject
     */
    protected $cache;

    /**
     * @param IdenticonConfiguration $hash
     * @return string
     */
    public function create(IdenticonConfiguration $hash)
    {
        if ($this->cache->has((string)$hash)) {
            return $this->cache->get((string)$hash);
        }
        $identicon = $this->identiconRepository->findByIdentifier($hash);
        if ($identicon === null) {
            $identicon = new Identicon($this->createImageFromService($hash), $hash);
            if ($this->persistenceEnabled === true) {
                $this->identiconRepository->add($identicon);
                $this->persistenceManager->persistAll();
            }
        }

        $image = $identicon->render();
        $this->cache->set((string)$hash, $image);
        return $image;
    }

    /**
     * @param IdenticonConfiguration $hash
     * @return Image
     */
    protected function createImageFromService(IdenticonConfiguration $hash)
    {
        $image = $this->identiconGenerator->generate($hash);
        ob_start();
        $image->show('png', [
            'quality' => 9,
            'filter' => PNG_ALL_FILTERS
        ]);
        $content = ob_get_contents();
        ob_clean();

        $resource = $this->resourceManager->importResourceFromContent($content, $hash . '.png');

        return new Image($resource);
    }
}
