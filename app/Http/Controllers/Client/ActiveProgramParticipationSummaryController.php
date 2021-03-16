<?php

namespace App\Http\Controllers\Client;

use Query\Application\Service\Firm\Client\ViewAllActiveProgramParticipationSummary;
use Query\Domain\Model\Firm\Client;

class ActiveProgramParticipationSummaryController extends ClientBaseController
{
    public function showAll()
    {
        $clientRepository = $this->em->getRepository(Client::class);
        $result = (new ViewAllActiveProgramParticipationSummary($clientRepository, $this->buildDataFinder()))
                ->execute($this->firmId(), $this->clientId(), $this->getPage(), $this->getPageSize());
        return $this->listQueryResponse($result);
    }
}
