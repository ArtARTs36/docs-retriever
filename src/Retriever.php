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

        foreach ($config->copy as $conf) {
            $sourcePath = $sourceDir . DIRECTORY_SEPARATOR . $conf->source;

            foreach (glob($sourcePath) as $filePath) {
                $fileName = pathinfo($filePath, PATHINFO_BASENAME);

                $targetPath = $conf->target->directory . DIRECTORY_SEPARATOR . $fileName;

                copy($filePath, $targetPath);

                $targetGit->index()->add($targetPath);
            }
        }

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
