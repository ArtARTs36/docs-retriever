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

    public function createMergeRequest(MergeRequestInput $request): MergeRequest
    {
        $this->logger->info(
            sprintf('[GithubClient] creating merge request to %s/%s', $request->repositoryOwner, $request->repositoryName),
            [
                'owner' => $request->repositoryOwner,
                'repo' => $request->repositoryOwner,
                'title' => $request->title,
                'base' => $request->targetBranch,
                'head' => $request->sourceBranch,
                'body' => $request->description,
            ],
        );

        $result = $this->client->pullRequest()->create(
            $request->repositoryOwner,
            $request->repositoryName,
            [
                'title' => $request->title,
                'base' => $request->targetBranch,
                'head' => $request->sourceBranch,
                'body' => $request->description,
            ],
        );

        $this->logger->info('[GithubClient] Merge Request was created', [
            'merge_request' => $result,
        ]);

        return new MergeRequest($result['number'] ?? '0', $result['html_url'] ?? '');
    }
}
