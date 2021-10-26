<?php

namespace App\Http\Controllers\Client\ProgramParticipation;

use App\Http\Controllers\Client\ClientBaseController;
use Participant\Application\Service\Client\ClientParticipant\ExecuteParticipantTask;
use Participant\Domain\Model\ClientParticipant;
use Participant\Domain\Model\ITaskExecutableByParticipant;

class ClientParticipantBaseController extends ClientBaseController
{

    public function executeParticipantTask(string $clientParticipantId, ITaskExecutableByParticipant $task): void
    {
        $clientParticipantRepository = $this->em->getRepository(ClientParticipant::class);
        (new ExecuteParticipantTask($clientParticipantRepository))
                ->execute($this->firmId(), $this->clientId(), $clientParticipantId, $task);
    }

}
