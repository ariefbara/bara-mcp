<?php

namespace Firm\Application\Service\Manager;

use Firm\Domain\Model\Firm\ClientCVForm;

interface ClientCVFormRepository
{

    public function ofId(string $clientCVFormId): ClientCVForm;

    public function update(): void;
}
