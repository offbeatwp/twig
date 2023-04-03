<?php
namespace OffbeatWP\Twig\Extensions;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class WordpressExtension extends AbstractExtension
{
    /** @return TwigFunction[] */
    public function getFunctions()
    {
        return [
            new TwigFunction('__', [$this, '__'])
        ];
    }

    /**
     * @param string $text
     * @param string $domain
     * @return string
     */
    public function __($text, $domain = 'default')
    {
        return __($text, $domain);
    }
}
