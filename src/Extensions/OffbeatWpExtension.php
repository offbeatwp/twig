<?php
namespace OffbeatWP\Twig\Extensions;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class OffbeatWpExtension extends AbstractExtension
{
    /** @inheritdoc */
    public function getFunctions()
    {
        return [
            new TwigFunction('config', [$this, 'getConfig']),
            new TwigFunction('assetUrl', [$this, 'getAssetUrl']),
        ];
    }

    public function getConfig(string $key): mixed
    {
        return config($key);
    }

    public function getAssetUrl(string $file): string
    {
        return assetUrl($file);
    }
}
