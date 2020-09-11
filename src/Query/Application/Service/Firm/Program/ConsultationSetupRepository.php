<?php

namespace Query\Application\Service\Firm\Program;

use Query\Domain\Model\Firm\Program\ConsultationSetup;

interface ConsultationSetupRepository
{

    public function ofId(string $firmId, string $programId, string $consultationSetupId): ConsultationSetup;

    public function all(string $firmId, string $programId, int $page, int $pageSize);
}
