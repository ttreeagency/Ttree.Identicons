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
use TYPO3\Media\Domain\Model\Image;

/**
 * Identicon Entity
 *
 * @Flow\Entity
 */
class Identicon
{
    /**
     * @var string
     * @ORM\Id
     */
    protected $token;

    /**
     * @var Image
     * @ORM\OneToOne(cascade={"persist"})
     */
    protected $image;

    /**
     * @param Image $image
     * @param string $token
     */
    public function __construct(Image $image, $token)
    {
        $this->image = $image;
        $this->token = $token;
    }

    /**
     * @return string
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * @return string
     */
    public function render()
    {
        $content = '';
        if ($this->getImage() !== null && $this->getImage()->getResource() !== null) {
            $handle = fopen($this->getImage()->getResource()->createTemporaryLocalCopy(), 'rb');
            $content = stream_get_contents($handle);
            fclose($handle);
        }

        return $content;
    }

    /**
     * @return Image
     */
    public function getImage()
    {
        return $this->image;
    }
}
