<?php

namespace Firm\Domain\Task\Dependency\Firm;

use Firm\Domain\Model\Firm\WorksheetForm;

interface WorksheetFormRepository
{

    public function aWorksheetFormOfId(string $worksheetFormId): WorksheetForm;
}
