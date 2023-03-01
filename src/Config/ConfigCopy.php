<?php

namespace ArtARTs36\DocsRetriever\Config;

class ConfigCopy
{
    public function __construct(
        public readonly string $source,
        public readonly ConfigCopyTarget $target
    ) {
        //
    }
}
