<?php
namespace Ttree\Identicons\Controller;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "Ttree.Identicons".      *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU General Public License, either version 3 of the   *
 * License, or (at your option) any later version.                        *
 *                                                                        *
 * The TYPO3 project - inspiring people to share!                         *
 *                                                                        */

use Ttree\Identicons\Domain\Model\IdenticonConfiguration;
use Ttree\Identicons\Factory\IdenticonFactory;
use Ttree\Identicons\Service\SettingsService;
use TYPO3\Flow\Annotations as Flow;
use TYPO3\Flow\Exception;
use TYPO3\Flow\Mvc\Controller\ActionController;

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

        $hash = IdenticonConfiguration::create($hash, $s ?: $this->settingsService->getDefaultIconSize());
        $identicon = $this->identiconFactory->create($hash);

        return $identicon->render();
    }
}
