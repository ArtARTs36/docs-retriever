<?php

namespace ArtARTs36\DocsRetriever\Config;

interface Loader
{
    public function load(string $path): Config;
}
