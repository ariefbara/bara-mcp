<?php

namespace Firm\Application\Service\Manager;

use Firm\Domain\Model\Firm\WorksheetForm;

interface WorksheetFormRepository
{

    public function aWorksheetFormOfId(string $worksheetFormId): WorksheetForm;
}
