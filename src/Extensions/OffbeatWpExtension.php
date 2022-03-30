<?php
namespace OffbeatWP\Twig\Extensions;

use Twig_Extension;
use Twig_Function;
use OffbeatWP\Contracts\SiteSettings;

class OffbeatWpExtension extends Twig_Extension
{
    public function getFunctions()
    {
        return [
            new Twig_Function('config', [$this, 'getConfig']),
            new Twig_Function('assetUrl', [$this, 'getAssetUrl']),
            new Twig_Function('component', [$this, 'getComponent']),
            new Twig_Function('setting', [$this, 'getSetting']),
        ];
    }

    public function getConfig($key)
    {
        return config($key);
    }

    public function getAssetUrl($file)
    {
        return assetUrl($file);
    }

    public function getComponent($name, $args = []): void
    {
        echo container('components')->render($name, $args);
    }

    public function getSetting($key)
    {
        return offbeat(SiteSettings::class)->get($key);
    }
}
