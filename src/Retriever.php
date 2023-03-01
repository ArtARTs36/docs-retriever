<?php

namespace ArtARTs36\DocsRetriever;

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

        $sourceGit->setup()->clone($config->sourceRepo);

        $targetGit = $this->gitFactory->factory(__DIR__ . '/../');

        $targetBranch = $this->createTemporaryBranch();

        try {
            $targetGit->branches()->create($targetBranch);
        } catch (BranchAlreadyExists) {
            // suppress
        }

        foreach ($config->sourcePaths as $path) {
            foreach (glob($path) as $filePath) {
                $fileName = pathinfo($filePath, PATHINFO_FILENAME);

                $targetPath = $config->targetDir . DIRECTORY_SEPARATOR . $fileName;

                copy($filePath, $targetPath);

                $targetGit->index()->add($targetPath);
            }
        }

        //$targetGit->branches()->switch($targetBranch);

        //$targetGit->pushes()->push();
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
