<?php

namespace ArtARTs36\DocsRetriever;

class CopyResult
{
    public function __construct(
        public readonly bool $modified,
        public readonly array $files,
    ) {
        //
    }
}
