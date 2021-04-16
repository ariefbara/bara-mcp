<?php

namespace Query\Application\Service\User;

use Query\Infrastructure\QueryFilter\ConsultationSessionFilter;

interface ConsultationSessionRepository
{

    public function allConsultationSessionBelongsToUser(
            string $userId, int $page, int $pageSize, ?ConsultationSessionFilter $consultationSessionFilter);
}
