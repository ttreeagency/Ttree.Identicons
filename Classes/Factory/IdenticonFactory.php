<?php
namespace Ttree\Identicons\Factory;

use Doctrine\ORM\Mapping as ORM;
use Imagine\Image\ImageInterface;
use Neos\Cache\Exception;
use Ttree\Identicons\Domain\Model\IdenticonConfiguration;
use Ttree\Identicons\Generator\GeneratorInterface;
use Neos\Flow\Annotations as Flow;
use Neos\Cache\Frontend\VariableFrontend;

/**
 * Identicon Factory
 *
 * @Flow\Scope("singleton")
 */
class IdenticonFactory
{
    const DEFAULT_IMAGE_FORMAT = 'png';

    /**
     * @var GeneratorInterface
     * @Flow\Inject
     */
    protected $identiconGenerator;

    /**
     * @var boolean
     * @Flow\InjectConfiguration(path="persist", package="Ttree.Identicons")
     */
    protected $persistenceEnabled;

    /**
     * @var VariableFrontend
     * @Flow\Inject
     */
    protected $cache;

    public static function defaultImageOptions(): array
    {
        return [
            'png_compression_level' => 6,
            'png_compression_filter' => 5,
            'flatten' => true,
            'filter' => PNG_ALL_FILTERS
        ];
    }

    /**
     * @param IdenticonConfiguration $hash
     * @return string
     * @throws Exception
     */
    public function create(IdenticonConfiguration $hash)
    {
        if ($this->cache->has((string)$hash) && $this->persistenceEnabled) {
            return $this->cache->get((string)$hash);
        }
        $identicon = $this->identiconGenerator->generate($hash);
        $this->emitIdenticonCreated($identicon, $hash);

        $image = $identicon->get(
            IdenticonFactory::DEFAULT_IMAGE_FORMAT,
            IdenticonFactory::defaultImageOptions()
        );
        if ($this->persistenceEnabled) {
            $this->cache->set((string)$hash, $image);
        }
        return $image;
    }

    /**
     * Emits a signal when an Identicon is created
     *
     * @param ImageInterface $identicon
     * @param IdenticonConfiguration $hash
     * @return void
     * @Flow\Signal
     */
    protected function emitIdenticonCreated(ImageInterface $identicon, IdenticonConfiguration $hash) {

    }
}
