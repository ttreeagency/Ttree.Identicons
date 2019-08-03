<?php
namespace Ttree\Identicons\Generator;

use Imagine\Image\ImageInterface;
use Ttree\Identicons\Domain\Model\IdenticonConfiguration;
use Neos\Flow\Annotations as Flow;

/**
 * Generator Interface
 *
 * @package Ttree\Identicons\Generator
 */
interface GeneratorInterface
{
    /**
     * @param IdenticonConfiguration $hash
     * @return ImageInterface
     */
    public function generate(IdenticonConfiguration $hash);
}
