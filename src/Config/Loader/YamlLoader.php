<?php

namespace ArtARTs36\DocsRetriever\Config\Loader;

use ArtARTs36\DocsRetriever\Config\Commit;
use ArtARTs36\DocsRetriever\Config\Config;
use ArtARTs36\DocsRetriever\Config\ConfigCopy;
use ArtARTs36\DocsRetriever\Config\ConfigCopyTarget;
use ArtARTs36\DocsRetriever\Config\ConfigSource;
use ArtARTs36\DocsRetriever\Config\ConfigTarget;
use ArtARTs36\DocsRetriever\Config\Loader;
use ArtARTs36\DocsRetriever\Config\MergeRequestConfig;
use ArtARTs36\GitHandler\Data\Author;
use Symfony\Component\Yaml\Yaml;

class YamlLoader implements Loader
{
    public function load(string $path): Config
    {
        $data = Yaml::parseFile($path);

        return new Config(
            new ConfigSource($data['repositories']['source']['repository'], $data['repositories']['source']['base_branch'] ?? null),
            new ConfigTarget($data['repositories']['target']['repository'], $data['repositories']['target']['base_branch'] ?? null),
            array_map(function (array $copy) {
                return $this->createConfigCopy($copy);
            }, $data['copy']),
            new MergeRequestConfig(
                $data['merge_request']['title'],
                $data['merge_request']['description'],
                $data['merge_request']['target_branch'],
                $data['merge_request']['user'],
            ),
        );
    }

    private function createConfigCopy(array $copy): ConfigCopy
    {
        return new ConfigCopy(
            $copy['source'],
            new ConfigCopyTarget(
                $copy['target']['directory'],
                new Commit(
                    $copy['target']['commit']['message'],
                    isset($copy['target']['commit']['author']) ?
                        new Author($copy['target']['commit']['author']['name'], $copy['target']['commit']['author']['email']) :
                        null,
                ),
            ),
        );
    }
}
