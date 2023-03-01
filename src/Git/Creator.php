<?php

namespace ArtARTs36\DocsRetriever\Git;

use ArtARTs36\DocsRetriever\Config\Repository;
use ArtARTs36\FileSystem\Contracts\FileSystem;
use ArtARTs36\GitHandler\Contracts\Factory\GitHandlerFactory;
use ArtARTs36\GitHandler\Contracts\Handler\GitHandler;

class Creator
{
    public function __construct(
        private readonly GitHandlerFactory $gitFactory,
        private readonly FileSystem $fileSystem,
    ) {
        //
    }

    public function create(Repository $repository): GitHandler
    {
        if ($repository->isSelfRepository()) {
            return $this->gitFactory->factory(getcwd());
        }

        $dir = $this->createTemporaryDirectory();
        $git = $this->gitFactory->factory($dir);

        $git->setup()->clone($repository->repository);

        return $git;
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
