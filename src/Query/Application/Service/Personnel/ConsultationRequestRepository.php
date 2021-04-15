<?php

namespace Query\Application\Service\Personnel;

use Query\Infrastructure\QueryFilter\ConsultationRequestFilter;

interface ConsultationRequestRepository
{

    public function allConsultationRequestBelongsToPersonnel(
            string $personnelId, int $page, int $pageSize, ?ConsultationRequestFilter $consultationRequestFilter);
}
