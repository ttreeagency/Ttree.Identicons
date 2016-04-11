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
class IdenticonHash
{
    /**
     * @var string
     */
    protected $hash;

    /**
     * @var string
     */
    protected $originalHash;

    /**
     * @param string $hash
     */
    public function __construct($hash)
    {
        $this->originalHash = Functions::strtolower(trim($hash));
        $this->hash = md5($this->originalHash);
    }

    /**
     * @param string $hash
     * @return string
     */
    public static function create($hash)
    {
        return new IdenticonHash($hash);
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
    function __toString()
    {
        return $this->hash;
    }
}
