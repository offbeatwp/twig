<?php
namespace OffbeatWP\Twig;

use App\Services\Twig\Filters\Component;
use OffbeatWP\Contracts\View;
use OffbeatWP\Twig\Extensions\OffbeatWpExtension;
use OffbeatWP\Twig\Extensions\WordpressExtension;
use OffbeatWP\Views\Wordpress;
use Twig\Environment;
use Twig\Extension\DebugExtension;
use Twig\Loader\FilesystemLoader;
use Twig\Markup;
use Twig\TwigFilter;

class TwigView implements View
{
    protected $viewGlobals = [];

    protected $templatePaths = [];

    public function __construct () {
        if (is_dir(get_template_directory() . '/resources/views/')) {
            $this->addTemplatePath(get_template_directory() . '/resources/views/');
        }

        if (is_dir(get_template_directory() . '/views/')) {
            $this->addTemplatePath(get_template_directory() . '/views/');
        }
    }

    public function render($template, $data = [])
    {
        $twig = $this->getTwig();

        if (!is_string($template)) {
            return;
        }

        $renderResult = $twig->render($template . '.twig', $data);

        return $renderResult;
    }

    public function getTwig()
    {
        $loader = new FilesystemLoader($this->getTemplatePaths());

        $settings = [];

        if (defined('WP_ENV') && WP_ENV === 'production') {
            $settings['cache'] = self::cacheDir();
        }

        if (defined('WP_DEBUG') && WP_DEBUG === true) {
            $settings['debug'] = true;
        }

        $twig = new Environment($loader, $settings);

        $twig->addGlobal('wp', offbeat()->container->make(Wordpress::class));

        if (!empty($this->viewGlobals)) foreach ($this->viewGlobals as $globalNamespace => $globalValue) {
            $twig->addGlobal($globalNamespace, $globalValue);
        }

        $twig->addFilter(new TwigFilter('component', function (Markup $content, string $name, array $args = []) {
            $componentToWrap = container('components')->render($name, $args);
            $contentToEmbed = $content->jsonSerialize();

            return preg_replace('#<innerblocks(\s*[^>]*)>#i', $contentToEmbed, $componentToWrap);
        }, ['is_safe' => ['html']]));

        $twig->addExtension(new OffbeatWpExtension());
        $twig->addExtension(new WordpressExtension());
        
        if (defined('WP_DEBUG') && WP_DEBUG === true) {
            $twig->addExtension(new DebugExtension());
        }

        return $twig;
    }

    public function cacheDir()
    {
        $cacheDirPath = defined('WP_OFFBEAT_TWIG_CACHE_DIR') ? WP_OFFBEAT_TWIG_CACHE_DIR : WP_CONTENT_DIR . '/cache/twig';

        if (!is_dir($cacheDirPath)) {
            mkdir($cacheDirPath);
        }

        return $cacheDirPath;
    }

    public function registerGlobal($namespace, $value)
    {
        $this->viewGlobals[$namespace] = $value;
    }

    public function addTemplatePath($path) {
        array_unshift($this->templatePaths, $path);
    }

    public function getTemplatePaths()
    {
        return $this->templatePaths;
    }

    public function createTemplate($templateCode)
    {
        return $this->getTwig()->createTemplate($templateCode);
    }
}
