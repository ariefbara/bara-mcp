<?php

namespace App\Http\Controllers\Client\ProgramParticipation;

use App\Http\Controllers\Client\ClientBaseController;
use Query\Application\Service\Firm\Client\ProgramParticipation\ViewSummary;
use Query\Domain\Model\Firm\Client\ClientParticipant;

class SummaryController extends ClientBaseController
{
    public function show($programParticipationId)
    {
        $programParticipationRepository = $this->em->getRepository(ClientParticipant::class);
        $result = (new ViewSummary($programParticipationRepository, $this->buildDataFinder()))
                ->execute($this->firmId(), $this->clientId(), $programParticipationId);
        return $this->singleQueryResponse($result);
    }
}
