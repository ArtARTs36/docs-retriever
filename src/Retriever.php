<?php

namespace ArtARTs36\DocsRetriever;

use ArtARTs36\DocsRetriever\Config\Config;
use ArtARTs36\DocsRetriever\Git\Creator;
use ArtARTs36\DocsRetriever\GitHosting\MergeRequestCreator;
use ArtARTs36\GitHandler\Exceptions\BranchAlreadyExists;
use ArtARTs36\ShellCommand\Exceptions\CommandFailed;
use Psr\Log\LoggerInterface;

class Retriever
{
    public function __construct(
        private readonly Creator $creator,
        private readonly Copier $copier,
        private readonly MergeRequestCreator $mergeRequestCreator,
        private readonly LoggerInterface $logger,
    ) {
        //
    }

    public function retrieve(Config $config): void
    {
        $this->logger->info('[Retriever] Started');

        $sourceGit = $this->creator->create($config->source);
        $targetGit = $this->creator->create($config->target);

        $targetBranch = $this->createTemporaryBranch();

        $this->logger->info(sprintf('[Retriever] Selected target branch: "%s"', $targetBranch));

        try {
            $targetGit->branches()->create($targetBranch);
        } catch (BranchAlreadyExists) {
            // suppress
        }

        $targetGit->branches()->switch($targetBranch);

        $this->logger->info(sprintf('[Retriever] Switched to target branch: "%s"', $targetBranch));

        $this->copier->copy($config, $sourceGit, $targetGit);

        $this->logger->info(
            sprintf('Try push new commits to: %s as user[%s]',
                $targetGit->urls()->toRepo()->url,
                implode(', ', $targetGit->config()->getSubject('user')->toArray()),
            ),
        );

        $targetGit->pushes()->pushOnAutoSetUpStream();

        $this->mergeRequestCreator->create($targetGit, $config->mergeRequest, $config->target->token);
    }

    private function createTemporaryBranch(): string
    {
        return 'docs-' . time();
    }
}
