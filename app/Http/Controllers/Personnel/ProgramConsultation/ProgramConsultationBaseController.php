<?php

namespace App\Http\Controllers\Personnel\ProgramConsultation;

use App\Http\Controllers\Personnel\PersonnelBaseController;
use Personnel\Application\Service\Firm\Personnel\ProgramConsultant\ExecuteTask;
use Personnel\Domain\Model\Firm\Personnel\ITaskExecutableByMentor;
use Personnel\Domain\Model\Firm\Personnel\ProgramConsultant;
use Query\Application\Service\Consultant\ExecuteTaskInProgram;
use Query\Domain\Model\Firm\Program\Consultant;
use Query\Domain\Model\Firm\Program\ITaskInProgramExecutableByConsultant;

class ProgramConsultationBaseController extends PersonnelBaseController
{

    protected function programConsultationRepository()
    {
        return $this->em->getRepository(Consultant::class);
    }

    protected function executeQueryTaskInProgram(string $consultantId, ITaskInProgramExecutableByConsultant $task): void
    {
        $consultantRepository = $this->em->getRepository(Consultant::class);
        (new ExecuteTaskInProgram($consultantRepository))
                ->execute($this->firmId(), $this->personnelId(), $consultantId, $task);
    }

    protected function executeMentorTaskInPersonnelContext(string $mentorId, ITaskExecutableByMentor $task): void
    {
        $mentorRepository = $this->em->getRepository(ProgramConsultant::class);
        (new ExecuteTask($mentorRepository))->execute($this->firmId(), $this->personnelId(), $mentorId, $task);
    }

}
