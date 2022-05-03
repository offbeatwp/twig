<?php
namespace OffbeatWP\Twig\Extensions;

use OffbeatWP\Exceptions\NonexistentComponentException;
use Twig_Extension;
use Twig_Function;
use OffbeatWP\Contracts\SiteSettings;

class OffbeatWpExtension extends Twig_Extension
{
    /** @return Twig_Function[] */
    public function getFunctions()
    {
        return [
            new Twig_Function('config', [$this, 'getConfig']),
            new Twig_Function('assetUrl', [$this, 'getAssetUrl']),
            new Twig_Function('component', [$this, 'getComponent']),
            new Twig_Function('setting', [$this, 'getSetting']),
        ];
    }

    /**
     * @param string $key
     * @return mixed
     */
    public function getConfig($key)
    {
        return config($key);
    }

    /**
     * @param string $file
     * @return false|string
     */
    public function getAssetUrl($file)
    {
        return assetUrl($file);
    }

    /**
     * @param string $name
     * @param array $args
     * @return void
     * @throws NonexistentComponentException
     */
    public function getComponent($name, $args = [])
    {
        echo container('components')->render($name, $args);
    }

    /**
     * @param string $key
     * @return mixed
     */
    public function getSetting($key)
    {
        return offbeat(SiteSettings::class)->get($key);
    }
}
