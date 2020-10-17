<?php

namespace Query\Application\Service\Firm\Personnel\ProgramConsultation;

interface ConsultantActivityLogRepository
{

    public function allActivityLogsBelongsToConsultant(
            string $personnelId, string $programConsultationId, int $page, int $pageSize);
}
