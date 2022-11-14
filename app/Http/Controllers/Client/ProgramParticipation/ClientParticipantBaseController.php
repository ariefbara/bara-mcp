<?php

namespace App\Http\Controllers\Client\ProgramParticipation;

use App\Http\Controllers\Client\ClientBaseController;
use Participant\Application\Service\Client\ClientParticipant\ExecuteParticipantTask;
use Participant\Domain\Model\ClientParticipant;
use Participant\Domain\Model\ITaskExecutableByParticipant;
use Query\Application\Service\Client\AsProgramParticipant\ExecuteParticipantQueryTask;
use Query\Application\Service\Client\AsProgramParticipant\ExecuteParticipantTask as ExecuteParticipantTask2;
use Query\Application\Service\Client\AsProgramParticipant\ExecuteProgramTask;
use Query\Domain\Model\Firm\Client\ClientParticipant as ClientParticipant2;
use Query\Domain\Model\Firm\Program\ITaskExecutableByParticipant as ITaskExecutableByParticipant2;
use Query\Domain\Model\Firm\Program\ITaskInProgramExecutableByParticipant;
use Query\Domain\Task\Participant\ParticipantQueryTask;

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

    protected function executeClientParticipantTask(
            string $programParticipationId, \Participant\Domain\Task\Participant\ParticipantTask $task, $payload): void
    {
        $clientParticipantRepository = $this->em->getRepository(ClientParticipant::class);
        (new \Participant\Application\Service\Client\ClientParticipant\ExecuteTask($clientParticipantRepository))
                ->execute($this->firmId(), $this->clientId(), $programParticipationId, $task, $payload);
    }

    protected function executeParticipantQueryTask(string $programParticipationId, ParticipantQueryTask $task, $payload): void
    {
        $clientParticipantRepository = $this->em->getRepository(ClientParticipant2::class);
        (new ExecuteParticipantQueryTask($clientParticipantRepository))
                ->execute($this->firmId(), $this->clientId(), $programParticipationId, $task, $payload);
    }

}
