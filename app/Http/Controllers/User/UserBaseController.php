<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Participant\Application\Service\User\UserParticipant\ExecuteTask;
use Participant\Domain\Model\UserParticipant as UserParticipant2;
use Participant\Domain\Task\Participant\ParticipantTask;
use Query\Application\Service\User\AsProgramParticipant\ExecuteParticipantQueryTask;
use Query\Domain\Model\User;
use Query\Domain\Model\User\UserParticipant;
use Query\Domain\Task\Participant\ParticipantQueryTask;

class UserBaseController extends Controller
{

    protected function userId()
    {
        return $this->request->userId;
    }

    protected function userQueryRepository()
    {
        return $this->em->getRepository(User::class);
    }

    protected function executeUserParticipantTask(string $userParticipantId, ParticipantTask $task, $payload): void
    {
        $userParticipantRepository = $this->em->getRepository(UserParticipant2::class);
        (new ExecuteTask($userParticipantRepository))
                ->execute($this->userId(), $userParticipantId, $task, $payload);
    }

    protected function executeUserParticipantQueryTask(string $userParticipantId, ParticipantQueryTask $task, $payload): void
    {
        $userParticipantRepository = $this->em->getRepository(UserParticipant::class);
        (new ExecuteParticipantQueryTask($userParticipantRepository))
                ->execute($this->userId(), $userParticipantId, $task, $payload);
    }

}
