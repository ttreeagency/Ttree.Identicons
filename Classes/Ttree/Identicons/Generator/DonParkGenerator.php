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

use TYPO3\Flow\Annotations as Flow;
use Doctrine\ORM\Mapping as ORM;

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

		$identicon = imagecreatetruecolor($size * 3, $size * 3);
		imageantialias($identicon, TRUE);

		$backgroundColor = imagecolorallocate($identicon, 255, 255, 255);
		imagefilledrectangle($identicon, 0, 0, $size, $size, $backgroundColor);

		$corner = $this->getSprite($cornerSpriteShape, $cornerSpriteForegroundColorRed, $cornerSpriteForegroundColorGreen, $cornerSpriteForegroundColorBlue, $cornerSpriteRotation);
		imagecopy($identicon, $corner, 0, 0, 0, 0, $size, $size);
		$corner = imagerotate($corner, 90, $backgroundColor);
		imagecopy($identicon, $corner, 0, $size * 2, 0, 0, $size, $size);
		$corner = imagerotate($corner, 90, $backgroundColor);
		imagecopy($identicon, $corner, $size * 2, $size * 2, 0, 0, $size, $size);
		$corner = imagerotate($corner, 90, $backgroundColor);
		imagecopy($identicon, $corner, $size * 2, 0, 0, 0, $size, $size);

		$side = $this->getSprite($sideSpriteShape, $sideSpriteForegroundColorRed, $sideSpriteForegroundColorGreen, $sideSpriteForegroundColorBlue, $sideSpriteRotation);
		imagecopy($identicon, $side, $size, 0, 0, 0, $size, $size);
		$side = imagerotate($side, 90, $backgroundColor);
		imagecopy($identicon, $side, 0, $size, 0, 0, $size, $size);
		$side = imagerotate($side, 90, $backgroundColor);
		imagecopy($identicon, $side, $size, $size * 2, 0, 0, $size, $size);
		$side = imagerotate($side, 90, $backgroundColor);
		imagecopy($identicon, $side, $size * 2, $size, 0, 0, $size, $size);

		/* generate center sprite */
		$center = $this->getCenter($centerSpriteShape, $cornerSpriteForegroundColorRed, $cornerSpriteForegroundColorGreen, $cornerSpriteForegroundColorBlue, $sideSpriteForegroundColorRed, $sideSpriteForegroundColorGreen, $sideSpriteForegroundColorBlue, $centerSpriteBackground);
		imagecopy($identicon, $center, $size, $size, 0, 0, $size, $size);

		/* make white transparent */
		imagecolortransparent($identicon, $backgroundColor);

		/* create blank image according to specified dimensions */
		$resized = imagecreatetruecolor($size, $size);
		imageantialias($resized, TRUE);

		/* assign white as background */
		$backgroundColor = imagecolorallocate($resized, 255, 255, 255);
		imagefilledrectangle($resized, 0, 0, $size, $size, $backgroundColor);

		/* resize identicon according to specification */
		imagecopyresampled($resized, $identicon, 0, 0, (imagesx($identicon) - $size * 3) / 2, (imagesx($identicon) - $size * 3) / 2, $size, $size, $size * 3, $size * 3);

		/* make white transparent */
		imagecolortransparent($resized, $backgroundColor);

		return $resized;
	}

	/**
	 * @param int $shape
	 * @param int $red
	 * @param int $green
	 * @param int $blue
	 * @param float $rotation
	 * @return resource
	 */
	protected function getSprite($shape, $red, $green, $blue, $rotation) {
		$sprite = imagecreatetruecolor($this->getSize(), $this->getSize());
		imageantialias($sprite, TRUE);
		$foregroundColor = imagecolorallocate($sprite, $red, $green, $blue);
		$backgroundColor = imagecolorallocate($sprite, 255, 255, 255);
		imagefilledrectangle($sprite, 0, 0, $this->getSize(), $this->getSize(), $backgroundColor);
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
		/* apply ratios */
		for ($i = 0; $i < count($shape); $i++) {
			$shape[$i] = $shape[$i] * $this->getSize();
		}
		imagefilledpolygon($sprite, $shape, count($shape) / 2, $foregroundColor);
		/* rotate the sprite */
		for ($i = 0; $i < $rotation; $i++) {
			$sprite = imagerotate($sprite, 90, $backgroundColor);
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
	 * @return resource
	 */
	protected function getCenter($shape, $foregroundRed, $foregroundGreen, $foregroundBlue, $backgroundRed, $backgroundGreen, $backgroundBlue, $useBackgroundColor = FALSE) {
		$size   = $this->getSize();
		$sprite = imagecreatetruecolor($size, $size);
		imageantialias($sprite, TRUE);
		$foregroundColor = imagecolorallocate($sprite, $foregroundRed, $foregroundGreen, $foregroundBlue);
		/* make sure there's enough contrast before we use background color of side sprite */
		if ($useBackgroundColor === TRUE && (abs($foregroundRed - $backgroundRed) > 127 || abs($foregroundGreen - $backgroundGreen) > 127 || abs($foregroundBlue - $backgroundBlue) > 127)) {
			$backgroundColor = imagecolorallocate($sprite, $backgroundRed, $backgroundGreen, $backgroundBlue);
		} else {
			$backgroundColor = imagecolorallocate($sprite, 255, 255, 255);
		}
		imagefilledrectangle($sprite, 0, 0, $size, $size, $backgroundColor);
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
			$shape[$i] = $shape[$i] * $size;
		}
		if (count($shape) > 0) {
			imagefilledpolygon($sprite, $shape, count($shape) / 2, $foregroundColor);
		}

		return $sprite;
	}
}

?>