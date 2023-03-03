<?php

namespace ArtARTs36\DocsRetriever\GitHosting;

use Github\Client;
use Psr\Log\LoggerInterface;

class GithubClient implements GitHostingClient
{
    public function __construct(
        private readonly Client $client,
        private readonly LoggerInterface $logger,
    ) {
        //
    }

    public function createMergeRequest(MergeRequest $request): void
    {
        $this->logger->info(
            sprintf('[GithubClient] creating merge request to %s/%s', $request->repositoryOwner, $request->repositoryName),
        );

        $this->client->pullRequest()->create(
            $request->repositoryOwner,
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
