<?php

namespace ArtARTs36\DocsRetriever\Git;

use ArtARTs36\DocsRetriever\Config\Repository;
use ArtARTs36\FileSystem\Contracts\FileSystem;
use ArtARTs36\GitHandler\Contracts\Factory\GitHandlerFactory;
use ArtARTs36\GitHandler\Contracts\Handler\GitHandler;
use ArtARTs36\GitHandler\Exceptions\AlreadySwitched;

class Creator
{
    public function __construct(
        private readonly GitHandlerFactory $gitFactory,
        private readonly FileSystem $fileSystem,
    ) {
        //
    }

    /**
     * Create GitHandler by config of Repository.
     */
    public function create(Repository $repository): GitHandler
    {
        if ($repository->isSelfRepository()) {
            return $this->createSelfRepository($repository);
        }

        $dir = $this->createTemporaryDirectory();
        $git = $this->gitFactory->factory($dir);

        $git->setup()->clone($repository->repository, $repository->baseBranch);

        return $git;
    }

    private function createSelfRepository(Repository $repository): GitHandler
    {
        $git = $this->gitFactory->factory(getcwd());

        if ($repository->baseBranch !== null) {
            try {
                $git->branches()->switch($repository->baseBranch);
            } catch (AlreadySwitched) {
                // suppress
            }
        }

        return $git;
    }

    private function createTemporaryDirectory(): string
    {
        $root = $this->fileSystem->getTmpDir();

        $dir = $root . '/temporary-repo-' . uniqid();

        if ($this->fileSystem->exists($dir)) {
            $this->fileSystem->removeDir($dir);
        }

        return $dir;
    }
}
