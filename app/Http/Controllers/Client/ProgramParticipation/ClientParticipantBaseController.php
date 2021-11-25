<?php

namespace App\Http\Controllers\Client\ProgramParticipation;

use App\Http\Controllers\Client\ClientBaseController;
use Participant\Application\Service\Client\ClientParticipant\ExecuteParticipantTask;
use Participant\Domain\Model\ClientParticipant;
use Participant\Domain\Model\ITaskExecutableByParticipant;
use Query\Application\Service\Client\AsProgramParticipant\ExecuteParticipantTask as ExecuteParticipantTask2;
use Query\Application\Service\Client\AsProgramParticipant\ExecuteProgramTask;
use Query\Domain\Model\Firm\Client\ClientParticipant as ClientParticipant2;
use Query\Domain\Model\Firm\Program\ITaskExecutableByParticipant as ITaskExecutableByParticipant2;
use Query\Domain\Model\Firm\Program\ITaskInProgramExecutableByParticipant;

class ClientParticipantBaseController extends ClientBaseController
{

    public function executeParticipantTask(string $programParticipationId, ITaskExecutableByParticipant $task): void
    {
        $clientParticipantRepository = $this->em->getRepository(ClientParticipant::class);
        (new ExecuteParticipantTask($clientParticipantRepository))
                ->execute($this->firmId(), $this->clientId(), $programParticipationId, $task);
    }

    protected function executeQueryTaskInProgram(
            string $clientParticipantId, ITaskInProgramExecutableByParticipant $task): void
    {
        $clientParticipantRepository = $this->em->getRepository(ClientParticipant2::class);
        (new ExecuteProgramTask($clientParticipantRepository))
                ->execute($this->firmId(), $this->clientId(), $clientParticipantId, $task);
    }

    protected function executeQueryParticipantTask(string $programParticipationId, ITaskExecutableByParticipant2 $task): void
    {
        $clientParticipantRepository = $this->em->getRepository(ClientParticipant2::class);
        (new ExecuteParticipantTask2($clientParticipantRepository))
                ->execute($this->firmId(), $this->clientId(), $programParticipationId, $task);
    }

}
