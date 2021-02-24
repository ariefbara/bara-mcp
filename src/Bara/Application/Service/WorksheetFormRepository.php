<?php

namespace Bara\Application\Service;

use Bara\Domain\Model\WorksheetForm;

interface WorksheetFormRepository
{

    public function nextIdentity(): string;

    public function add(WorksheetForm $worksheetForm): void;

    public function ofId(string $worksheetFormId): WorksheetForm;
}
