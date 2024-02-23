<?php

namespace App\Dto\Model;

final readonly class EnrichedInstanceData
{
    public function __construct(
        public string $instance,
        public ?string $software,
        public ?string $version,
        public ?bool $openRegistrations,
        public ?bool $captcha,
        public ?bool $emailVerification,
        public ?bool $applications,
    ) {
    }
}
