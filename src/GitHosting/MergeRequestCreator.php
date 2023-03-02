<?php

namespace ArtARTs36\DocsRetriever\GitHosting;

use ArtARTs36\GitHandler\Config\MergeRequestConfig;
use ArtARTs36\GitHandler\Contracts\Handler\GitHandler;
use ArtARTs36\GitHandler\Origin\Url\OriginUrlSelector;

class MergeRequestCreator
{
    public function __construct(
        private readonly ClientFactory $clientFactory,
        private readonly OriginUrlSelector $urlSelector,
    ) {
       //
    }

    public function create(GitHandler $target, MergeRequestConfig $config): void
    {
        $url = $target->remotes()->show()->push;

        $repo = $this->urlSelector->select($target)->toRepoFromUrl($url);

        $this
            ->clientFactory
            ->create($url)
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
