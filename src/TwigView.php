<?php
namespace OffbeatWP\Twig;

use OffbeatWP\Contracts\View;

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

        return $twig->render($template . '.twig', $data);
    }

    public function getTwig()
    {
        $loader = new \Twig\Loader\FilesystemLoader($this->getTemplatePaths());

        $settings = [];

        if (defined('WP_ENV') && WP_ENV === 'production') {
            $settings['cache'] = self::cacheDir();
        }

        if (defined('WP_DEBUG') && WP_DEBUG === true) {
            $settings['debug'] = true;
        }

        $twig = new \Twig\Environment($loader, $settings);

        $twig->addGlobal('wp', offbeat()->container->make(\OffbeatWP\Views\Wordpress::class));

        if (!empty($this->viewGlobals)) foreach ($this->viewGlobals as $globalNamespace => $globalValue) {
            $twig->addGlobal($globalNamespace, $globalValue);
        }

        $twig->addExtension(new Extensions\OffbeatWpExtension());
        $twig->addExtension(new Extensions\WordpressExtension());

        if (defined('WP_DEBUG') && WP_DEBUG === true) {
            $twig->addExtension(new \Twig\Extension\DebugExtension());
        }

        return $twig;
    }

    public function cacheDir()
    {
        $cacheDirPath = WP_CONTENT_DIR . '/cache/twig';

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
