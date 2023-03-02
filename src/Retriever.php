<?php

namespace ArtARTs36\DocsRetriever;

use ArtARTs36\DocsRetriever\Config\Config;
use ArtARTs36\DocsRetriever\Git\Creator;
use ArtARTs36\GitHandler\Exceptions\BranchAlreadyExists;

class Retriever
{
    public function __construct(
        private readonly Creator $creator,
        private readonly Copier $copier,
    ) {
        //
    }

    public function retrieve(Config $config): void
    {
        $sourceGit = $this->creator->create($config->source);
        $targetGit = $this->creator->create($config->target);

        $targetBranch = $this->createTemporaryBranch();

        try {
            $targetGit->branches()->create($targetBranch);
        } catch (BranchAlreadyExists) {
            // suppress
        }

        $targetGit->branches()->switch($targetBranch);

        $this->copier->copy($config, $sourceGit, $targetGit);

        $targetGit->pushes()->push();
    }

    private function createTemporaryBranch(): string
    {
        return 'docs-' . time();
    }
}
