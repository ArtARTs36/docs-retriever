<?php

namespace ArtARTs36\DocsRetriever\Config;

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
