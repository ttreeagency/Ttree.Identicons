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

use Imagine\Image\Box;
use Imagine\Image\Color;
use Imagine\Image\Point;
use TYPO3\Flow\Annotations as Flow;

/**
 * Original Don Park identicon generator
 *
 * @package Ttree\Identicons\Generator
 * @Flow\Scope("singleton")
 */
class DonParkGenerator extends AbstractGenerator {

	/**
	 * @param string $hash
	 * @param int $size
	 * @return resource
	 */
	public function generate($hash, $size = NULL) {
		$size = $size ? : $this->settingsService->getDefaultIconSize();

		$cornerSpriteShape = hexdec(substr($hash, 0, 1));
		$sideSpriteShape   = hexdec(substr($hash, 1, 1));
		$centerSpriteShape = hexdec(substr($hash, 2, 1)) & 7;

		$cornerSpriteRotation   = hexdec(substr($hash, 3, 1)) & 3;
		$sideSpriteRotation     = hexdec(substr($hash, 4, 1)) & 3;

		$cornerSpriteForegroundColor = new Color(array(hexdec(substr($hash, 6, 2)), hexdec(substr($hash, 8, 2)), hexdec(substr($hash, 10, 2))));
		$sideSpriteForegroundColor = new Color(array(hexdec(substr($hash, 12, 2)), hexdec(substr($hash, 14, 2)), hexdec(substr($hash, 16, 2))));
		$centerSpriteForegroundColor = new Color(array(hexdec(substr($hash, 18, 2)), hexdec(substr($hash, 20, 2)), hexdec(substr($hash, 22, 2))));
		$centerSpriteBackgroundColor = new Color(array(hexdec(substr($hash, 24, 2)), hexdec(substr($hash, 26, 2)), hexdec(substr($hash, 28, 2))));

		$backgroundColor = new Color(array(255, 255, 255));
		$identicon       = $this->createImage($size * 3, $size * 3, $backgroundColor);

		$corner = $this->getSprite($cornerSpriteShape, $cornerSpriteForegroundColor, $cornerSpriteRotation);
		$identicon->paste($corner, new Point(0, 0));
		$identicon->paste($this->rotate($corner, 90), new Point(0, $size * 2));
		$identicon->paste($this->rotate($corner, 90), new Point($size * 2, $size * 2));
		$identicon->paste($this->rotate($corner, 90), new Point($size * 2, 0));

		$side = $this->getSprite($sideSpriteShape, $sideSpriteForegroundColor, $sideSpriteRotation);
		$identicon->paste($side, new Point($size, 0));
		$identicon->paste($this->rotate($side, 90), new Point(0, $size));
		$identicon->paste($this->rotate($side, 90), new Point($size, $size * 2));
		$identicon->paste($this->rotate($side, 90), new Point($size * 2, $size));

		$center = $this->getCenter($centerSpriteShape, $centerSpriteForegroundColor, $centerSpriteBackgroundColor);
		$identicon->paste($center, new Point($size, $size));

		/* create blank image according to specified dimensions */
		$identicon = $identicon->resize(new Box($size, $size));

		return $this->pad($identicon, $this->settingsService->getDefaultIconSize() / 4);
	}

	/**
	 * @param $shape
	 * @param Color $foregroundColor
	 * @param $rotation
	 * @return \Imagine\Image\ImageInterface
	 */
	protected function getSprite($shape, Color $foregroundColor, $rotation) {
		$sprite = $this->createImage($this->settingsService->getDefaultIconSize(), $this->settingsService->getDefaultIconSize(), $this->backgroundColor);

		switch ($shape) {
			case 0: // triangle
				$shape = array(
					0.5, 1,
					1, 0,
					1, 1
				);
				break;
			case 1: // parallelogram
				$shape = array(
					0.5, 0,
					1, 0,
					0.5, 1,
					0, 1
				);
				break;
			case 2: // mouse ears
				$shape = array(
					0.5, 0,
					1, 0,
					1, 1,
					0.5, 1,
					1, 0.5
				);
				break;
			case 3: // ribbon
				$shape = array(
					0, 0.5,
					0.5, 0,
					1, 0.5,
					0.5, 1,
					0.5, 0.5
				);
				break;
			case 4: // sails
				$shape = array(
					0, 0.5,
					1, 0,
					1, 1,
					0, 1,
					1, 0.5
				);
				break;
			case 5: // fins
				$shape = array(
					1, 0,
					1, 1,
					0.5, 1,
					1, 0.5,
					0.5, 0.5
				);
				break;
			case 6: // beak
				$shape = array(
					0, 0,
					1, 0,
					1, 0.5,
					0, 0,
					0.5, 1,
					0, 1
				);
				break;
			case 7: // chevron
				$shape = array(
					0, 0,
					0.5, 0,
					1, 0.5,
					0.5, 1,
					0, 1,
					0.5, 0.5
				);
				break;
			case 8: // fish
				$shape = array(
					0.5, 0,
					0.5, 0.5,
					1, 0.5,
					1, 1,
					0.5, 1,
					0.5, 0.5,
					0, 0.5
				);
				break;
			case 9: // kite
				$shape = array(
					0, 0,
					1, 0,
					0.5, 0.5,
					1, 0.5,
					0.5, 1,
					0.5, 0.5,
					0, 1
				);
				break;
			case 10: // trough
				$shape = array(
					0, 0.5,
					0.5, 1,
					1, 0.5,
					0.5, 0,
					1, 0,
					1, 1,
					0, 1
				);
				break;
			case 11: // rays
				$shape = array(
					0.5, 0,
					1, 0,
					1, 1,
					0.5, 1,
					1, 0.75,
					0.5, 0.5,
					1, 0.25
				);
				break;
			case 12: // double rhombus
				$shape = array(
					0, 0.5,
					0.5, 0,
					0.5, 0.5,
					1, 0,
					1, 0.5,
					0.5, 1,
					0.5, 0.5,
					0, 1
				);
				break;
			case 13: // crown
				$shape = array(
					0, 0,
					1, 0,
					1, 1,
					0, 1,
					1, 0.5,
					0.5, 0.25,
					0.5, 0.75,
					0, 0.5,
					0.5, 0.25
				);
				break;
			case 14: // radioactive
				$shape = array(
					0, 0.5,
					0.5, 0.5,
					0.5, 0,
					1, 0,
					0.5, 0.5,
					1, 0.5,
					0.5, 1,
					0.5, 0.5,
					0, 1
				);
				break;
			default: // tiles
				$shape = array(
					0, 0,
					1, 0,
					0.5, 0.5,
					0.5, 0,
					0, 0.5,
					1, 0.5,
					0.5, 1,
					0.5, 0.5,
					0, 1
				);
				break;
		}
		$sprite->draw()->polygon($this->applyRatioToShapeCoordinates($shape, $this->settingsService->getDefaultIconSize()), $foregroundColor, TRUE);

		/* rotate the sprite */
		for ($i = 0; $i < $rotation; $i++) {
			$sprite = $this->rotate($sprite, 90);
		}

		return $sprite;
	}

	/**
	 * @param $shape
	 * @param Color $foregroundColor
	 * @param Color $backgroundColor
	 * @return \Imagine\Image\ImageInterface
	 */
	protected function getCenter($shape, Color $foregroundColor, Color $backgroundColor = NULL) {
		/* make sure there's enough contrast before we use background color of side sprite */
		if ($backgroundColor !== NULL) {
			$backgroundColor = $this->generateBackgroundColorBasedOnContrast($foregroundColor, $backgroundColor);
		} else {
			$backgroundColor = $this->backgroundColor;
		}

		$sprite = $this->createImage($this->settingsService->getDefaultIconSize(), $this->settingsService->getDefaultIconSize(), $backgroundColor);
		switch ($shape) {
			case 0: // empty
				$shape = array();
				break;
			case 1: // fill
				$shape = array(
					0, 0,
					1, 0,
					1, 1,
					0, 1
				);
				break;
			case 2: // diamond
				$shape = array(
					0.5, 0,
					1, 0.5,
					0.5, 1,
					0, 0.5
				);
				break;
			case 3: // reverse diamond
				$shape = array(
					0, 0,
					1, 0,
					1, 1,
					0, 1,
					0, 0.5,
					0.5, 1,
					1, 0.5,
					0.5, 0,
					0, 0.5
				);
				break;
			case 4: // cross
				$shape = array(
					0.25, 0,
					0.75, 0,
					0.5, 0.5,
					1, 0.25,
					1, 0.75,
					0.5, 0.5,
					0.75, 1,
					0.25, 1,
					0.5, 0.5,
					0, 0.75,
					0, 0.25,
					0.5, 0.5
				);
				break;
			case 5: // morning star
				$shape = array(
					0, 0,
					0.5, 0.25,
					1, 0,
					0.75, 0.5,
					1, 1,
					0.5, 0.75,
					0, 1,
					0.25, 0.5
				);
				break;
			case 6: // small square
				$shape = array(
					0.33, 0.33,
					0.67, 0.33,
					0.67, 0.67,
					0.33, 0.67
				);
				break;
			case 7: // checkerboard
				$shape = array(
					0, 0,
					0.33, 0,
					0.33, 0.33,
					0.66, 0.33,
					0.67, 0,
					1, 0,
					1, 0.33,
					0.67, 0.33,
					0.67, 0.67,
					1, 0.67,
					1, 1,
					0.67, 1,
					0.67, 0.67,
					0.33, 0.67,
					0.33, 1,
					0, 1,
					0, 0.67,
					0.33, 0.67,
					0.33, 0.33,
					0, 0.33
				);
				break;
		}
		for ($i = 0; $i < count($shape); $i++) {
			$shape[$i] = $shape[$i] * $this->settingsService->getDefaultIconSize();
		}
		if (count($shape) > 0) {
			$sprite->draw()->polygon($this->applyRatioToShapeCoordinates($shape, $this->settingsService->getDefaultIconSize()), $foregroundColor, TRUE);
		}

		return $sprite;
	}

	/**
	 * @param Color $foregroundColor
	 * @param Color $backgroundColor
	 * @return Color
	 */
	protected function generateBackgroundColorBasedOnContrast(Color $foregroundColor, Color $backgroundColor) {
		if (!(abs($foregroundColor->getRed() - $backgroundColor->getRed()) > 127 || abs($foregroundColor->getGreen() - $backgroundColor->getGreen()) > 127 || abs($foregroundColor->getBlue() - $backgroundColor->getBlue()) > 127)) {
			$backgroundColor = $this->backgroundColor;
		}

		return $backgroundColor;
	}
}

?>