<?php

namespace ArtARTs36\DocsRetriever\GitHosting;

use ArtARTs36\DocsRetriever\Config\MergeRequestConfig;
use ArtARTs36\GitHandler\Contracts\Handler\GitHandler;
use Psr\Log\LoggerInterface;

class MergeRequestCreator
{
    public function __construct(
        private readonly ClientFactory $clientFactory,
        private readonly LoggerInterface $logger,
    ) {
       //
    }

    public function create(GitHandler $target, MergeRequestConfig $config, string $token): void
    {
        $repo = $target->urls()->toRepo();

        $this->logger->info(
            sprintf('[MergeRequestCreator] creating merge request to %s/%s', $repo->user, $repo->name),
        );

        $request = $this
            ->clientFactory
            ->create($repo->url, $token)
            ->createMergeRequest(new MergeRequestInput(
                $config->title,
                $repo->user,
                $repo->name,
                $target->branches()->current(),
                $config->targetBranch,
                $config->message,
            ));

        $this->logger->info(sprintf(
            '[MergeRequestCreator] Merge Request was created with id %s. Url: %s',
            $request->id,
            $request->url,
        ));
    }
}
