<?php

namespace Query\Application\Service\Firm\Personnel;

use Query\Domain\Model\Firm\Program\Consultant;

interface ProgramConsultationRepository
{

    public function aProgramConsultationOfPersonnel(string $firmId, string $personnelId, string $programConsultationId): Consultant;

    public function allProgramConsultationOfPersonnel(string $firmId, string $personnelId, int $page, int $pageSize);
}
