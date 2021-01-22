<?php

namespace Client\Application\Service\Client;

use Client\Domain\DependencyModel\Firm\BioForm;

interface BioFormRepository
{

    public function ofId(string $bioFormId): BioForm;
}
