<?php

use ArtARTs36\DocsRetriever\Config;
use ArtARTs36\DocsRetriever\Retriever;
use ArtARTs36\FileSystem\Local\LocalFileSystem;
use ArtARTs36\GitHandler\Factory\CachedGitFactory;
use ArtARTs36\GitHandler\Factory\LocalGitFactory;

require __DIR__ . '/../vendor/autoload.php';

$fs = new LocalFileSystem();

$retriever = new Retriever(
    new \ArtARTs36\DocsRetriever\Git\Creator(
        new CachedGitFactory(new LocalGitFactory()),
        $fs,
    ),
    new \ArtARTs36\DocsRetriever\Copier($fs),
    $fs,
);
$loader = new Config\Loader\YamlLoader();

$config = $loader->load(getcwd() . '/docs-retriever.yaml');

$retriever->retrieve($config);
