<?php

namespace ArtARTs36\GitHandler\Config;

class MergeRequestConfig
{
    public function __construct(
        public readonly string $title,
        public readonly string $message,
        public readonly string $targetBranch,
        public readonly string $user,
    ) {
        //
    }
}
