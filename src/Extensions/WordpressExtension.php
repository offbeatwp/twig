<?php
namespace OffbeatWP\Twig\Extensions;

use Twig_Extension;
use Twig_Function;

class WordpressExtension extends Twig_Extension
{
    public function getFunctions()
    {
        return [
            new Twig_Function('__', [$this, '__']),
        ];
    }

    public function __($string, $textdomain)
    {
        return __($string, $textdomain);
    }
}
