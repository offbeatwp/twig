<?php
namespace OffbeatWP\Twig\Extensions;

use OffbeatWP\Exceptions\NonexistentComponentException;
use OffbeatWP\Contracts\SiteSettings;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class OffbeatWpExtension extends AbstractExtension
{
    /** @return TwigFunction[] */
    public function getFunctions()
    {
        return [
            new TwigFunction('config', [$this, 'getConfig']),
            new TwigFunction('assetUrl', [$this, 'getAssetUrl']),
            new TwigFunction('component', [$this, 'getComponent']),
            new TwigFunction('setting', [$this, 'getSetting']),
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
        if (is_array($args)) {
            $args = (object) $args;
        }

        $output = container('components')->render($name, $args);

        if (isset($args->echo) && $args->echo === false) {
            return $output;
        }
        
        echo $output;
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
