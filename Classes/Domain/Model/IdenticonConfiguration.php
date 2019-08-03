<?php
namespace Ttree\Identicons\Domain\Model;

use Doctrine\ORM\Mapping as ORM;
use Neos\Flow\Annotations as Flow;
use Neos\Utility\Unicode\Functions;

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
