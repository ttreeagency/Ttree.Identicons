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
use Imagine\Image\Color;
use Imagine\Image\ImageInterface;
use Imagine\Image\Point;
use TYPO3\Flow\Annotations as Flow;
use TYPO3\Flow\Utility\Arrays;

/**
 * Abstract Generator
 *
 * @package Ttree\Identicons\Generator
 */
abstract class AbstractGenerator implements GeneratorInterface {

	/**
	 * @var array
	 */
	protected $settings;

	/**
	 * @var \Imagine\Imagick\Imagine
	 */
	protected $imagine;

	/**
	 * @var \TYPO3\Imagine\ImagineFactory
	 * @Flow\Inject
	 */
	protected $imagineFactory;

	/**
	 * @var \Imagine\Image\Color
	 */
	protected $backgroundColor;

	/**
	 * @param array $settings
	 */
	public function injectSettings(array $settings) {
		$this->settings = $settings;
	}

	/**
	 * Initialize Object
	 */
	public function initializeObject() {
		$this->imagine         = $this->imagineFactory->create();
		$this->backgroundColor = new \Imagine\Image\Color($this->settings['backgroundColor']);
	}

	/**
	 * @return int
	 */
	protected function getSize() {
		return Arrays::getValueByPath($this->settings, 'size') ? : 128;
	}

	/**
	 * @param int $width
	 * @param int $height
	 * @param \Imagine\Image\Color $backgroundColor
	 * @return \Imagine\Image\ImageInterface
	 */
	protected function createImage($width, $height, Color $backgroundColor = NULL) {
		return $this->imagine->create(new \Imagine\Image\Box($width, $height), $backgroundColor ? : $this->backgroundColor);
	}

	/**
	 * @param \Imagine\Image\ImageInterface $sprite
	 * @param null $rotation
	 * @return \Imagine\Image\ImageInterface
	 */
	protected function rotate(\Imagine\Image\ImageInterface $sprite, $rotation = NULL) {
		if ($rotation !== NULL) {
			$rotation = new Rotate($rotation, $this->backgroundColor);
			$sprite   = $rotation->apply($sprite);
		}

		return $sprite;
	}

	/**
	 * @param array $shape
	 * @param int $size
	 * @throws \Exception
	 * @return array
	 */
	protected function applyRatioToShapeCoordinates(array $shape, $size) {
		$count = count($shape);
		if ($count % 2 !== 0) {
			throw new \Exception('Invalid number of coordinate', 1376757994);
		}
		$coordinates = array();
		for ($i = 0; $i < $count / 2; $i++) {
			//$shape[$i] = $shape[$i] * $this->getSize();
			$coordinate    = array_slice($shape, $i * 2, 2);
			$coordinates[] = new Point($coordinate[0] * $size, $coordinate[1] * $size);
		}

		return $coordinates;
	}

	/**
	 * @param ImageInterface $image
	 * @param int $padding
	 * @return \Imagine\Image\ManipulatorInterface
	 */
	protected function pad(ImageInterface $image, $padding) {
		$size    = $image->getSize();
		$resized = $this->createImage($size->getWidth() + $padding, $size->getHeight() + $padding);

		return $resized->paste($image, new Point($padding / 2, $padding / 2))->resize(new Box($this->getSize(), $this->getSize()));
	}
}

?>