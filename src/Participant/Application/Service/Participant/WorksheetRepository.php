<?php

namespace Participant\Application\Service\Participant;

use Participant\Domain\Model\Participant\Worksheet;

interface WorksheetRepository
{

    public function nextIdentity(): string;

    public function add(Worksheet $worksheet): void;

    public function update(): void;

    public function aWorksheetOfClientParticipant(
            string $firmId, string $clientId, string $programId, string $worksheetId): Worksheet;

    public function aWorksheetOfUserParticipant(string $userId, string $firmId, string $programId, string $worksheetId): Worksheet;
}
