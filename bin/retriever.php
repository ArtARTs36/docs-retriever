<?php

use ArtARTs36\DocsRetriever\Config;
use ArtARTs36\DocsRetriever\Retriever;
use ArtARTs36\FileSystem\Local\LocalFileSystem;
use ArtARTs36\GitHandler\Factory\CachedGitFactory;
use ArtARTs36\GitHandler\Factory\LocalGitFactory;

require __DIR__ . '/../vendor/autoload.php';

$retriever = new Retriever(new CachedGitFactory(new LocalGitFactory()), new LocalFileSystem());

$config = new Config(
    'https://github.com/ArtARTs36/php-merge-request-linter',
    [
        'docs/*.md',
    ],
    __DIR__ . '/../docs',
);

$retriever->retrieve($config);
