<?php

namespace Client\Application\Service\Client\ProgramParticipation;

use Client\Domain\Model\Client\ProgramParticipation\Worksheet;

interface WorksheetRepository
{

    public function nextIdentity(): string;

    public function add(Worksheet $worksheet): void;

    public function update(): void;

    public function ofId(ProgramParticipationCompositionId $programParticipationCompositionId, string $worksheetId): Worksheet;

    public function all(ProgramParticipationCompositionId $programParticipationCompositionId, int $page, int $pageSize);
}
