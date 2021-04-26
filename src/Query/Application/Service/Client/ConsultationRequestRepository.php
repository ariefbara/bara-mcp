<?php

namespace Query\Application\Service\Client;

use Query\Infrastructure\QueryFilter\ConsultationRequestFilter;

interface ConsultationRequestRepository
{

    public function allAccessibleConsultationSesssionBelongsToClient(
            string $clientId, int $page, int $pageSize, ?ConsultationRequestFilter $consultationRequestFilter);
}
