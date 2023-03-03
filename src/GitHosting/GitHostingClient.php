<?php

namespace ArtARTs36\DocsRetriever\GitHosting;

interface GitHostingClient
{
    public function createMergeRequest(MergeRequestInput $request): MergeRequest;
}
