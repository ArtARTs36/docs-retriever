<?php

namespace ArtARTs36\DocsRetriever\Config\Loader;

use ArtARTs36\DocsRetriever\Config\Config;
use ArtARTs36\DocsRetriever\Config\ConfigCopy;
use ArtARTs36\DocsRetriever\Config\ConfigCopyTarget;
use ArtARTs36\DocsRetriever\Config\ConfigSource;
use ArtARTs36\DocsRetriever\Config\ConfigTarget;
use ArtARTs36\DocsRetriever\Config\Loader;
use Symfony\Component\Yaml\Yaml;

class YamlLoader implements Loader
{
    public function load(string $path): Config
    {
        $data = Yaml::parseFile($path);

        return new Config(
            new ConfigSource($data['repositories']['source']['repository'], $data['repositories']['source']['base_branch'] ?? null),
            new ConfigTarget($data['repositories']['source']['repository'], $data['repositories']['source']['base_branch'] ?? null),
            array_map(function (array $copy) {
                return new ConfigCopy(
                    $copy['source'],
                    new ConfigCopyTarget(
                        $copy['target']['directory'],
                    ),
                );
            }, $data['copy']),
        );
    }
}
