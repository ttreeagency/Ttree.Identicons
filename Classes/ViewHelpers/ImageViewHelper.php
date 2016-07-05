<?php
namespace Ttree\Identicons\ViewHelpers;

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
use TYPO3\Media\Domain\Model\ImageInterface;

/**
 * Render an identicon image
 *
 * @package Ttree\Identicons\ViewHelpers
 */
class ImageViewHelper extends \TYPO3\Fluid\Core\ViewHelper\AbstractTagBasedViewHelper
{
    /**
     * @var \Ttree\Identicons\Factory\IdenticonFactory
     * @Flow\Inject
     */
    protected $identiconFactory;

    /**
     * @var \TYPO3\Flow\Resource\Publishing\ResourcePublisher
     * @Flow\Inject
     */
    protected $resourcePublisher;

    /**
     * name of the tag to be created by this view helper
     *
     * @var string
     */
    protected $tagName = 'img';

    /**
     * @return void
     */
    public function initializeArguments()
    {
        parent::initializeArguments();
        $this->registerUniversalTagAttributes();
        $this->registerTagAttribute('alt', 'string', 'Specifies an alternate text for an image', true);
    }

    /**
     * @param string $hash
     * @param integer $size
     * @return string
     */
    public function render($hash, $size = null)
    {
        $identicon = $this->identiconFactory->create(new IdenticonConfiguration($hash, $size));

        $thumbnailImage = $this->getThumbnailImage($identicon->getImage(), $size);

        $this->tag->addAttributes(array(
            'width' => $thumbnailImage->getWidth(),
            'height' => $thumbnailImage->getHeight(),
            'src' => $this->resourcePublisher->getPersistentResourceWebUri($thumbnailImage->getResource()),
        ));

        return $this->tag->render();
    }

    /**
     * Calculates the dimensions of the thumbnail to be generated and returns the thumbnail image if the new dimensions
     * differ from the specified image dimensions, otherwise the original image is returned.
     *
     * @param \TYPO3\Media\Domain\Model\ImageInterface $image
     * @param integer $size
     * @return \TYPO3\Media\Domain\Model\ImageInterface
     *
     */
    protected function getThumbnailImage(ImageInterface $image, $size)
    {
        if ($size === null || $size > $image->getWidth()) {
            $maximumWidth = $image->getWidth();
            $maximumHeight = $image->getWidth();
        } else {
            $maximumWidth = $maximumHeight = $size;
        }
        if ($maximumWidth === $image->getWidth() && $maximumHeight === $image->getHeight()) {
            return $image;
        }
        return $image->getThumbnail($maximumWidth, $maximumHeight, ImageInterface::RATIOMODE_INSET);
    }
}
