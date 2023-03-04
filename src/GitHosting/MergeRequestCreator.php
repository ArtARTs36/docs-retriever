<?php

namespace ArtARTs36\DocsRetriever\GitHosting;

use ArtARTs36\DocsRetriever\Config\MergeRequestConfig;
use ArtARTs36\DocsRetriever\Renderer;
use ArtARTs36\GitHandler\Contracts\Handler\GitHandler;
use Psr\Log\LoggerInterface;

class MergeRequestCreator
{
    public function __construct(
        private readonly ClientFactory $clientFactory,
        private readonly LoggerInterface $logger,
        private readonly Renderer $renderer,
    ) {
       //
    }

    /**
     * @param array<string> $files
     */
    public function create(GitHandler $target, MergeRequestConfig $config, string $token, array $files): void
    {
        $repo = $target->urls()->toRepo();

        $this->logger->info(
            sprintf('[MergeRequestCreator] creating merge request to %s/%s', $repo->user, $repo->name),
        );

        $request = $this
            ->clientFactory
            ->create($repo->url, $token)
            ->createMergeRequest(new MergeRequestInput(
                $config->title,
                $repo->user,
                $repo->name,
                $target->branches()->current(),
                $config->targetBranch,
                $this->renderer->render($config->message, [
                    'files' => $files,
                ]),
            ));

        $this->logger->info(sprintf(
            '[MergeRequestCreator] Merge Request was created with id %s. Url: %s .',
            $request->id,
            $request->url,
        ));
    }
}
