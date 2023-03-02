<?php

namespace ArtARTs36\DocsRetriever\Config;

class ConfigTarget extends Repository
{
    public function __construct(
        string $repository,
        ?string $baseBranch,
    ) {
        parent::__construct($repository, $baseBranch);
    }
}
