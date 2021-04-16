<?php

namespace Query\Application\Service\Client;

use Query\Infrastructure\QueryFilter\ConsultationSessionFilter;

interface ConsultationSessionRepository
{

    public function allAccessibleConsultationSesssionBelongsToClient(
            string $clientId, int $page, int $pageSize, ?ConsultationSessionFilter $consultationSessionFilter);
}
