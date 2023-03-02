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

    public function create(Repository $repository): GitHandler
    {
        if ($repository->isSelfRepository()) {
            $git = $this->gitFactory->factory(getcwd());

            try {
                $git->branches()->switch($repository->branch);
            } catch (AlreadySwitched) {
                // suppress
            }

            return $git;
        }

        $dir = $this->createTemporaryDirectory(uniqid());
        $git = $this->gitFactory->factory($dir);

        $git->setup()->clone($repository->repository, $repository->branch);

        return $git;
    }

    private function createTemporaryDirectory(string $repoName): string
    {
        $root = $this->fileSystem->getTmpDir();

        $dir = $root . '/temporary-repo-' . $repoName;

        if ($this->fileSystem->exists($dir)) {
            $this->fileSystem->removeDir($dir);
        }

        return $dir;
    }
}
