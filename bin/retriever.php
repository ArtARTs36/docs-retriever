<?php

use ArtARTs36\DocsRetriever\Config;
use ArtARTs36\DocsRetriever\Retriever;
use ArtARTs36\FileSystem\Local\LocalFileSystem;
use ArtARTs36\GitHandler\Factory\CachedGitFactory;
use ArtARTs36\GitHandler\Factory\LocalGitFactory;

require __DIR__ . '/../vendor/autoload.php';

$retriever = new Retriever(new CachedGitFactory(new LocalGitFactory()), new LocalFileSystem());
$loader = new Config\Loader\YamlLoader();

$config = $loader->load(__DIR__ . '/../docs-retriever.yaml');

$retriever->retrieve($config);
