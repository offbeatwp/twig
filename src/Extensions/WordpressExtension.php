<?php
namespace OffbeatWP\Twig\Extensions;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class WordpressExtension extends AbstractExtension
{
    /** @inheritdoc */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('__', [$this, '__'])
        ];
    }

    public function __(string $text, string $domain = 'default'): string
    {
        return __($text, $domain);
    }
}
