<?php

namespace ArtARTs36\DocsRetriever;

use ArtARTs36\DocsRetriever\Config\Config;
use ArtARTs36\DocsRetriever\Git\Creator;
use ArtARTs36\FileSystem\Contracts\FileSystem;
use ArtARTs36\GitHandler\Exceptions\BranchAlreadyExists;

class Retriever
{
    public function __construct(
        private readonly Creator $creator,
        private readonly Copier $copier,
        private readonly FileSystem $fileSystem,
    ) {
        //
    }

    public function retrieve(Config $config): void
    {
        $sourceGit = $this->creator->create($config->source);
        $sourceDir = $sourceGit->getContext()->getRootDir();

        $sourceGit->setup()->clone($config->source->repository);

        $targetGit = $this->creator->create($config->target);

        $targetBranch = $this->createTemporaryBranch();

        try {
            $targetGit->branches()->create($targetBranch);
        } catch (BranchAlreadyExists) {
            // suppress
        }

        $targetGit->branches()->switch($targetBranch);

        foreach ($config->copy as $copy) {
            $this->checkTargetDir($copy->target->directory);
        }

        $this->copier->copy($config, $sourceGit, $targetGit);

        $targetGit->pushes()->push();
    }

    private function checkTargetDir(string $dir): void
    {
        if ($this->fileSystem->exists($dir)) {
            return;
        }

        $this->fileSystem->createDir($dir);
    }

    private function createTemporaryBranch(): string
    {
        return 'docs-' . time();
    }
}
