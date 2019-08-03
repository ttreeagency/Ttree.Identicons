<?php
namespace Ttree\Identicons\ViewHelpers;

use Neos\Flow\ResourceManagement\ResourceManager;
use Neos\FluidAdaptor\Core\ViewHelper\AbstractTagBasedViewHelper;
use Ttree\Identicons\Domain\Model\IdenticonConfiguration;
use Neos\Flow\Annotations as Flow;
use Neos\Media\Domain\Model\ImageInterface;
use Ttree\Identicons\Factory\IdenticonFactory;

/**
 * Render an identicon image
 *
 * @package Ttree\Identicons\ViewHelpers
 */
class ImageViewHelper extends AbstractTagBasedViewHelper
{
    /**
     * @var IdenticonFactory
     * @Flow\Inject
     */
    protected $identiconFactory;

    /**
     * @Flow\Inject
     * @var ResourceManager
     */
    protected $resourceManager;

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

        $this->tag->addAttributes(array(
            'width' => $size,
            'height' => $size,
            'src' => 'data:image/png;base64,' . base64_encode($identicon),
        ));

        return $this->tag->render();
    }

    /**
     * Calculates the dimensions of the thumbnail to be generated and returns the thumbnail image if the new dimensions
     * differ from the specified image dimensions, otherwise the original image is returned.
     *
     * @param \Neos\Media\Domain\Model\ImageInterface $image
     * @param integer $size
     * @return \Neos\Media\Domain\Model\ImageInterface
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
