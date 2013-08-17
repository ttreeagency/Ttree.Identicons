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
 */
class DonParkGenerator extends AbstractGenerator {

	/**
	 * @param string $hash
	 * @param int $size
	 * @return resource
	 */
	public function generate($hash, $size = NULL) {
		$size = $size ? : $this->getSize();

		$cornerSpriteShape = hexdec(substr($hash, 0, 1));
		$sideSpriteShape   = hexdec(substr($hash, 1, 1));
		$centerSpriteShape = hexdec(substr($hash, 2, 1)) & 7;

		$cornerSpriteRotation   = hexdec(substr($hash, 3, 1)) & 3;
		$sideSpriteRotation     = hexdec(substr($hash, 4, 1)) & 3;
		$centerSpriteBackground = hexdec(substr($hash, 5, 1)) % 2;

		$cornerSpriteForegroundColorRed   = hexdec(substr($hash, 6, 2));
		$cornerSpriteForegroundColorGreen = hexdec(substr($hash, 8, 2));
		$cornerSpriteForegroundColorBlue  = hexdec(substr($hash, 10, 2));

		$sideSpriteForegroundColorRed   = hexdec(substr($hash, 12, 2));
		$sideSpriteForegroundColorGreen = hexdec(substr($hash, 14, 2));
		$sideSpriteForegroundColorBlue  = hexdec(substr($hash, 16, 2));

		$backgroundColor = new Color(array(255, 255, 255));
		$identicon       = $this->createImage($size * 3, $size * 3, $backgroundColor);

		$corner = $this->getSprite($cornerSpriteShape, $cornerSpriteForegroundColorRed, $cornerSpriteForegroundColorGreen, $cornerSpriteForegroundColorBlue, $cornerSpriteRotation);
		$identicon->paste($corner, new Point(0, 0));
		$identicon->paste($this->rotate($corner, 90), new Point(0, $size * 2));
		$identicon->paste($this->rotate($corner, 90), new Point($size * 2, $size * 2));
		$identicon->paste($this->rotate($corner, 90), new Point($size * 2, 0));

		$side = $this->getSprite($sideSpriteShape, $sideSpriteForegroundColorRed, $sideSpriteForegroundColorGreen, $sideSpriteForegroundColorBlue, $sideSpriteRotation);
		$identicon->paste($side, new Point($size, 0));
		$identicon->paste($this->rotate($side, 90), new Point(0, $size));
		$identicon->paste($this->rotate($side, 90), new Point($size, $size * 2));
		$identicon->paste($this->rotate($side, 90), new Point($size * 2, $size));

		$center = $this->getCenter($centerSpriteShape, $cornerSpriteForegroundColorRed, $cornerSpriteForegroundColorGreen, $cornerSpriteForegroundColorBlue, $sideSpriteForegroundColorRed, $sideSpriteForegroundColorGreen, $sideSpriteForegroundColorBlue, $centerSpriteBackground);
		$identicon->paste($center, new Point($size, $size));

		/* create blank image according to specified dimensions */
		$identicon = $identicon->resize(new Box($size, $size));

		return $identicon;
	}

	/**
	 * @param int $shape
	 * @param int $red
	 * @param int $green
	 * @param int $blue
	 * @param float $rotation
	 * @return \Imagine\Image\ImageInterface
	 */
	protected function getSprite($shape, $red, $green, $blue, $rotation) {
		$foregroundColor = new Color(array($red, $green, $blue));
		$backgroundColor = new Color(array(255, 255, 255));
		$sprite          = $this->createImage($this->getSize(), $this->getSize(), $backgroundColor);

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
		$sprite->draw()->polygon($this->applyRatioToShapeCoordinates($shape, $this->getSize()), $foregroundColor, TRUE);

		/* rotate the sprite */
		for ($i = 0; $i < $rotation; $i++) {
			$sprite = $this->rotate($sprite, 90);
		}

		return $sprite;
	}

	/**
	 * @param int $shape
	 * @param int $foregroundRed
	 * @param int $foregroundGreen
	 * @param int $foregroundBlue
	 * @param int $backgroundRed
	 * @param int $backgroundGreen
	 * @param int $backgroundBlue
	 * @param bool $useBackgroundColor
	 * @return \Imagine\Image\ImageInterface
	 */
	protected function getCenter($shape, $foregroundRed, $foregroundGreen, $foregroundBlue, $backgroundRed, $backgroundGreen, $backgroundBlue, $useBackgroundColor = FALSE) {
		/* make sure there's enough contrast before we use background color of side sprite */
		if ($useBackgroundColor === TRUE) {
			$backgroundColor = $this->generateBackgroundColorBasedOnContrast($foregroundRed, $foregroundGreen, $foregroundBlue, $backgroundRed, $backgroundGreen, $backgroundBlue);
		} else {
			$backgroundColor = $this->backgroundColor;
		}
		$foregroundColor = new Color(array($foregroundRed, $foregroundGreen, $foregroundBlue));

		$sprite = $this->createImage($this->getSize(), $this->getSize(), $backgroundColor);
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
		/* apply ratios */
		for ($i = 0; $i < count($shape); $i++) {
			$shape[$i] = $shape[$i] * $this->getSize();
		}
		if (count($shape) > 0) {
			$sprite->draw()->polygon($this->applyRatioToShapeCoordinates($shape, $this->getSize()), $foregroundColor, TRUE);
		}

		return $sprite;
	}

	/**
	 * @param int $foregroundRed
	 * @param int $foregroundGreen
	 * @param int $foregroundBlue
	 * @param int $backgroundRed
	 * @param int $backgroundGreen
	 * @param int $backgroundBlue
	 * @return Color
	 */
	protected function generateBackgroundColorBasedOnContrast($foregroundRed, $foregroundGreen, $foregroundBlue, $backgroundRed, $backgroundGreen, $backgroundBlue) {
		if (abs($foregroundRed - $backgroundRed) > 127 || abs($foregroundGreen - $backgroundGreen) > 127 || abs($foregroundBlue - $backgroundBlue) > 127) {
			$backgroundColor = new Color(array($backgroundRed, $backgroundGreen, $backgroundBlue));
		} else {
			$backgroundColor = $this->backgroundColor;
		}

		return $backgroundColor;
	}
}

?>