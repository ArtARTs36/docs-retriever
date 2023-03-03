<?php

namespace ArtARTs36\DocsRetriever\GitHosting;

class MergeRequest
{
    public function __construct(
        public readonly string $id,
        public readonly string $url,
    ) {
        //
    }
}
