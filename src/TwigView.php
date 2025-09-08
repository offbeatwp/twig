<?php
namespace OffbeatWP\Twig;

use Exception;
use OffbeatWP\Contracts\View;
use OffbeatWP\Twig\Extensions\OffbeatWpExtension;
use OffbeatWP\Twig\Extensions\WordpressExtension;
use OffbeatWP\Twig\Extensions\RenderBlockExtension;
use OffbeatWP\Views\Wordpress;
use RuntimeException;
use Twig\Environment;
use Twig\Extension\DebugExtension;
use Twig\Loader\FilesystemLoader;
use Twig\TemplateWrapper;

class TwigView implements View
{
    /** @var array<string, mixed> */
    protected array $viewGlobals = [];
    /** @var list<string> */
    protected array $templatePaths = [];

    final public function __construct()
    {
        if (is_dir(get_template_directory() . '/resources/views/')) {
            $this->addTemplatePath(get_template_directory() . '/resources/views/');
        }

        if (is_dir(get_template_directory() . '/views/')) {
            $this->addTemplatePath(get_template_directory() . '/views/');
        }
    }

    /** @param mixed[] $data */
    final public function render(string $template, array $data = []): string
    {
        $twig = $this->getTwig();

        try {
            return $twig->render($template . '.twig', $data);
        } catch (Exception $err) {
            return "Error in <b>{$err->getFile()}</b> on line <b>{$err->getLine()}:</b> {$err->getMessage()}";
        }
    }

    public function getTwig(): Environment
    {
        $loader = new FilesystemLoader($this->getTemplatePaths());

        $settings = [];

        if ($this->isProduction() && filter_input(INPUT_GET, 'disableTwigCache') === null) {
            $settings['cache'] = $this->cacheDir();
        }

        if ($this->isDebug()) {
            $settings['debug'] = true;
        }

        $twig = new Environment($loader, $settings);

        $twig->addGlobal('wp', offbeat()->container->make(Wordpress::class));

        foreach ($this->viewGlobals as $globalNamespace => $globalValue) {
            $twig->addGlobal($globalNamespace, $globalValue);
        }

        $twig->addExtension(new OffbeatWpExtension());
        $twig->addExtension(new WordpressExtension());
        $twig->addExtension(new RenderBlockExtension());

        if ($this->isDebug()) {
            $twig->addExtension(new DebugExtension());
        }

        return $twig;
    }

    final public function cacheDir(): string
    {
        $cacheDirPath = defined('WP_OFFBEAT_TWIG_CACHE_DIR') && constant('WP_OFFBEAT_TWIG_CACHE_DIR') ? constant('WP_OFFBEAT_TWIG_CACHE_DIR') : constant('WP_CONTENT_DIR') . '/cache/twig';

        if (!is_string($cacheDirPath)) {
            throw new RuntimeException('Cache directory path is not a string');
        }

        if (!is_dir($cacheDirPath) && !mkdir($cacheDirPath, 0777, true)) {
            throw new RuntimeException(sprintf('Directory "%s" was not created', $cacheDirPath));
        }

        return $cacheDirPath;
    }

    final public function registerGlobal(string $namespace, mixed $value): void
    {
        $this->viewGlobals[$namespace] = $value;
    }

    final public function addTemplatePath(string $path): void
    {
        array_unshift($this->templatePaths, $path);
    }

    /** @return string[] */
    final public function getTemplatePaths(): array
    {
        return $this->templatePaths;
    }

    final public function createTemplate(string $templateCode): TemplateWrapper
    {
        return $this->getTwig()->createTemplate($templateCode);
    }

    private function isProduction(): bool
    {
        return defined('WP_ENV') && constant('WP_ENV') === 'production';
    }

    private function isDebug(): bool
    {
        return defined('WP_DEBUG') && constant('WP_DEBUG');
    }
}
