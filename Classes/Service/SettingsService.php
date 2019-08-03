<?php
namespace Ttree\Identicons\Service;

use Neos\Flow\Annotations as Flow;
use Neos\Utility\Arrays;

/**
 * @Flow\Scope("singleton")
 */
class SettingsService
{
    /**
     * @Flow\InjectConfiguration
     * @var array
     */
    protected $settings;

    /**
     * @param array $settings
     */
    public function injectSettings(array $settings)
    {
        $this->settings = $settings;
    }

    /**
     * @param string $path
     * @return string|array|boolean
     */
    public function get($path)
    {
        return Arrays::getValueByPath($this->settings, $path);
    }

    /**
     * @return integer
     */
    public function getCacheControlMaxAge()
    {
        return (int)$this->get('ttl') ?: 2592000;
    }

    /**
     * @return integer
     */
    public function getDefaultIconSize()
    {
        return (int)$this->get('size.default') ?: 420;
    }
}
