<?php

namespace ArtARTs36\DocsRetriever\GitHosting;

use ArtARTs36\DocsRetriever\Config\MergeRequestConfig;
use ArtARTs36\GitHandler\Contracts\Handler\GitHandler;

class MergeRequestCreator
{
    public function __construct(
        private readonly ClientFactory $clientFactory,
    ) {
       //
    }

    public function create(GitHandler $target, MergeRequestConfig $config, string $token): void
    {
        $repo = $target->urls()->toRepo();

        $this
            ->clientFactory
            ->create($repo->url, $token)
            ->createMergeRequest(new MergeRequest(
                $config->title,
                $config->user,
                $repo->name,
                $target->branches()->current(),
                $config->targetBranch,
                $config->message,
            ));
    }
}
