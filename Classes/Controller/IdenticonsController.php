<?php
namespace Ttree\Identicons\Controller;

use Ttree\Identicons\Domain\Model\IdenticonConfiguration;
use Ttree\Identicons\Factory\IdenticonFactory;
use Ttree\Identicons\Service\SettingsService;
use Neos\Flow\Annotations as Flow;
use Neos\Flow\Exception;
use Neos\Flow\Mvc\Controller\ActionController;

/**
 * Identicon Controller
 */
class IdenticonsController extends ActionController
{
    /**
     * @var IdenticonFactory
     * @Flow\Inject
     */
    protected $identiconFactory;

    /**
     * @var SettingsService
     * @Flow\Inject
     */
    protected $settingsService;

    /**
     * @param string $hash
     * @param integer $s
     * @return string
     * @throws Exception
     */
    public function generateAction($hash, $s = null)
    {
        if ($this->request->getFormat() !== 'png') {
            throw new Exception('Invalid request format, currently only PNG format is supported', 1460365289);
        }
        $ttl = $this->settingsService->getCacheControlMaxAge();

        $this->response->setHeader('Content-Type', 'image/png');
        $this->response->setHeader('Cache-Control', sprintf('max-age=%i, public', $ttl));
        $this->response->setHeader('Expires', date(DATE_RFC1123, time()+$ttl));

        $configuration = IdenticonConfiguration::create($hash, $s ?: $this->settingsService->getDefaultIconSize());
        $image = $this->identiconFactory->create($configuration);

        return $image;
    }
}
