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

use Ttree\Identicons\Domain\Model\IdenticonHash;
use TYPO3\Flow\Annotations as Flow;

/**
 * Generator Interface
 *
 * @package Ttree\Identicons\Generator
 */
interface GeneratorInterface
{
    /**
     * @param IdenticonHash $hash
     * @param integer $size
     * @return \Imagine\Image\ImageInterface
     */
    public function generate(IdenticonHash $hash, $size = null);
}
