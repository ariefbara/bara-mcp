<?php

namespace App\Http\Controllers\Personnel\DedicatedMentor;

use App\Http\Controllers\Personnel\PersonnelBaseController;
use Query\Application\Service\Personnel\DedicatedMentor\ExecuteQueryTaskOnDedicatedMentee;
use Query\Domain\Model\Firm\Program\Participant\DedicatedMentor;
use Query\Domain\Model\Firm\Program\Participant\QueryTaskOnDedicatedMenteeExecutableByDedicatedMentor;

class DedicatedMentorBaseController extends PersonnelBaseController
{

    protected function executeQueryTaskOnDedicatedMentee(
            string $dedicatedMentorId, QueryTaskOnDedicatedMenteeExecutableByDedicatedMentor $task, $payload): void
    {
        $dedicatedMentorRepository = $this->em->getRepository(DedicatedMentor::class);
        (new ExecuteQueryTaskOnDedicatedMentee($dedicatedMentorRepository))
                ->execute($this->personnelId(), $dedicatedMentorId, $task, $payload);
    }

}
