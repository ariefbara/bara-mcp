<?php

namespace Firm\Application\Service\Manager;

use Firm\Domain\Model\Firm\BioForm;

interface BioFormRepository
{
    
    public function nextIdentity(): string;
    
    public function add(BioForm $bioForm): void;

    public function ofId(string $bioFormId): BioForm;

    public function update(): void;
}
