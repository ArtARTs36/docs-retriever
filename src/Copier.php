<?php

namespace ArtARTs36\DocsRetriever;

use ArtARTs36\DocsRetriever\Config\Config;
use ArtARTs36\FileSystem\Contracts\FileSystem;
use ArtARTs36\GitHandler\Contracts\Handler\GitHandler;
use Psr\Log\LoggerInterface;

class Copier
{
    public function __construct(
        private readonly FileSystem $fileSystem,
        private readonly LoggerInterface $logger,
    ) {
        //
    }

    public function copy(Config $config, GitHandler $source, GitHandler $target): void
    {
        $this->logger->info('[Copier] Started');

        $sourceDir = $source->getContext()->getRootDir();

        foreach ($config->copy as $copy) {
            $this->checkTargetDir($copy->target->directory);
        }

        foreach ($config->copy as $conf) {
            if ($conf->target->commit->author !== null) {
                $target->config()->set('user', 'name', $conf->target->commit->author->name);
                $target->config()->set('user', 'email', $conf->target->commit->author->email);
            }

            $sourcePath = $sourceDir . DIRECTORY_SEPARATOR . $conf->source;
            $targetPaths = [];

            foreach (glob($sourcePath) as $filePath) {
                $fileName = pathinfo($filePath, PATHINFO_BASENAME);

                $targetPath = $conf->target->directory . DIRECTORY_SEPARATOR . $fileName;

                copy($filePath, $targetPath);

                $targetPaths[] = $targetPath;
            }

            $this->logger->info(sprintf('[Copier] Copied files: [%s]', implode(', ', $targetPaths)));

            $target->index()->add($targetPaths);

            $this->logger->info(sprintf('[Copier] Added to index: [%s]', implode(', ', $targetPaths)));

            $target->commits()->commit($conf->target->commit->message, author: $conf->target->commit->author);

            $this->logger->info(sprintf('[Copier] Committed: [%s]', implode(', ', $targetPaths)));
        }
    }

    private function checkTargetDir(string $dir): void
    {
        if ($this->fileSystem->exists($dir)) {
            return;
        }

        $this->fileSystem->createDir($dir);
    }
}
