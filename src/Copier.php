<?php

namespace ArtARTs36\DocsRetriever;

use ArtARTs36\DocsRetriever\Config\Config;
use ArtARTs36\FileSystem\Contracts\FileSystem;
use ArtARTs36\GitHandler\Contracts\Handler\GitHandler;
use ArtARTs36\ShellCommand\Exceptions\CommandFailed;
use Psr\Log\LoggerInterface;

class Copier
{
    public function __construct(
        private readonly FileSystem $fileSystem,
        private readonly LoggerInterface $logger,
    ) {
        //
    }

    public function copy(Config $config, GitHandler $source, GitHandler $target): CopyResult
    {
        $this->logger->info('[Copier] Started');

        $sourceDir = $source->getContext()->getRootDir();
        $targetRoot = $target->getContext()->getRootDir();

        foreach ($config->copy as $copy) {
            $this->checkTargetDir($targetRoot . DIRECTORY_SEPARATOR . $copy->target->directory);
        }

        $modified = false;
        $resultFiles = [];

        foreach ($config->copy as $conf) {
            if ($conf->target->commit->author !== null) {
                $this->logger->info(sprintf(
                    '[Copier] set user: [%s, %s]',
                    $conf->target->commit->author->name,
                    $conf->target->commit->author->email,
                ));

                $target->config()->set('user', 'name', $conf->target->commit->author->name);
                $target->config()->set('user', 'email', $conf->target->commit->author->email);
            }

            $sourcePath = $sourceDir . DIRECTORY_SEPARATOR . $conf->source;
            $targetPaths = [];
            $partHasModified = false;

            foreach (glob($sourcePath) as $filePath) {
                $fileName = pathinfo($filePath, PATHINFO_BASENAME);
                $dirFile = $conf->target->directory . DIRECTORY_SEPARATOR . $fileName;

                $targetPath = $targetRoot . DIRECTORY_SEPARATOR . $dirFile;

                $prevHash = $this->fileSystem->exists($targetPath) ? md5_file($targetPath) : null;

                copy($filePath, $targetPath);

                if ($prevHash !== md5_file($targetPath)) {
                    $resultFiles[] = $dirFile;
                    $partHasModified = true;
                }

                $targetPaths[] = $targetPath;
            }

            $this->logger->info(sprintf('[Copier] Copied files: [%s]', implode(', ', $targetPaths)));

            $target->index()->add($targetPaths);

            if (! $partHasModified) {
                continue;
            }

            $modified = true;

            $this->logger->info(sprintf('[Copier] Added to index: [%s]', implode(', ', $targetPaths)));

            try {
                $target->commits()->commit($conf->target->commit->message, author: $conf->target->commit->author);
            } catch (CommandFailed $e) {
                $this->logger->info(
                    sprintf(
                        '[Copier] Committing failed: %s',
                        $e->commandResult->getError(),
                    ),
                    [
                        'command' => $e->commandResult->getCommandLine(),
                    ],
                );

                throw $e;
            }

            $this->logger->info(sprintf('[Copier] Committed: [%s]', implode(', ', $targetPaths)));
        }

        return new CopyResult($modified, $resultFiles);
    }

    private function checkTargetDir(string $dir): void
    {
        if ($this->fileSystem->exists($dir)) {
            return;
        }

        $this->fileSystem->createDir($dir);
    }
}
