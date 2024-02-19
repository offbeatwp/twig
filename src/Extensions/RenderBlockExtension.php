<?php

namespace OffbeatWP\Twig\Extensions;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;
use WP_Embed;

class RenderBlockExtension extends AbstractExtension
{
    public function getFunctions()
    {
        return [
            new TwigFunction('render_block', [$this, 'renderBlockFunction'], ['pre_escape' => 'html', 'is_safe' => ['html']]),
        ];
    }

    public function getFilters()
    {
        return [
            new TwigFilter('render_block', [$this, 'renderBlockFilter'], ['pre_escape' => 'html', 'is_safe' => ['html']]),
        ];
    }

    public function renderBlockFunction($blockName, $attributes = [], $content = null)
    {
        return $this->renderBlock($blockName, $attributes);
    }

    public function renderBlockFilter($content, $blockName, $attributes = [])
    {
        return $this->renderBlock($blockName, $attributes, $content);
    }

    protected function renderBlock($blockName, $attributes = [], $content = null)
    {
        $blockArgs = [
            'blockName' => $blockName,
            'attrs' => $attributes,
            'innerBlocks' => []
        ];

        if (!empty($content)) {
            $blockArgs['innerHTML'] = (string) $content;
            $blockArgs['innerContent'] = [(string) $content]; 
        }

        $content = render_block( $blockArgs );

        $content = (new WP_Embed)->autoembed($content);

        return $content;
    }
}
