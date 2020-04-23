<?php

namespace Firm\Application\Service\Firm;

use Firm\Domain\Model\Firm\WorksheetForm;

interface WorksheetFormRepository
{

    public function nextIdentity(): string;

    public function add(WorksheetForm $worksheetForm): void;

    public function update(): void;

    public function ofId(string $firmId, string $worksheetFormId): WorksheetForm;

    public function all(string $firmId, int $page, int $pageSize);
}
