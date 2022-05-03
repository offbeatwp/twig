<?php
namespace OffbeatWP\Twig\Extensions;

use Twig_Extension;
use Twig_Function;

class WordpressExtension extends Twig_Extension
{
    /** @return Twig_Function[] */
    public function getFunctions()
    {
        return [
            new Twig_Function('__', [$this, '__']),
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

    /**
     * @param string $string
     * @param string $textdomain
     * @param string|int|float ...$values
     * @return string
     */
    public function _s(string $string, string $textdomain = 'default', ...$values)
    {
        return sprintf(__($string, $textdomain), $values);
    }
}
