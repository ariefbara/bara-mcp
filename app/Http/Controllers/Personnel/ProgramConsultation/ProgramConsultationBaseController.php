<?php

namespace App\Http\Controllers\Personnel\ProgramConsultation;

use App\Http\Controllers\Personnel\PersonnelBaseController;
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

}
