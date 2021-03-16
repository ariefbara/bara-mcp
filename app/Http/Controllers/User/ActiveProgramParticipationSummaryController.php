<?php

namespace App\Http\Controllers\User;

use Query\Application\Service\User\ViewAllActiveProgramParticipationSummary;
use Query\Domain\Model\User;

class ActiveProgramParticipationSummaryController extends UserBaseController
{
    public function showAll()
    {
        $userRepository = $this->em->getRepository(User::class);
        $result = (new ViewAllActiveProgramParticipationSummary($userRepository, $this->buildDataFinder()))
                ->execute($this->userId(), $this->getPage(), $this->getPageSize());
        return $this->listQueryResponse($result);
    }
}
