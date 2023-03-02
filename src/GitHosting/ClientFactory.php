<?php

namespace ArtARTs36\DocsRetriever\GitHosting;

use ArtARTs36\GitHandler\Support\Uri;
use Github\Client;

class ClientFactory
{
    private const DOMAIN_GITHUB = 'github.com';

    public function create(string $repoUri): GitHostingClient
    {
        $domain = Uri::host($repoUri);

        if ($domain === self::DOMAIN_GITHUB) {
            return new GithubClient(
                new Client(),
            );
        }

        throw new \Exception(sprintf('Client for domain "%s" not found', $domain));
    }
}
