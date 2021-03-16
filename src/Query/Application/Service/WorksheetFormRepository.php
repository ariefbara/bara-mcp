<?php

namespace Query\Application\Service;

use Query\Domain\Model\Firm\WorksheetForm;

interface WorksheetFormRepository
{

    public function allGlobalWorksheetForms(int $page, int $pageSize);

    public function aGlobalWorksheetForm(string $worksheetFormId): WorksheetForm;
}
