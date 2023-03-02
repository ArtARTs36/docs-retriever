<?php

namespace ArtARTs36\DocsRetriever\Config;

abstract class Repository
{
    public const REPOSITORY_SELF = 'self';

    public function __construct(
        public readonly string  $repository,
        public readonly ?string $baseBranch,
    ) {
        //
    }

    public function isSelfRepository(): bool
    {
        return $this->repository === self::REPOSITORY_SELF;
    }
}
