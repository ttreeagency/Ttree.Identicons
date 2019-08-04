<?php
namespace Ttree\Identicons\Generator;

use Imagine\Filter\Basic\FlipHorizontally;
use Imagine\Image\Box;
use Imagine\Image\ImageInterface;
use Imagine\Image\Palette;
use Imagine\Image\Palette\Color\RGB;
use Imagine\Image\Point;
use Ttree\Identicons\Domain\Model\IdenticonConfiguration;
use Neos\Flow\Annotations as Flow;

/**
 * Original Don Park identicon generator
 *
 * @package Ttree\Identicons\Generator
 * @Flow\Scope("singleton")
 */
class GithubLikeGenerator extends AbstractGenerator
{
    /**
     * @var RGB
     */
    protected $foregroundColor;

    /**
     * {@inheritdoc}
     */
    public function generate(IdenticonConfiguration $configuration)
    {
        parent::generate($configuration);
        
        $size = $this->defaultSize;
        $configuration = $configuration->getOriginalHash();

        $topCornerSpriteShape = hexdec(substr($configuration, 0, 1));
        $bottomCornerSpriteShape = hexdec(substr($configuration, 1, 1));
        $topMiddleSpriteShape = hexdec(substr($configuration, 2, 1)) & 2;
        $middleSideSpriteShape = hexdec(substr($configuration, 3, 1)) & 2;
        $centerSpriteShape = hexdec(substr($configuration, 4, 1)) & 2;
        $bottomSpriteShape = hexdec(substr($configuration, 5, 1)) & 7;

        $this->generateForegroundColor($configuration);

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

        $size = $identicon->getSize();
        $padding = $this->defaultSize / 2;
        $resized = $this->createImage($size->getWidth() + $padding, $size->getHeight() + $padding);
        $resized
            ->paste($identicon, new Point($padding / 2, $padding / 2))
            ->resize(new Box($this->defaultSize, $this->defaultSize));
        return $this->pad($identicon, $this->defaultSize / 2);
    }

    /**
     * @param string $hash
     */
    protected function generateForegroundColor($hash)
    {
        $palette = new Palette\RGB();
        $this->foregroundColor = new RGB($palette, [
            hexdec(substr($hash, 6, 2)),
            hexdec(substr($hash, 8, 2)),
            hexdec(substr($hash, 10, 2))
        ], 100);
    }


    /**
     * @param int $shape
     * @param float $rotation
     * @return ImageInterface
     * @throws \Exception
     */
    protected function getSquareSprite($shape, $rotation = null)
    {
        $sprite = $this->createImage($this->defaultSize, $this->defaultSize);

        $shape = $shape - 1;
        $shape = $this->selectShape($this->getConfigurationPath('squareSprite.shapes'), $shape, $this->getConfigurationPath('squareSprite.default'));

        $size = $sprite->getSize();
        $square = $this->createImage(0.5 * $this->defaultSize, 0.5 * $this->defaultSize, $this->foregroundColor);
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
     * @return ImageInterface
     * @throws \Exception
     */
    protected function getRectangularSprite($shape, $rotation = null)
    {
        $sprite = $this->createImage($this->defaultSize, $this->defaultSize / 2);

        $shape = $shape - 1;
        $shape = $this->selectShape($this->getConfigurationPath('rectangularSprite.shapes'), $shape, $this->getConfigurationPath('rectangularSprite.default'));

        $size = $sprite->getSize();
        $square = $this->createImage(0.5 * $this->defaultSize, 0.5 * $this->defaultSize, $this->foregroundColor);
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
     * @param integer $shape
     * @return ImageInterface
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
        return $this->createImage($this->defaultSize / 2, $this->defaultSize / 2, $color);
    }
}
