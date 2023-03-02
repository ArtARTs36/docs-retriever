<?php

namespace ArtARTs36\DocsRetriever\Config;

use ArtARTs36\GitHandler\Config\MergeRequestConfig;

class Config
{
    /**
     * @param array<ConfigCopy> $copy
     */
    public function __construct(
        public readonly ConfigSource $source,
        public readonly ConfigTarget $target,
        public readonly array $copy,
        public readonly MergeRequestConfig $mergeRequest,
    ) {
        //
    }
}
