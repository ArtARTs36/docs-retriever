<?php

namespace ArtARTs36\DocsRetriever;

use ArtARTs36\DocsRetriever\Config\Config;
use ArtARTs36\FileSystem\Contracts\FileSystem;
use ArtARTs36\GitHandler\Contracts\Factory\GitHandlerFactory;
use ArtARTs36\GitHandler\Exceptions\BranchAlreadyExists;

class Retriever
{
    public function __construct(
        private readonly GitHandlerFactory $gitFactory,
        private readonly FileSystem $fileSystem,
    ) {
        //
    }

    public function retrieve(Config $config): void
    {
        $sourceDir = $this->createTemporaryDirectory();
        $sourceGit = $this->gitFactory->factory($sourceDir);

        $sourceGit->setup()->clone($config->source->repository);

        $targetGit = $this->gitFactory->factory(__DIR__ . '/../');

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

    private function createTemporaryDirectory(): string
    {
        $root = $this->fileSystem->getTmpDir();

        $dir = $root . '/temporary-repo';

        if ($this->fileSystem->exists($dir)) {
            $this->fileSystem->removeDir($dir);
        }

        return $dir;
    }
}
