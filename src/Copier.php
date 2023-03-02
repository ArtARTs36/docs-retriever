<?php

namespace ArtARTs36\DocsRetriever;

use ArtARTs36\DocsRetriever\Config\Config;
use ArtARTs36\FileSystem\Contracts\FileSystem;
use ArtARTs36\GitHandler\AbstractGit;
use ArtARTs36\GitHandler\CachedGit;
use ArtARTs36\GitHandler\Contracts\Handler\GitHandler;

class Copier
{
    public function __construct(
        private readonly FileSystem $fileSystem,
    ) {
        //
    }

    public function copy(Config $config, GitHandler $source, GitHandler $target): void
    {
        if ($source instanceof AbstractGit) {
            $sourceDir = $source->getContext()->getRootDir();
        } else if ($source instanceof CachedGit) {
            $sourceDir = (function () {
                return $this->cachedAndReturn('getContext')->getRootDir();
            })->call($source);
        } else {
            throw new \LogicException('sourceDir not resolved');
        }

        foreach ($config->copy as $copy) {
            $this->checkTargetDir($copy->target->directory);
        }

        foreach ($config->copy as $conf) {
            $sourcePath = $sourceDir . DIRECTORY_SEPARATOR . $conf->source;
            $targetPaths = [];

            foreach (glob($sourcePath) as $filePath) {
                $fileName = pathinfo($filePath, PATHINFO_BASENAME);

                $targetPath = $conf->target->directory . DIRECTORY_SEPARATOR . $fileName;

                copy($filePath, $targetPath);

                $targetPaths[] = $targetPath;
            }

            $target->index()->add($targetPaths);
            $target->commits()->commit($conf->target->commit);
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
