<?php

namespace OffbeatWP\Twig\Extensions;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;
use WP_Embed;

final class RenderBlockExtension extends AbstractExtension
{
    /** @inheritdoc */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('render_block', [$this, 'renderBlockFunction'], ['pre_escape' => 'html', 'is_safe' => ['html']]),
        ];
    }

    /** @inheritdoc */
    public function getFilters(): array
    {
        return [
            new TwigFilter('render_block', [$this, 'renderBlockFilter'], ['pre_escape' => 'html', 'is_safe' => ['html']]),
        ];
    }

    /** @param mixed[] $attributes */
    public function renderBlockFunction(string $blockName, array $attributes = [], string $content = ''): string
    {
        return $this->renderBlock($blockName, $attributes);
    }

    /** @param mixed[] $attributes */
    public function renderBlockFilter(string $content, string $blockName, array $attributes = []): string
    {
        return $this->renderBlock($blockName, $attributes, $content);
    }

    /** @param mixed[] $attributes */
    protected function renderBlock(string $blockName, array $attributes = [], string $content = ''): string
    {
        $blockArgs = [
            'blockName' => $blockName,
            'attrs' => $attributes,
            'innerBlocks' => []
        ];

        if ($content) {
            $blockArgs['innerHTML'] = $content;
            $blockArgs['innerContent'] = [$content];
        }

        return (new WP_Embed())->autoembed(render_block($blockArgs));
    }
}
