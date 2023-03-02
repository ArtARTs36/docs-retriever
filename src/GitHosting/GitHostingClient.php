<?php

namespace ArtARTs36\DocsRetriever\GitHosting;

interface GitHostingClient
{
    public function createMergeRequest(MergeRequest $request): void;
}
