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
class GithubLikeGenerator extends AbstractGenerator {

	const IMAGE_FLIP_HORIZONTAL = 1;

	const IMAGE_FLIP_VERTICAL = 2;

	const IMAGE_FLIP_BOTH = 3;

	/**
	 * @param string $hash
	 * @param int $size
	 * @return resource
	 */
	public function generate($hash, $size = NULL) {
		$size = $size ? : $this->getSize();

		$topCornerSpriteShape    = hexdec(substr($hash, 0, 1));
		$bottomCornerSpriteShape = hexdec(substr($hash, 1, 1));
		$topSideSpriteShape      = hexdec(substr($hash, 2, 1)) & 2;
		$middleSideSpriteShape   = hexdec(substr($hash, 3, 1)) & 2;
		$centerSpriteShape       = hexdec(substr($hash, 4, 1)) & 7;
		$bottomSpriteShape       = hexdec(substr($hash, 5, 1)) & 7;

		$foregroundColorRed   = hexdec(substr($hash, 6, 2));
		$foregroundColorBlue  = hexdec(substr($hash, 8, 2));
		$foregroundColorGreen = hexdec(substr($hash, 10, 2));

		$identicon = imagecreatetruecolor($size * 2.5, $size * 2.5);

		$backgroundColor = $this->getBackgroundColor($identicon);
		imagefilledrectangle($identicon, 0, 0, $size, $size, $backgroundColor);

		$topCorner = $this->getSquareSprite($topCornerSpriteShape, $foregroundColorRed, $foregroundColorBlue, $foregroundColorGreen);
		imagecopy($identicon, $topCorner, 0, 0, 0, 0, $size, $size);
		$topCorner = $this->flipImage($topCorner, self::IMAGE_FLIP_VERTICAL);
		imagecopy($identicon, $topCorner, $size * 1.5, 0, 0, 0, $size, $size);

		$bottomCorner = $this->getSquareSprite($bottomCornerSpriteShape, $foregroundColorRed, $foregroundColorBlue, $foregroundColorGreen);
		imagecopy($identicon, $bottomCorner, 0, $size * 1.5, 0, 0, $size, $size);
		$bottomCorner = $this->flipImage($bottomCorner, self::IMAGE_FLIP_VERTICAL);
		imagecopy($identicon, $bottomCorner, $size * 1.5, $size * 1.5, 0, 0, $size, $size);

		$topSide = $this->getRectangularSprite($topSideSpriteShape, $foregroundColorRed, $foregroundColorBlue, $foregroundColorGreen, 90);
		imagecopy($identicon, $topSide, $size, 0, 0, 0, $size / 2, $size);

		$leftMiddleSide = $this->getRectangularSprite($middleSideSpriteShape, $foregroundColorRed, $foregroundColorBlue, $foregroundColorGreen, 0);
		imagecopy($identicon, $leftMiddleSide, 0, $size, 0, 0, $size, $size / 2);

		$rightMiddleSide = $this->flipImage($leftMiddleSide, self::IMAGE_FLIP_VERTICAL);
		imagecopy($identicon, $rightMiddleSide, $size * 1.5, $size, 0, 0, $size, $size / 2);

		$bottomSide = $this->getRectangularSprite($bottomSpriteShape, $foregroundColorRed, $foregroundColorBlue, $foregroundColorGreen, 90);
		imagecopy($identicon, $bottomSide, $size, $size * 1.5, 0, 0, $size / 2, $size);

		$center = $this->getCenter($centerSpriteShape, $foregroundColorRed, $foregroundColorBlue, $foregroundColorGreen);
		imagecopy($identicon, $center, $size, $size, 0, 0, $size / 2, $size / 2);

		/* create blank image according to specified dimensions */
		$resized = imagecreatetruecolor($size, $size);

		/* assign white as background */
		$backgroundColor = $this->getBackgroundColor($resized);
		imagefilledrectangle($resized, 0, 0, $size, $size, $backgroundColor);

		/* resize identicon according to specification */
		imagecopyresampled($resized, $identicon, 30, 30, (imagesx($identicon) - $size * 2.5) / 2, (imagesx($identicon) - $size * 2.5) / 2, $size - 60, $size - 60, $size * 2.5, $size * 2.5);

		return $resized;
	}

	/**
	 * @param resource $sprite
	 * @return int
	 */
	protected function getBackgroundColor($sprite) {
		return imagecolorallocate($sprite, 235, 235, 235);
	}

	/**
	 * @param int $shape
	 * @param int $red
	 * @param int $green
	 * @param int $blue
	 * @param float $rotation
	 * @return resource
	 */
	protected function getSquareSprite($shape, $red, $green, $blue, $rotation = NULL) {
		$sprite          = imagecreatetruecolor($this->getSize(), $this->getSize());
		$foregroundColor = imagecolorallocate($sprite, $red, $green, $blue);
		$backgroundColor = $this->getBackgroundColor($sprite);
		imagefilledrectangle($sprite, 0, 0, $this->getSize(), $this->getSize(), $backgroundColor);
		switch ($shape) {
			case 1:
				$shape = array(
					0, 0,
					1, 1
				);
				break;
			case 2:
				$shape = array(
					0, 1,
					1, 1
				);
				break;
			case 3:
				$shape = array(
					0, 1,
					0, 1
				);
				break;
			case 4:
				$shape = array(
					1, 1,
					0, 0
				);
				break;
			case 5:
				$shape = array(
					1, 1,
					1, 0
				);
				break;
			case 6:
				$shape = array(
					1, 0,
					1, 1
				);
				break;
			case 7:
				$shape = array(
					1, 0,
					0, 1
				);
				break;
			case 8:
				$shape = array(
					0, 1,
					1, 0
				);
				break;
			case 9:
				$shape = array(
					0, 1,
					0, 0
				);
				break;
			case 10:
				$shape = array(
					1, 0,
					0, 0
				);
				break;
			case 11:
				$shape = array(
					1, 0,
					1, 0
				);
				break;
			case 12:
				$shape = array(
					1, 1,
					0, 1
				);
				break;
			default:
				$shape = array(
					0, 0,
					0, 1
				);
				break;
		}
		/* apply ratios */
		for ($i = 0; $i < count($shape); $i++) {
			if ($shape[$i] === 0) {
				continue;
			}
			switch ($i) {
				case 0:
					imagefilledrectangle($sprite, 0, 0, 0.5 * $this->getSize(), 0.5 * $this->getSize(), $foregroundColor);
					break;
				case 1:
					imagefilledrectangle($sprite, 0.5 * $this->getSize(), 0 * $this->getSize(), 1 * $this->getSize(), 0.5 * $this->getSize(), $foregroundColor);
					break;
				case 2:
					imagefilledrectangle($sprite, 0 * $this->getSize(), 0.5 * $this->getSize(), 0.5 * $this->getSize(), 1 * $this->getSize(), $foregroundColor);
					break;
				case 3:
					imagefilledrectangle($sprite, 0.5 * $this->getSize(), 0.5 * $this->getSize(), 1 * $this->getSize(), 1 * $this->getSize(), $foregroundColor);
					break;
			}
		}

		if ($rotation !== NULL) {
			$sprite = imagerotate($sprite, $rotation, $backgroundColor);
		}

		return $sprite;
	}

	/**
	 * @param resource $imageResource
	 * @param int $mode
	 * @return resource
	 */
	protected function flipImage($imageResource, $mode) {
		if (function_exists('imageflip')) {
			return imageflip($imageResource, $mode);
		}

		$width  = imagesx($imageResource);
		$height = imagesy($imageResource);

		$srcX      = 0;
		$srcY      = 0;
		$srcWidth  = $width;
		$srcHeight = $height;

		switch ((int)$mode) {

			case self::IMAGE_FLIP_HORIZONTAL:
				$srcY      = $height;
				$srcHeight = -$height;
				break;

			case self::IMAGE_FLIP_VERTICAL:
				$srcX     = $width;
				$srcWidth = -$width;
				break;

			case self::IMAGE_FLIP_BOTH:
				$srcX      = $width;
				$srcY      = $height;
				$srcWidth  = -$width;
				$srcHeight = -$height;
				break;

			default:
				return $imageResource;
		}

		$imgdest = imagecreatetruecolor($width, $height);

		if (imagecopyresampled($imgdest, $imageResource, 0, 0, $srcX, $srcY, $width, $height, $srcWidth, $srcHeight)) {
			return $imgdest;
		}

		return $imageResource;
	}

	/**
	 * @param int $shape
	 * @param int $red
	 * @param int $green
	 * @param int $blue
	 * @param float $rotation
	 * @return resource
	 */
	protected function getRectangularSprite($shape, $red, $green, $blue, $rotation = NULL) {
		$width           = $this->getSize();
		$height          = $this->getSize() / 2;
		$sprite          = imagecreatetruecolor($width, $height);
		$foregroundColor = imagecolorallocate($sprite, $red, $green, $blue);
		$backgroundColor = $this->getBackgroundColor($sprite);
		imagefilledrectangle($sprite, 0, 0, $width, $height, $backgroundColor);
		switch ($shape) {
			case 1:
				$shape = array(
					0,
					0
				);
				break;
			case 2:
				$shape = array(
					1,
					0
				);
				break;
			case 3:
				$shape = array(
					0,
					1
				);
				break;
			case 4:
				$shape = array(
					1,
					1
				);
				break;
			default:
				$shape = array(
					1,
					1
				);
				break;
		}

		/* apply ratios */
		for ($i = 0; $i < count($shape); $i++) {
			if ($shape[$i] === 0) {
				continue;
			}
			switch ($i) {
				case 0:
					imagefilledrectangle($sprite, 0.5 * $width, 0 * $height, 1 * $width, 1 * $height, $foregroundColor);
					break;
				case 1:
					imagefilledrectangle($sprite, 0, 0, 0.5 * $width, 1 * $height, $foregroundColor);
					break;
			}
		}

		if ($rotation !== NULL) {
			$sprite = imagerotate($sprite, $rotation, $backgroundColor);
		}

		return $sprite;
	}

	/**
	 * @param int $shape
	 * @param int $red
	 * @param int $green
	 * @param int $blue
	 * @return resource
	 */
	protected function getCenter($shape, $red, $green, $blue) {
		$size            = $this->getSize();
		$sprite          = imagecreatetruecolor($size, $size);
		$foregroundColor = imagecolorallocate($sprite, $red, $green, $blue);
		$backgroundColor = $this->getBackgroundColor($sprite);
		imagefilledrectangle($sprite, 0, 0, $size, $size, $backgroundColor);
		switch ($shape) {
			case 0: // empty
				$shape = TRUE;
				break;
			case 1: // fill
				$shape = FALSE;
				break;
		}
		if ($shape === TRUE) {
			imagefilledrectangle($sprite, 0, 0, $size, $size, $foregroundColor);
		} else {
			imagefilledrectangle($sprite, 0, 0, $size, $size, $backgroundColor);
		}

		return $sprite;
	}
}

?>