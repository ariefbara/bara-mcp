<?php

namespace App\Http\Controllers\User\ProgramParticipation;

use App\Http\Controllers\User\UserBaseController;
use Query\Application\Service\User\ProgramParticipation\ViewSummary;
use Query\Domain\Model\User\UserParticipant;

class SummaryController extends UserBaseController
{
    public function show($programParticipationId)
    {
        $programParticipationRepository = $this->em->getRepository(UserParticipant::class);
        $result = (new ViewSummary($programParticipationRepository, $this->buildDataFinder()))
                ->execute($this->userId(), $programParticipationId);
        return $this->singleQueryResponse($result);
    }
}
