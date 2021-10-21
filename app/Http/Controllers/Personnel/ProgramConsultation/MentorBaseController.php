<?php

namespace App\Http\Controllers\Personnel\ProgramConsultation;

use App\Http\Controllers\Personnel\PersonnelBaseController;
use Personnel\Application\Service\Firm\Personnel\ProgramConsultant\ExecuteTask;
use Personnel\Domain\Model\Firm\Personnel\ProgramConsultant;

class MentorBaseController extends PersonnelBaseController
{
    public function executeTaskService(): ExecuteTask
    {
        $mentorRepository = $this->em->getRepository(ProgramConsultant::class);
        return new ExecuteTask($mentorRepository);
    }
}
