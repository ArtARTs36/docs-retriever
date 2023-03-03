<?php

namespace ArtARTs36\DocsRetriever\GitHosting;

class MergeRequest
{
    public function __construct(
        public readonly string $title,
        public readonly string $repositoryOwner,
        public readonly string $repositoryName,
        public readonly string $sourceBranch,
        public readonly string $targetBranch,
        public readonly string $description,
    ) {
        //
    }
}
