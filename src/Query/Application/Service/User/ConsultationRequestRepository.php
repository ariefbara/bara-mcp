<?php

namespace Query\Application\Service\User;

use Query\Infrastructure\QueryFilter\ConsultationRequestFilter;

interface ConsultationRequestRepository
{

    public function allConsultationRequestBelongsToUser(
            string $userId, int $page, int $pageSize, ?ConsultationRequestFilter $consultationRequestFilter);
}
