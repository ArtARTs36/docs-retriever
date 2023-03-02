<?php

namespace ArtARTs36\DocsRetriever\Config;

use ArtARTs36\GitHandler\Data\Author;

class Commit
{
    public function __construct(
        public readonly string $message,
        public readonly ?Author $author
    ) {
        //
    }
}
