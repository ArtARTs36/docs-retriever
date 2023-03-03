<?php

namespace ArtARTs36\DocsRetriever\GitHosting;

use ArtARTs36\GitHandler\Support\Uri;
use Github\AuthMethod;
use Github\Client;
use Psr\Log\LoggerInterface;

class ClientFactory
{
    private const DOMAIN_GITHUB = 'github.com';

    public function __construct(
        private readonly LoggerInterface $logger,
    ) {
        //
    }

    public function create(string $repoUri, string $token): GitHostingClient
    {
        $domain = Uri::host($repoUri);

        if ($domain === self::DOMAIN_GITHUB) {
            $adapter = new GithubClient(
                $gh = new Client(),
                $this->logger,
            );

            $gh->authenticate($token, AuthMethod::ACCESS_TOKEN);

            return $adapter;
        }

        throw new \Exception(sprintf('Client for domain "%s" not found', $domain));
    }
}
