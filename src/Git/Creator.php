<?php

namespace ArtARTs36\DocsRetriever\Git;

use ArtARTs36\DocsRetriever\Config\Repository;
use ArtARTs36\FileSystem\Contracts\FileSystem;
use ArtARTs36\GitHandler\Contracts\Factory\GitHandlerFactory;
use ArtARTs36\GitHandler\Contracts\Handler\GitHandler;
use ArtARTs36\GitHandler\Exceptions\AlreadySwitched;
use ArtARTs36\ShellCommand\Exceptions\CommandFailed;
use Psr\Log\LoggerInterface;

class Creator
{
    public function __construct(
        private readonly GitHandlerFactory $gitFactory,
        private readonly FileSystem $fileSystem,
        private readonly LoggerInterface $logger,
    ) {
        //
    }

    /**
     * Create GitHandler by config of Repository.
     */
    public function create(Repository $repository): GitHandler
    {
        $this->logger->info(sprintf(
            '[GitCreator] started creating git instance for %s',
            $repository->repository,
        ));

        if ($repository->isSelfRepository()) {
            return $this->createSelfRepository($repository);
        }

        $dir = $this->createTemporaryDirectory();
        $git = $this->gitFactory->factory($dir);

        $this->logger->info(sprintf(
            '[GitCreator] cloning repository %s',
            $repository->repository,
        ));

        try {
            $git->setup()->clone($repository->repository, $repository->baseBranch);
        } catch (CommandFailed $e) {
            $this->logger->error(sprintf(
                '[GitCreator] failed cloning repository %s: %s',
                $repository->repository,
                $e->commandResult->getResult()->append($e->commandResult->getError()),
            ), [
                'command' => $e->commandResult->getCommandLine(),
            ]);

            throw $e;
        }

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
