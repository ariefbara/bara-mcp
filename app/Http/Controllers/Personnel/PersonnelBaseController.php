<?php

namespace App\Http\Controllers\Personnel;

use App\Http\Controllers\Controller;
use Personnel\Application\Service\Firm\Personnel\ProgramConsultant\ExecuteMentorTask;
use Personnel\Domain\Model\Firm\Personnel\ITaskExecutableByMentor;
use Personnel\Domain\Task\Mentor\MentorTask;
use Query\Application\Auth\Firm\AuthorizeUserIsActiveFirmPersonnel;
use Query\Application\Service\Personnel\ExecutePersonnelTask;
use Query\Application\Service\Personnel\ExecuteQueryTask;
use Query\Domain\Model\Firm\Personnel;
use Query\Domain\Model\Firm\TaskExecutableByPersonnel;
use Query\Domain\Task\Personnel\PersonnelTask;

class PersonnelBaseController extends Controller
{

    protected function firmId()
    {
        return $this->request->firmId;
    }

    protected function personnelId()
    {
        return $this->request->personnelId;
    }

    protected function authorizedRequestFromActivePersonnel()
    {
        $personnelRepository = $this->em->getRepository(Personnel::class);
        $authZ = new AuthorizeUserIsActiveFirmPersonnel($personnelRepository);
        $authZ->execute($this->firmId(), $this->personnelId());
    }

    protected function personnelQueryRepository()
    {
        return $this->em->getRepository(Personnel::class);
    }

    protected function executePersonnelQueryTask(TaskExecutableByPersonnel $task): void
    {
        $personnelRepository = $this->em->getRepository(Personnel::class);
        (new ExecuteQueryTask($personnelRepository))
                ->execute($this->firmId(), $this->personnelId(), $task);
    }

    protected function executeMentorTaskInPersonnelContext(string $mentorId, ITaskExecutableByMentor $task): void
    {
        $mentorRepository = $this->em->getRepository(\Personnel\Domain\Model\Firm\Personnel\ProgramConsultant::class);
        (new \Personnel\Application\Service\Firm\Personnel\ProgramConsultant\ExecuteTask($mentorRepository))
                ->execute($this->firmId(), $this->personnelId(), $mentorId, $task);
    }

    protected function executePersonalQueryTask(PersonnelTask $task, $payload): void
    {
        $personnelRepository = $this->em->getRepository(Personnel::class);
        (new ExecutePersonnelTask($personnelRepository))
                ->execute($this->firmId(), $this->personnelId(), $task, $payload);
    }

    protected function executeExtendedMentorTaskInPersonnelContext(string $mentorId, MentorTask $task, $payload): void
    {
        $mentorRepository = $this->em->getRepository(\Personnel\Domain\Model\Firm\Personnel\ProgramConsultant::class);
        (new ExecuteMentorTask($mentorRepository))
                ->execute($this->firmId(), $this->personnelId(), $mentorId, $task, $payload);
    }

}
