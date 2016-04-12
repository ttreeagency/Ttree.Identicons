<?php
namespace Ttree\Identicons\Generator;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "Ttree.Identicons".      *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU General Public License, either version 3 of the   *
 * License, or (at your option) any later version.                        *
 *                                                                        *
 * The TYPO3 project - inspiring people to share!                         *
 *                                                                        */

use Imagine\Filter\Basic\Rotate;
use Imagine\Image\Box;
use Imagine\Image\ImageInterface;
use Imagine\Image\ManipulatorInterface;
use Imagine\Image\Palette;
use Imagine\Image\Palette\Color;
use Imagine\Image\Palette\ColorParser;
use Imagine\Image\Point;
use Imagine\Imagick\Imagine;
use Ttree\Identicons\Domain\Model\IdenticonConfiguration;
use Ttree\Identicons\Service\SettingsService;
use TYPO3\Flow\Annotations as Flow;
use TYPO3\Flow\Exception;
use TYPO3\Flow\Utility\Arrays;
use TYPO3\Imagine\ImagineFactory;

/**
 * Abstract Generator
 *
 * @package Ttree\Identicons\Generator
 */
abstract class AbstractGenerator implements GeneratorInterface
{
    /**
     * @var SettingsService
     * @Flow\Inject
     */
    protected $settingsService;

    /**
     * @var array
     * @Flow\InjectConfiguration(path="generator")
     */
    protected $configuration;

    /**
     * @var Imagine
     */
    protected $imagine;

    /**
     * @var ImagineFactory
     * @Flow\Inject
     */
    protected $imagineFactory;

    /**
     * @var Color\RGB
     */
    protected $backgroundColor;

    /**
     * @var integer
     */
    protected $defaultSize;

    /**
     * @var array
     * @Flow\InjectConfiguration(path="size")
     */
    protected $sizeContraints;

    /**
     * {@inheritdoc}
     */
    public function generate(IdenticonConfiguration $hash)
    {
        $this->defaultSize = $hash->getSize() ?: $this->settingsService->getDefaultIconSize();
        list($minimumSize, $maximumSize) = array_values($this->sizeContraints);
        if ($this->defaultSize < $minimumSize || $this->defaultSize > $maximumSize) {
            throw new Exception(sprintf('Icon size is limited between %d and %d', $minimumSize, $maximumSize), 1460367015);
        }
    }

    /**
     * Initialize Object
     */
    public function initializeObject()
    {
        $this->imagine = $this->imagineFactory->create();
        $parser = new ColorParser();
        $backgroundConfiguration = $this->settingsService->get('background');
        $color = $parser->parseToRGB($backgroundConfiguration['color']);
        $palette = new Palette\RGB();
        $this->backgroundColor = new Color\RGB($palette, $color, $backgroundConfiguration['opacity']);
    }

    /**
     * @param int $width
     * @param int $height
     * @param Color\ColorInterface $backgroundColor
     * @return ImageInterface
     */
    protected function createImage($width, $height, Color\ColorInterface $backgroundColor = null)
    {
        return $this->imagine->create(new Box($width, $height), $backgroundColor ?: $this->backgroundColor);
    }

    /**
     * @param ImageInterface $sprite
     * @param integer $rotation
     * @return ImageInterface
     */
    protected function rotate(ImageInterface $sprite, $rotation = null)
    {
        if ($rotation !== null) {
            $rotation = new Rotate($rotation, $this->backgroundColor);
            $sprite = $rotation->apply($sprite);
        }

        return $sprite;
    }

    /**
     * @param array $shape
     * @param integer $size
     * @throws \Exception
     * @return array
     */
    protected function applyRatioToShapeCoordinates(array $shape, $size)
    {
        $count = count($shape);
        if ($count % 2 !== 0) {
            throw new \Exception('Invalid number of coordinate', 1376757994);
        }
        $coordinates = [];
        for ($i = 0; $i < $count / 2; $i++) {
            $coordinate = array_slice($shape, $i * 2, 2);
            $coordinates[] = new Point($coordinate[0] * $size, $coordinate[1] * $size);
        }

        return $coordinates;
    }

    /**
     * @param ImageInterface $image
     * @param integer $padding
     * @return ManipulatorInterface
     */
    protected function pad(ImageInterface $image, $padding)
    {
        $size = $image->getSize();
        $resized = $this->createImage($size->getWidth() + $padding, $size->getHeight() + $padding);

        return $resized->paste($image, new Point($padding / 2, $padding / 2))->resize(new Box($this->defaultSize, $this->defaultSize));
    }

    /**
     * @param array $shapes
     * @param integer $shape
     * @param array $defaultShape
     * @return array
     */
    protected function selectShape(array $shapes, $shape, array $defaultShape)
    {
        if (isset($shapes[$shape])) {
            $shape = $shapes[$shape];
        } else {
            $shape = $defaultShape;
        }
        return $shape;
    }

    /**
     * @param string $path
     * @return mixed
     */
    protected function getConfigurationPath($path)
    {
        $generatorConfiguration = Arrays::getValueByPath($this->configuration, get_called_class());
        return Arrays::getValueByPath($generatorConfiguration, $path);
    }

}
