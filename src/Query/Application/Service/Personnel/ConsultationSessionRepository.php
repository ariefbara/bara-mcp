<?php

namespace Query\Application\Service\Personnel;

use Query\Infrastructure\QueryFilter\ConsultationSessionFilter;

interface ConsultationSessionRepository
{

    public function allConsultationSessionBelongsToPersonnel(
            string $personnelId, int $page, int $pageSize, ?ConsultationSessionFilter $consultationSessionFilter);
}
