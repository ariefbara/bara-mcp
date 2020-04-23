<?php

namespace Bara\Application\Service;

use Bara\Domain\Model\Firm;

interface FirmRepository
{

    public function nextIdentity(): string;

    public function add(Firm $firm): void;
    
    public function update(): void;
    
    public function containRecordOfIdentifier(string $identifier): bool;

    public function ofId(string $firmId): Firm;

    public function all(int $page, int $pageSize);
}
