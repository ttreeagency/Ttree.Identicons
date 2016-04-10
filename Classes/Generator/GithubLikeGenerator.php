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

use Imagine\Filter\Basic\FlipHorizontally;
use Imagine\Image\Box;
use Imagine\Image\Color;
use Imagine\Image\ImageInterface;
use Imagine\Image\Point;
use TYPO3\Flow\Annotations as Flow;

/**
 * Original Don Park identicon generator
 *
 * @package Ttree\Identicons\Generator
 * @Flow\Scope("singleton")
 */
class GithubLikeGenerator extends AbstractGenerator
{
    /**
     * @var \Imagine\Image\Color
     */
    protected $foregroundColor;

    /**
     * {@inheritdoc}
     */
    public function generate($hash, $size = null)
    {
        $size = $size ? : $this->settingsService->getDefaultIconSize();

        $topCornerSpriteShape    = hexdec(substr($hash, 0, 1));
        $bottomCornerSpriteShape = hexdec(substr($hash, 1, 1));
        $topMiddleSpriteShape      = hexdec(substr($hash, 2, 1)) & 2;
        $middleSideSpriteShape   = hexdec(substr($hash, 3, 1)) & 2;
        $centerSpriteShape       = hexdec(substr($hash, 4, 1)) & 2;
        $bottomSpriteShape       = hexdec(substr($hash, 5, 1)) & 7;

        $this->generateForegroundColor($hash);

        $identicon = $this->createImage($size * 2.5, $size * 2.5);

        $flipFilter = new FlipHorizontally();

        $topCornerSprite = $this->getSquareSprite($topCornerSpriteShape);
        $identicon->paste($topCornerSprite, new Point(0, 0));
        $identicon->paste($flipFilter->apply($topCornerSprite), new Point($size * 1.5, 0));

        $bottomCornerSprite = $this->getSquareSprite($bottomCornerSpriteShape);
        $identicon->paste($bottomCornerSprite, new Point(0, $size * 1.5));
        $identicon->paste($flipFilter->apply($bottomCornerSprite), new Point($size * 1.5, $size * 1.5));

        $topMiddleSprite = $this->getRectangularSprite($topMiddleSpriteShape, 90);
        $identicon->paste($topMiddleSprite, new Point($size, 0));

        $leftMiddleSideSprite = $this->getRectangularSprite($middleSideSpriteShape, 0);
        $identicon->paste($leftMiddleSideSprite, new Point(0, $size));
        $identicon->paste($flipFilter->apply($leftMiddleSideSprite), new Point($size * 1.5, $size));

        $bottomSideSprite = $this->getRectangularSprite($bottomSpriteShape, 90);
        $identicon->paste($bottomSideSprite, new Point($size, $size * 1.5));

        $centerSprite = $this->getCenter($centerSpriteShape);
        $identicon->paste($centerSprite, new Point($size, $size));

        $size    = $identicon->getSize();
        $padding = $this->settingsService->getDefaultIconSize() / 2;
        $resized = $this->createImage($size->getWidth() + $padding, $size->getHeight() + $padding);
        $resized->paste($identicon, new Point($padding / 2, $padding / 2))->resize(new Box($this->settingsService->getDefaultIconSize(), $this->settingsService->getDefaultIconSize()));

        return $this->pad($identicon, $this->settingsService->getDefaultIconSize() / 2);
    }

    /**
     * @param string $hash
     */
    protected function generateForegroundColor($hash)
    {
        $this->foregroundColor = new Color(array(
            hexdec(substr($hash, 6, 2)),
            hexdec(substr($hash, 8, 2)),
            hexdec(substr($hash, 10, 2))
        ));
    }


    /**
     * @param int $shape
     * @param float $rotation
     * @return \Imagine\Image\ImageInterface
     * @throws \Exception
     */
    protected function getSquareSprite($shape, $rotation = null)
    {
        $sprite = $this->createImage($this->settingsService->getDefaultIconSize(), $this->settingsService->getDefaultIconSize());
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
        $size   = $sprite->getSize();
        $square = $this->createImage(0.5 * $this->settingsService->getDefaultIconSize(), 0.5 * $this->settingsService->getDefaultIconSize(), $this->foregroundColor);
        for ($i = 0; $i < count($shape); $i++) {
            if ($shape[$i] === 0) {
                continue;
            }
            $position = null;
            switch ($i) {
                case 0:
                    $position = new Point(0, 0, $size->getWidth() / 2, $size->getHeight() / 2);
                    break;
                case 1:
                    $position = new Point($size->getWidth() / 2, 0, $size->getWidth(), $size->getHeight() / 2);
                    break;
                case 2:
                    $position = new Point(0, $size->getHeight() / 2, $size->getWidth() / 2, $size->getHeight());
                    break;
                case 3:
                    $position = new Point($size->getWidth() / 2, $size->getHeight() / 2, $size->getWidth(), $size->getHeight());
                    break;
            }
            if ($position === null) {
                throw new \Exception('Invalid position', 1376733310);
            }
            $sprite->paste($square, $position);
        }

        $sprite = $this->rotate($sprite, $rotation);

        return $sprite;
    }

    /**
     * @param int $shape
     * @param float $rotation
     * @return \Imagine\Image\ImageInterface
     * @throws \Exception
     */
    protected function getRectangularSprite($shape, $rotation = null)
    {
        $sprite = $this->createImage($this->settingsService->getDefaultIconSize(), $this->settingsService->getDefaultIconSize() / 2);

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
                    1
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
                    0
                );
                break;
            default:
                $shape = array(
                    1,
                    1
                );
                break;
        }

        $size   = $sprite->getSize();
        $square = $this->createImage(0.5 * $this->settingsService->getDefaultIconSize(), 0.5 * $this->settingsService->getDefaultIconSize(), $this->foregroundColor);
        for ($i = 0; $i < count($shape); $i++) {
            if ($shape[$i] === 0) {
                continue;
            }
            $position = null;
            switch ($i) {
                case 0:
                    $position = new Point($size->getWidth() / 2, 0, $size->getWidth(), $size->getHeight());
                    break;
                case 1:
                    $position = new Point(0, 0, $size->getWidth() / 2, $size->getHeight());
                    break;
            }
            if ($position === null) {
                throw new \Exception('Invalid position', 1376743851);
            }
            $sprite->paste($square, $position);
        }

        $sprite = $this->rotate($sprite, $rotation);

        return $sprite;
    }

    /**
     * @param int $shape
     * @return \Imagine\Image\ImageInterface
     */
    protected function getCenter($shape)
    {
        switch ($shape) {
            case 0:
                $color = $this->foregroundColor;
                break;
            case 1:
                $color = $this->foregroundColor;
                break;
            case 2:
                $color = $this->backgroundColor;
                break;
        }
        return $this->createImage($this->settingsService->getDefaultIconSize() / 2, $this->settingsService->getDefaultIconSize() / 2, $color);
    }
}
