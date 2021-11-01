<?php

namespace Query\Domain\Task\Dependency\Firm\Program;

use Query\Domain\Model\Firm\Program\ConsultationSetup;

interface ConsultationSetupRepository
{

    public function aConsultationSetupInProgram(string $programId, string $id): ConsultationSetup;

    /**
     * 
     * @param string $programId
     * @param int $page
     * @param int $pageSize
     * @return ConsultationSetup[]
     */
    public function allConsultationSetupsInProgram(string $programId, int $page, int $pageSize);
}
