<?php

namespace ArtARTs36\DocsRetriever;

use Twig\Environment;
use Twig\Loader\ArrayLoader;

class Renderer
{
    public function __construct(
        private readonly Environment $twig,
        private readonly ArrayLoader $loader,
    ) {
        //
    }

    public static function create(): self
    {
        $loader = new ArrayLoader();

        return new self(new Environment($loader), $loader);
    }

    public function render(string $template, array $data): string
    {
        $templateName = 'template_' . time();

        $this->loader->setTemplate($templateName, $template);

        return $this->twig->render($templateName, $data);
    }
}
