<?php

namespace Firm\Application\Service\Firm\Program;

use Firm\Domain\Model\Firm\Program\ClientRegistrant;

interface ClientRegistrantRepository
{
    public function ofId(string $firmId, string $programId, string $clientRegistrantId): ClientRegistrant;
    
    public function update(): void;
}
