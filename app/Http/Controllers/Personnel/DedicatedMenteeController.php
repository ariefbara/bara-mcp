<?php

namespace App\Http\Controllers\Personnel;

use Query\Domain\Model\Firm\Program\Participant;
use Query\Domain\Task\Personnel\ViewSummaryOfAllDedidatedMentees;
use Query\Domain\Task\Personnel\ViewSummaryOfAllDedidatedMenteesPayload;
use Resources\Domain\ValueObject\QueryOrder;

class DedicatedMenteeController extends PersonnelBaseController
{

    public function allWithSummary()
    {
        $participantRepository = $this->em->getRepository(Participant::class);
        $queryOrder = new QueryOrder($this->stripTagQueryRequest('order') ?? 'DESC');
        $payload = new ViewSummaryOfAllDedidatedMenteesPayload($this->getPage(), $this->getPageSize(), $queryOrder);
        $task = new ViewSummaryOfAllDedidatedMentees($participantRepository, $payload);
        $this->executePersonnelQueryTask($task);

        return $this->listQueryResponse($payload->result);
    }

}
