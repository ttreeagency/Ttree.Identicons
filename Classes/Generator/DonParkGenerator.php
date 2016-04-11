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
use Imagine\Image\ImageInterface;
use Imagine\Image\Palette;
use Imagine\Image\Palette\Color\ColorInterface;
use Imagine\Image\Palette\Color\RGB;
use Imagine\Image\Point;
use TYPO3\Flow\Annotations as Flow;
use TYPO3\Flow\Exception;

/**
 * Original Don Park identicon generator
 *
 * @package Ttree\Identicons\Generator
 * @Flow\Scope("singleton")
 */
class DonParkGenerator extends AbstractGenerator
{
    /**
     * @param string $hash
     * @param int $size
     * @return resource
     */
    public function generate($hash, $size = null)
    {
        $size = $size ?: $this->settingsService->getDefaultIconSize();

        $cornerSpriteShape = hexdec(substr($hash, 0, 1));
        $sideSpriteShape = hexdec(substr($hash, 1, 1));
        $centerSpriteShape = hexdec(substr($hash, 2, 1)) & 7;

        $cornerSpriteRotation = hexdec(substr($hash, 3, 1)) & 3;
        $sideSpriteRotation = hexdec(substr($hash, 4, 1)) & 3;

        $palette = new Palette\RGB();
        $cornerSpriteForegroundColor = new RGB($palette, [
            hexdec(substr($hash, 6, 2)),
            hexdec(substr($hash, 8, 2)),
            hexdec(substr($hash, 10, 2))
        ], 100);
        $sideSpriteForegroundColor = new RGB($palette, [
            hexdec(substr($hash, 12, 2)),
            hexdec(substr($hash, 14, 2)),
            hexdec(substr($hash, 16, 2))
        ], 100);
        $centerSpriteForegroundColor = new RGB($palette, [
            hexdec(substr($hash, 18, 2)),
            hexdec(substr($hash, 20, 2)),
            hexdec(substr($hash, 22, 2))
        ], 100);
        $centerSpriteBackgroundColor = new RGB($palette, [
            hexdec(substr($hash, 24, 2)),
            hexdec(substr($hash, 26, 2)),
            hexdec(substr($hash, 28, 2))
        ], 100);

        $backgroundColor = new RGB($palette, [255, 255, 255], 100);
        $identicon = $this->createImage($size * 3, $size * 3, $backgroundColor);

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
     * @param integer $shape
     * @param ColorInterface $foregroundColor
     * @param integer $rotation
     * @return ImageInterface
     */
    protected function getSprite($shape, ColorInterface $foregroundColor, $rotation)
    {
        $sprite = $this->createImage($this->settingsService->getDefaultIconSize(), $this->settingsService->getDefaultIconSize(), $this->backgroundColor);

        switch ($shape) {
            case 0: // triangle
                $shape = [0.5, 1, 1, 0, 1, 1];
                break;
            case 1: // parallelogram
                $shape = [0.5, 0, 1, 0, 0.5, 1, 0, 1];
                break;
            case 2: // mouse ears
                $shape = [0.5, 0, 1, 0, 1, 1, 0.5, 1, 1, 0.5];
                break;
            case 3: // ribbon
                $shape = [0, 0.5, 0.5, 0, 1, 0.5, 0.5, 1, 0.5, 0.5];
                break;
            case 4: // sails
                $shape = [0, 0.5, 1, 0, 1, 1, 0, 1, 1, 0.5];
                break;
            case 5: // fins
                $shape = [1, 0, 1, 1, 0.5, 1, 1, 0.5, 0.5, 0.5];
                break;
            case 6: // beak
                $shape = [0, 0, 1, 0, 1, 0.5, 0, 0, 0.5, 1, 0, 1];
                break;
            case 7: // chevron
                $shape = [0, 0, 0.5, 0, 1, 0.5, 0.5, 1, 0, 1, 0.5, 0.5];
                break;
            case 8: // fish
                $shape = [0.5, 0, 0.5, 0.5, 1, 0.5, 1, 1, 0.5, 1, 0.5, 0.5, 0, 0.5];
                break;
            case 9: // kite
                $shape = [0, 0, 1, 0, 0.5, 0.5, 1, 0.5, 0.5, 1, 0.5, 0.5, 0, 1];
                break;
            case 10: // trough
                $shape = [0, 0.5, 0.5, 1, 1, 0.5, 0.5, 0, 1, 0, 1, 1, 0, 1];
                break;
            case 11: // rays
                $shape = [0.5, 0, 1, 0, 1, 1, 0.5, 1, 1, 0.75, 0.5, 0.5, 1, 0.25];
                break;
            case 12: // double rhombus
                $shape = [0, 0.5, 0.5, 0, 0.5, 0.5, 1, 0, 1, 0.5, 0.5, 1, 0.5, 0.5, 0, 1];
                break;
            case 13: // crown
                $shape = [0, 0, 1, 0, 1, 1, 0, 1, 1, 0.5, 0.5, 0.25, 0.5, 0.75, 0, 0.5, 0.5, 0.25];
                break;
            case 14: // radioactive
                $shape = [0, 0.5, 0.5, 0.5, 0.5, 0, 1, 0, 0.5, 0.5, 1, 0.5, 0.5, 1, 0.5, 0.5, 0, 1];
                break;
            default: // tiles
                $shape = [0, 0, 1, 0, 0.5, 0.5, 0.5, 0, 0, 0.5, 1, 0.5, 0.5, 1, 0.5, 0.5, 0, 1];
                break;
        }
        $sprite->draw()->polygon($this->applyRatioToShapeCoordinates($shape, $this->settingsService->getDefaultIconSize()), $foregroundColor, true);

        /* rotate the sprite */
        for ($i = 0; $i < $rotation; $i++) {
            $sprite = $this->rotate($sprite, 90);
        }

        return $sprite;
    }

    /**
     * @param $shape
     * @param ColorInterface $foregroundColor
     * @param ColorInterface $backgroundColor
     * @return ImageInterface
     */
    protected function getCenter($shape, ColorInterface $foregroundColor, ColorInterface $backgroundColor = null)
    {
        /* make sure there's enough contrast before we use background color of side sprite */
        if ($backgroundColor !== null) {
            $backgroundColor = $this->generateBackgroundColorBasedOnContrast($foregroundColor, $backgroundColor);
        } else {
            $backgroundColor = $this->backgroundColor;
        }

        $sprite = $this->createImage($this->settingsService->getDefaultIconSize(), $this->settingsService->getDefaultIconSize(), $backgroundColor);
        switch ($shape) {
            case 0: // empty
                $shape = [];
                break;
            case 1: // fill
                $shape = [0, 0, 1, 0, 1, 1, 0, 1];
                break;
            case 2: // diamond
                $shape = [0.5, 0, 1, 0.5, 0.5, 1, 0, 0.5];
                break;
            case 3: // reverse diamond
                $shape = [0, 0, 1, 0, 1, 1, 0, 1, 0, 0.5, 0.5, 1, 1, 0.5, 0.5, 0, 0, 0.5];
                break;
            case 4: // cross
                $shape = [0.25, 0, 0.75, 0, 0.5, 0.5, 1, 0.25, 1, 0.75, 0.5, 0.5, 0.75, 1, 0.25, 1, 0.5, 0.5, 0, 0.75, 0, 0.25, 0.5, 0.5];
                break;
            case 5: // morning star
                $shape = [0, 0, 0.5, 0.25, 1, 0, 0.75, 0.5, 1, 1, 0.5, 0.75, 0, 1, 0.25, 0.5];
                break;
            case 6: // small square
                $shape = [0.33, 0.33, 0.67, 0.33, 0.67, 0.67, 0.33, 0.67];
                break;
            case 7: // checkerboard
                $shape = [0, 0, 0.33, 0, 0.33, 0.33, 0.66, 0.33, 0.67, 0, 1, 0, 1, 0.33, 0.67, 0.33, 0.67, 0.67, 1, 0.67, 1, 1, 0.67, 1, 0.67, 0.67, 0.33, 0.67, 0.33, 1, 0, 1, 0, 0.67, 0.33, 0.67, 0.33, 0.33, 0, 0.33];
                break;
        }
        for ($i = 0; $i < count($shape); $i++) {
            $shape[$i] = $shape[$i] * $this->settingsService->getDefaultIconSize();
        }
        if (count($shape) > 0) {
            $sprite->draw()->polygon($this->applyRatioToShapeCoordinates($shape, $this->settingsService->getDefaultIconSize()), $foregroundColor, true);
        }

        return $sprite;
    }

    /**
     * @param RGB $foregroundColor
     * @param RGB $backgroundColor
     * @return ColorInterface
     * @throws Exception
     */
    protected function generateBackgroundColorBasedOnContrast(RGB $foregroundColor, RGB $backgroundColor)
    {
        if (!(abs($foregroundColor->getRed() - $backgroundColor->getRed()) > 127 || abs($foregroundColor->getGreen() - $backgroundColor->getGreen()) > 127 || abs($foregroundColor->getBlue() - $backgroundColor->getBlue()) > 127)) {
            $backgroundColor = $this->backgroundColor;
        }

        return $backgroundColor;
    }
}
