<?php

namespace Query\Application\Service\Firm;

use Query\Domain\Model\Firm\WorksheetForm;

interface WorksheetFormRepository
{

    public function ofId(string $firmId, string $worksheetFormId): WorksheetForm;

    public function all(string $firmId, int $page, int $pageSize);
}
