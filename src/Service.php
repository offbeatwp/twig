<?php
namespace OffbeatWP\Twig;

use OffbeatWP\Services\AbstractService;
use OffbeatWP\Contracts\View;

class Service extends AbstractService {
    public $bindings = [View::class => TwigView::class];
}