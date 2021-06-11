<?php

namespace Firm\Domain\Task;

use Firm\Domain\Model\Firm\BioForm;

interface BioFormRepository
{

    public function ofId(string $bioFormId): BioForm;
}
