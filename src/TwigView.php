<?php
namespace OffbeatWP\Twig;

use Exception;
use OffbeatWP\Contracts\View;
use OffbeatWP\Twig\Extensions\OffbeatWpExtension;
use OffbeatWP\Twig\Extensions\WordpressExtension;
use OffbeatWP\Views\Wordpress;
use RuntimeException;
use Twig\Environment;
use Twig\Extension\DebugExtension;
use Twig\Loader\FilesystemLoader;
use Twig\Markup;
use Twig\TwigFilter;
use Twig_Error_Loader;
use Twig_Error_Syntax;
use Twig_Template;

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

    /**
     * @param string $template
     * @param array $data
     * @return string|null
     */
    public function render($template, $data = [])
    {
        $twig = $this->getTwig();

        if (!is_string($template)) {
            return null;
        }

        try {
            return $twig->render($template . '.twig', $data);
        } catch (Exception $err) {
            return "Error in <b>{$err->getFile()}</b> on line <b>{$err->getLine()}:</b> {$err->getMessage()}";
        }
    }

    /** @return Environment */
    public function getTwig()
    {
        $loader = new FilesystemLoader($this->getTemplatePaths());

        $settings = [];

        if (defined('WP_ENV') && WP_ENV === 'production') {
            $settings['cache'] = $this->cacheDir();
        }

        if (defined('WP_DEBUG') && WP_DEBUG === true) {
            $settings['debug'] = true;
        }

        $twig = new Environment($loader, $settings);

        $twig->addGlobal('wp', offbeat()->container->make(Wordpress::class));

        foreach ($this->viewGlobals as $globalNamespace => $globalValue) {
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

    /** @return string */
    public function cacheDir()
    {
        $cacheDirPath = defined('WP_OFFBEAT_TWIG_CACHE_DIR') && !empty(WP_OFFBEAT_TWIG_CACHE_DIR) ? WP_OFFBEAT_TWIG_CACHE_DIR : WP_CONTENT_DIR . '/cache/twig';

        if (!is_dir($cacheDirPath) && !mkdir($cacheDirPath, 0777, true)) {
            throw new RuntimeException(sprintf('Directory "%s" was not created', $cacheDirPath));
        }

        return $cacheDirPath;
    }

    /** @return void */
    public function registerGlobal($namespace, $value)
    {
        $this->viewGlobals[$namespace] = $value;
    }

    /**
     * @param string $path
     * @return void
     */
    public function addTemplatePath($path)
    {
        array_unshift($this->templatePaths, $path);
    }

    /** @return string[] */
    public function getTemplatePaths()
    {
        return $this->templatePaths;
    }

    /**
     * @param string $templateCode
     * @return Twig_Template
     * @throws Twig_Error_Loader
     * @throws Twig_Error_Syntax
     */
    public function createTemplate($templateCode)
    {
        return $this->getTwig()->createTemplate($templateCode);
    }
}
