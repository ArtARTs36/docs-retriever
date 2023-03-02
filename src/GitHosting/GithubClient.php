<?php

namespace ArtARTs36\DocsRetriever\GitHosting;

use Github\Client;

class GithubClient implements GitHostingClient
{
    public function __construct(
        private readonly Client $client,
    ) {
        //
    }

    public function createMergeRequest(MergeRequest $request): void
    {
        $this->client->pullRequest()->create(
            $request->user,
            $request->repositoryName,
            [
                'title' => $request->title,
                'base' => $request->targetBranch,
                'head' => $request->sourceBranch,
                'body' => $request->description,
            ],
        );
    }
}
