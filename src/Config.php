<?php

namespace ArtARTs36\DocsRetriever;

class Config
{
    /**
     * @param array<string> $sourcePaths - regex paths
     */
    public function __construct(
        public readonly string $sourceRepo,
        public readonly array  $sourcePaths,
        public readonly string $targetDir,
    ) {
        //
    }
}
