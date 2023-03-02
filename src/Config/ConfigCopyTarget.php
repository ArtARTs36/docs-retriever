<?php

namespace ArtARTs36\DocsRetriever\Config;

class ConfigCopyTarget
{
    public function __construct(
        public readonly string $directory,
        public readonly string $commit,
    ) {
        //
    }
}
