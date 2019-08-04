<?php
namespace Ttree\Identicons\Generator;

use Imagine\Image\Box;
use Imagine\Image\ImageInterface;
use Imagine\Image\Palette;
use Imagine\Image\Palette\Color\ColorInterface;
use Imagine\Image\Palette\Color\RGB;
use Imagine\Image\Point;
use Ttree\Identicons\Domain\Model\IdenticonConfiguration;
use Neos\Flow\Annotations as Flow;
use Neos\Flow\Exception;

/**
 * Original Don Park identicon generator
 *
 * @package Ttree\Identicons\Generator
 * @Flow\Scope("singleton")
 */
class DonParkGenerator extends AbstractGenerator
{
    /**
     * {@inheritdoc}
     */
    public function generate(IdenticonConfiguration $configuration)
    {
        parent::generate($configuration);

        $size = $this->defaultSize;
        $configuration = $configuration->getOriginalHash();

        $cornerSpriteShape = hexdec(substr($configuration, 0, 1));
        $sideSpriteShape = hexdec(substr($configuration, 1, 1));
        $centerSpriteShape = hexdec(substr($configuration, 2, 1)) & 7;

        $cornerSpriteRotation = hexdec(substr($configuration, 3, 1)) & 3;
        $sideSpriteRotation = hexdec(substr($configuration, 4, 1)) & 3;

        $palette = new Palette\RGB();
        $cornerSpriteForegroundColor = new RGB($palette, [
            hexdec(substr($configuration, 6, 2)),
            hexdec(substr($configuration, 8, 2)),
            hexdec(substr($configuration, 10, 2))
        ], 100);
        $sideSpriteForegroundColor = new RGB($palette, [
            hexdec(substr($configuration, 12, 2)),
            hexdec(substr($configuration, 14, 2)),
            hexdec(substr($configuration, 16, 2))
        ], 100);
        $centerSpriteForegroundColor = new RGB($palette, [
            hexdec(substr($configuration, 18, 2)),
            hexdec(substr($configuration, 20, 2)),
            hexdec(substr($configuration, 22, 2))
        ], 100);
        $centerSpriteBackgroundColor = new RGB($palette, [
            hexdec(substr($configuration, 24, 2)),
            hexdec(substr($configuration, 26, 2)),
            hexdec(substr($configuration, 28, 2))
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

        return $this->pad($identicon, $this->defaultSize / 4);
    }

    /**
     * @param integer $shape
     * @param ColorInterface $foregroundColor
     * @param integer $rotation
     * @return ImageInterface
     */
    protected function getSprite($shape, ColorInterface $foregroundColor, $rotation)
    {
        $sprite = $this->createImage($this->defaultSize, $this->defaultSize, $this->backgroundColor);

        $shape = $this->selectShape($this->getConfigurationPath('sprite.shapes'), $shape, $this->getConfigurationPath('sprite.default'));
        $sprite->draw()->polygon($this->applyRatioToShapeCoordinates($shape, $this->defaultSize), $foregroundColor, true);

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

        $shape = $this->selectShape($this->getConfigurationPath('centerSprite.shapes'), $shape, $this->getConfigurationPath('centerSprite.default'));
        $sprite = $this->createImage($this->defaultSize, $this->defaultSize, $backgroundColor);
        for ($i = 0; $i < count($shape); $i++) {
            $shape[$i] = $shape[$i] * $this->settingsService->getDefaultIconSize();
        }
        if (count($shape) > 0) {
            $sprite->draw()->polygon($this->applyRatioToShapeCoordinates($shape, $this->defaultSize), $foregroundColor, true);
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
