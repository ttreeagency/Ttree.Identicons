<?php
namespace Ttree\Identicons\Domain\Model;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "Ttree.Identicons".      *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU General Public License, either version 3 of the   *
 * License, or (at your option) any later version.                        *
 *                                                                        *
 * The TYPO3 project - inspiring people to share!                         *
 *                                                                        */

use Doctrine\ORM\Mapping as ORM;
use TYPO3\Flow\Annotations as Flow;
use TYPO3\Flow\Utility\Unicode\Functions;
use TYPO3\Media\Domain\Model\Image;

/**
 * Identicon Hash
 */
class IdenticonConfiguration
{
    /**
     * @var string
     */
    protected $hash;

    /**
     * @var string
     */
    protected $size;

    /**
     * @var string
     */
    protected $originalHash;

    /**
     * @param string $hash
     * @param integer $size
     */
    public function __construct($hash, $size)
    {
        $this->originalHash = md5(Functions::strtolower(trim($hash)));
        $size = (integer)$size;
        $this->size = $size - ($size % 2);
        $hash = Functions::strtolower(trim($hash));
        $this->hash = md5($hash . $this->size);
    }

    /**
     * @param string $hash
     * @param integer $size
     * @return IdenticonConfiguration
     */
    public static function create($hash, $size)
    {
        return new IdenticonConfiguration($hash, $size);
    }

    /**
     * @return string
     */
    public function getOriginalHash()
    {
        return $this->originalHash;
    }

    /**
     * @return string
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * @return string
     */
    function __toString()
    {
        return $this->hash;
    }
}
