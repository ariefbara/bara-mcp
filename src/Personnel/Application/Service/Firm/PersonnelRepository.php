<?php

namespace Personnel\Application\Service\Firm;

use Personnel\Domain\Model\Firm\Personnel;

interface PersonnelRepository
{

    public function aPersonnelHavingConsultationRequest(string $consultationRequestId): Personnel;

    public function aPersonnelHavingConsultationSession(string $consultationSessionId): Personnel;

    public function ofId(string $firmId, string $personnelId): Personnel;

    public function update(): void;
}
