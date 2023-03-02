<?php

namespace ArtARTs36\DocsRetriever\GitHosting;

use ArtARTs36\GitHandler\Support\Uri;
use Github\AuthMethod;
use Github\Client;

class ClientFactory
{
    private const DOMAIN_GITHUB = 'github.com';

    public function create(string $repoUri, string $token): GitHostingClient
    {
        $domain = Uri::host($repoUri);

        if ($domain === self::DOMAIN_GITHUB) {
            $adapter = new GithubClient(
                $gh = new Client(),
            );

            $gh->authenticate($token, AuthMethod::ACCESS_TOKEN);

            return $adapter;
        }

        throw new \Exception(sprintf('Client for domain "%s" not found', $domain));
    }
}
